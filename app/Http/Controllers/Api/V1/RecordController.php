<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Record;
use Util;

/** @group Usuarios
*/
class RecordController extends Controller
{
    /**
    * Registros de actividad
    * Devuelve el listado con los datos paginados
    * @queryParam user_id Filtro por id de usuario. Example: 122
    * @queryParam search Parámetro de búsqueda. Example: Datos Personales
    * @queryParam sortBy Vector de ordenamiento. Example: [created_at]
    * @queryParam sortDesc Vector de orden descendente(true) o ascendente(false). Example: [false]
    * @queryParam per_page Número de datos por página. Example: 8
    * @queryParam page Número de página. Example: 1
    * @authenticated
    * @response
    * {
    *     "current_page": 1,
    *     "data": [
    *         {
    *             "id": 1,
    *             "user_id": 122,
    *             "record_type_id": 5,
    *             "recordable_id": 56545,
    *             "recordable_type": "affiliates",
    *             "action": "[Datos Personales] El usuario jbarrios inició la captura de huellas. Afiliado: ERTERTET DONATO SILESR HINOJOSA",
    *             "created_at": "2019-10-11 16:54:27",
    *             "updated_at": "2019-10-11 16:54:27"
    *         }, {}
    *     ],
    *     "first_page_url": "http://127.0.0.1/api/v1/record?page=1",
    *     "from": 1,
    *     "last_page": 66,
    *     "last_page_url": "http://127.0.0.1/api/v1/record?page=66",
    *     "next_page_url": "http://127.0.0.1/api/v1/record?page=2",
    *     "path": "http://127.0.0.1/api/v1/record",
    *     "per_page": "8",
    *     "prev_page_url": null,
    *     "to": 8,
    *     "total": 526
    * }
    */
    public function index(Request $request)
    {
        $filter = $request->has('user_id') ? ['user_id' => $request->user_id] : [];
        return Util::search_sort(new Record(), $request, $filter);
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
