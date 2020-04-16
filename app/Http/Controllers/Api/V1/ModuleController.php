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

    /**
    * Detalle de módulo
    * Devuelve el detalle de un módulo mediante su ID
    * @urlParam module required ID de afiliado. Example: 3
    * @authenticated
    * @response
    * {
    *     "id": 3,
    *     "display_name": "Fondo de Retiro",
    *     "description": "Unidad de Fondo de Retiro",
    *     "name": "fondo-de-retiro",
    *     "shortened": "FR"
    * }
    */
    public function show($id)
    {
        return Module::findOrFail($id);
    }

    /**
    * Roles asociados al módulo
    * Devuelve la lista de roles asociados a un módulo
    * @urlParam module required ID del módulo. Example: 3
    * @authenticated
    * @response
    * [
    *     {
    *         "id": 30,
    *         "module_id": 3,
    *         "display_name": "Regional Santa Cruz",
    *         "action": "Recepcionado",
    *         "created_at": "1958-07-21 00:00:00",
    *         "updated_at": "2020-01-21 15:53:23",
    *         "correlative": "32/2020",
    *         "name": "FR-regional-santa-cruz"
    *     }, {}
    * ]
    */
    public function get_roles($id)
    {
        $module = Module::findOrFail($id);
        return $module->roles;
    }

    /**
    * Tipos de trámite asociados al módulo
    * Devuelve la lista de tipos de trámites asociados a un módulo
    * @urlParam module required ID del módulo. Example: 3
    * @authenticated
    * @response
    * [
    *     {
    *         "id": 2,
    *         "module_id": 3,
    *         "name": "Beneficio de Pago de Fondo de Retiro Policial Solidario",
    *         "created_at": null,
    *         "updated_at": null,
    *         "second_name": "Fondo de Retiro"
    *     }, {}
    * ]
    */
    public function get_procedure_types($id)
    {
        $module = Module::findOrFail($id);
        return $module->procedure_types;
    }

    /**
    * Tipos de observaciones asociados al módulo
    * Devuelve la lista de tipos de observaciones asociados a un módulo
    * @urlParam module required ID del módulo. Example: 6
    * @authenticated
    * @response
    * [
    *     {
    *         "id": 2,
    *         "module_id": 6,
    *         "name": "Suspendido - Préstamo en mora.",
    *         "description": "Amortizable",
    *         "type": "AT",
    *         "shortened": "Préstamos"
    *     }, {}
    * ]
    */
    public function get_observation_types($id)
    {
        $module = Module::findOrFail($id);
        return $module->observation_types;
    }
}
