<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Module;
use Util;

/** @group Módulos
* Módulos disponibles en el sistema
*/
class ModuleController extends Controller
{
    /**
    * Lista de módulos
    * Devuelve el listado con los datos paginados
    * @queryParam name Filtro por nombre. Example: prestamos
    * @queryParam sortBy Vector de ordenamiento. Example: [name]
    * @queryParam sortDesc Vector de orden descendente(true) o ascendente(false). Example: [false]
    * @queryParam per_page Número de datos por página. Example: 10
    * @queryParam page Número de página. Example: 1
    * @authenticated
    * @response
    * {
    *     "current_page": 1,
    *     "data": [
    *         {
    *             "id": 6,
    *             "display_name": "Préstamos",
    *             "description": "Unidad de Préstamos",
    *             "name": "prestamos",
    *             "shortened": "PRE"
    *         }, {}
    *     ],
    *     "first_page_url": "http://127.0.0.1/api/v1/module?page=1",
    *     "from": 1,
    *     "last_page": 1,
    *     "last_page_url": "http://127.0.0.1/api/v1/module?page=1",
    *     "next_page_url": null,
    *     "path": "http://127.0.0.1/api/v1/module",
    *     "per_page": "10",
    *     "prev_page_url": null,
    *     "to": 1,
    *     "total": 1
    * }
    */
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
