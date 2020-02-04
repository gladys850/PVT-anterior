<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Module;
use Util;

class ModuleController extends Controller
{
    public function index(Request $request)
    {
        return Util::search_sort(new Module(), $request);
    }

    public function get_roles($id)
    {
        $module = Module::findOrFail($id);
        return $module->roles;
    }
}
