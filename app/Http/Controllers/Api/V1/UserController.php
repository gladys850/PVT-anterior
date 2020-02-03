<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserForm;
use App\User;
use App\Role;
use Ldap;
use Util;

class UserController extends Controller
{
    private $db_users = ['admin', 'asistente'];

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Request $request)
    {
        $filter = $request->has('active') ? ['active' => Util::get_bool($request->active)] : [];
        $data = Util::search_sort(new User(), $request, $filter);
        return $data;
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(UserForm $request)
    {
        if (env("LDAP_AUTHENTICATION")) {
            $ldap = new Ldap();
            if (is_null($ldap->get_entry($request->username, 'uid'))) {
                abort(404);
            }
        }
        return User::create($request->all());
    }

    /**
    * Display the specified resource.
    *
    * @param  \App\User  $user
    * @return \Illuminate\Http\Response
    */
    public function show($id)
    {
        $user = User::findOrFail($id);
        if (Auth::user()->id == $id || Auth::user()->can('show-user')) {
            return $user;
        } else {
            abort(401);
        }
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \App\User  $user
    * @return \Illuminate\Http\Response
    */
    public function update(UserForm $request, $id)
    {
        $user = User::findOrFail($id);
        if ($request->has('password') && !env("LDAP_AUTHENTICATION")) {
            if ($request->has('old_password')) {
                if (!(Auth::user()->id == $id && Hash::check($request->input('old_password'), $user->password))) {
                    return response()->json([
                        'message' => 'Invalid',
                        'errors' => [
                            'type' => ['Contraseña anterior incorrecta'],
                        ],
                    ], 409);
                }
            } else {
                unset($request['password']);
            }
        }
        $user->fill($request->all());
        $user->save();
        return $user;
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\User  $user
    * @return \Illuminate\Http\Response
    */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $has_actions = $user->has_actions();
        if ($user->has_actions() || in_array($user->username, $this->db_users)) {
            return response()->json([
				'message' => 'Forbidden',
				'errors' => [
					'type' => ['El usuario aún tiene acciones registradas'],
				]
			], 403);
        } else {
            $user->records()->delete();
            $user->delete();
            return $user;
        }
    }

    public function get_permissions($id)
    {
        $user = User::findOrFail($id);
        return $user->allPermissions()->pluck('id');
    }

    public function get_roles($id)
    {
        $user = User::findOrFail($id);
        return $user->roles()->pluck('id');
    }

    public function set_roles(Request $request, $id)
    {
        foreach ($request->roles as $role) {
            Role::findOrFail($role);
        }
        $user = User::findOrFail($id);
        $user->syncRoles($request->roles);
        return $user->roles()->pluck('id');
    }

    public function unregistered_users()
    {
        $ldap = new Ldap();
        $unregistered_users = collect($ldap->get_entries())->pluck('uid')->diff(User::get()->pluck('username')->all());
        $items = [];
        foreach($unregistered_users as $user) {
            array_push($items, $ldap->get_entry($user, 'uid'));
        }
        return response()->json(collect($items)->sortBy('sn')->values());
    }

    public function synchronize_users()
    {
        $ldap = new Ldap();
        $discharged_users = collect(User::where(function($query) {
            foreach($this->db_users as $user) {
                $query->where('username', '!=', $user);
            }
        })->whereActive(true)->get()->pluck('username')->all())->diff(collect($ldap->get_entries())->pluck('uid'));
        $items = [];
        foreach($discharged_users as $user) {
            array_push($items, User::whereUsername($user)->first());
        }
        return response()->json(collect($items)->sortBy('last_name')->values());
    }
}
