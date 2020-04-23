<?php

namespace App\Http\Controllers\Api\V1;

use App\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/** @group Permisos
* Datos de los permisos disponibles en el sistema
*/
class PermissionController extends Controller
{
    /**
    * Lista de permisos
    * Devuelve el listado de los permisos disponibles en el sistema
    * @authenticated
    * @responseFile responses/permission/index.200.json
    */
    public function index()
    {
        return Permission::where('name', '!=', null)->orderBy('name')->get();
    }
}