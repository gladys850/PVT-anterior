<?php

namespace App\Http\Controllers\Api\V1;

use App\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Util;

/** @group Permisos
* Datos de los permisos disponibles en el sistema
*/
class PermissionController extends Controller
{
    /**
    * Lista de permisos
    * Devuelve el listado de los permisos disponibles en el sistema
    * @queryParam search Parámetro de búsqueda. Example: afiliado
    * @authenticated
    * @responseFile responses/permission/index.200.json
    */
    public function index(Request $request)
    {
        $filter = ['name' => ['!=', null]];
        return Util::search_sort(new Permission(), $request, $filter);
    }
}