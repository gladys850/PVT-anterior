<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ProcedureType;
use Util;

/** @group Tipos de procedimientos
* Procedimientos agrupados por tipo de acuerdo a un filtro de modalidad
*/
class ProcedureTypeController extends Controller
{
    /**
    * Lista de procedimientos
    * Devuelve el listado con los datos paginados
    * @queryParam module_id Filtro de ID del módulo. Example: 6
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
    *             "id": 3,
    *             "module_id": 4,
    *             "name": "Pago Cuota Mortuoria",
    *             "created_at": null,
    *             "updated_at": null,
    *             "second_name": "Cuota Mortuoria"
    *         }, {}
    *     ],
    *     "first_page_url": "http://127.0.0.1/api/v1/procedure_type?page=1",
    *     "from": 1,
    *     "last_page": 6,
    *     "last_page_url": "http://127.0.0.1/api/v1/procedure_type?page=6",
    *     "next_page_url": "http://127.0.0.1/api/v1/procedure_type?page=2",
    *     "path": "http://127.0.0.1/api/v1/procedure_type",
    *     "per_page": "2",
    *     "prev_page_url": null,
    *     "to": 2,
    *     "total": 12
    * }
     */
    public function index(Request $request)
    {
        $filter = $request->has('module_id') ? ['module_id' => $request->module_id] : [];
        return Util::search_sort(new ProcedureType(), $request, $filter);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
