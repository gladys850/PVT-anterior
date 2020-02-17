<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Role;

/** @group Roles
* Datos de los roles disponibles en el sistema
*/
class RoleController extends Controller
{
    /**
    * Display a listing of the roles data.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
        return Role::orderBy('name')->get();
    }

    /**
    * Display the specified resource.
    *
    * @param  \App\Role  $role
    * @return \Illuminate\Http\Response
    */
    public function show($id)
    {
        return Role::findOrFail($id);
    }

    public function get_permissions($id) {
        $role = Role::findOrFail($id);
        return $role->permissions()->where('name', '!=', null)->get()->pluck('id');
    }

    public function set_permissions(Request $request, $id) {
        $role = Role::findOrFail($id);
        $role->syncPermissions($request->permissions);
        return $role->permissions()->where('name', '!=', null)->get()->pluck('id');
    }
}
