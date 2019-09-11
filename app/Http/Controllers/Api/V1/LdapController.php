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
        $users=User::get()->pluck('username')->all();
        $ldap_users = new Ldap();
        $ldap_users = $ldap_users->list_entries();
        $ldap_users = collect($ldap_users)->sortBy('uid');
        $unregistered_users = $ldap_users->pluck('uid')->diff($users);
        $unregistered_users->each(function ($item) use ($ldap_users) {
            $ldap_users->filter(function($item) {
                return $item->uid != $item;
            });
        });
        return response()->json($ldap_users->values());
    }
}
