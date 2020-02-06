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
        $filter = $request->has('name') ? ['name' => $request->name] : [];
        return Util::search_sort(new Module(), $request, $filter);
    }

    public function show($id)
    {
        return Module::findOrFail($id);
    }

    public function get_roles($id)
    {
        $module = Module::findOrFail($id);
        return $module->roles;
    }

    public function get_procedure_types($id)
    {
        $module = Module::findOrFail($id);
        return $module->procedure_types;
    }
}
