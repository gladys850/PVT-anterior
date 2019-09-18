<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Module;

class ModuleController extends Controller
{
    public function index()
    {
        return Module::orderBy('name')->get();
    }

    public function get_roles($id)
    {
        $module = Module::findOrFail($id);
        return $module->roles;
    }
}
