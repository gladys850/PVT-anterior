<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Ldap;

class LdapController extends Controller
{
    public function index()
    {
        $ldap = new Ldap();
        $unregistered_users = collect($ldap->list_entries())->pluck('uid')->diff(User::get()->pluck('username')->all());
        $items = [];
        foreach($unregistered_users as $user) {
            $item = $ldap->get_entry($user, 'uid');
            if (!is_null($item)) {
                array_push($items, $item);
            }
        }
        return response()->json(collect($items)->sortBy('sn')->values());
    }
}
