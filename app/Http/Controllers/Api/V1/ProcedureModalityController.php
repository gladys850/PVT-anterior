<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ProcedureModality;
use Util;

/** @group Modalidad de procedimientos
* Procedimientos agrupados por modalidad de acuerdo a filtro de tipo de procedimiento
*/
class ProcedureModalityController extends Controller
{
    /**
    * Lista de procedimientos
    * Devuelve el listado con los datos paginados
    * @queryParam procedure_type_id Filtro de ID del tipo de procedimiento. Example: 9
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
    *             "id": 32,
    *             "procedure_type_id": 9,
    *             "name": "Anticipo sector activo",
    *             "shortened": "ANT-SA",
    *             "is_valid": true
    *         }, {}
    *     ],
    *     "first_page_url": "http://127.0.0.1/api/v1/procedure_modality?page=1",
    *     "from": 1,
    *     "last_page": 1,
    *     "last_page_url": "http://127.0.0.1/api/v1/procedure_modality?page=1",
    *     "next_page_url": null,
    *     "path": "http://127.0.0.1/api/v1/procedure_modality",
    *     "per_page": 10,
    *     "prev_page_url": null,
    *     "to": 2,
    *     "total": 2
    * }
     */
    public function index(Request $request)
    {
        $filter = $request->has('procedure_type_id') ? ['procedure_type_id' => $request->procedure_type_id] : [];
        $data = Util::search_sort(new ProcedureModality(), $request, $filter);
        return $data;
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
        return ProcedureModality::findOrFail($id);
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

    /**
    * Requisitos para una modalidad de préstamo
    * Devuelve los documentos requeridos para cada modalidad
    * @urlParam id ID de la modalidad. Example: 9
    * @authenticated
    * @response
    * {
    *     "required": [
    *         [
    *             {
    *                 "id": 15,
    *                 "name": "Certificado de descendencia del titular fallecido en original y actualizado emitido por el SERECI.",
    *                 "created_at": "2019-04-02 21:57:15",
    *                 "updated_at": null,
    *                 "expire_date": null
    *             }
    *         ],
    *         [
    *             {
    *                 "id": 16,
    *                 "name": "Declaratoria de herederos en original.",
    *                 "created_at": "2019-04-03 13:18:24",
    *                 "updated_at": null,
    *                 "expire_date": null
    *             }, {}
    *         ]
    *     ],
    *     "optional": [
    *         {
    *             "id": 1,
    *             "name": "Comprobante de depósito bancario de Bs.- 25,00 por concepto de adquisición de folder y formularios, en la cuenta fiscal de la MUSERPOL.",
    *             "created_at": "2019-04-05 20:19:32",
    *             "updated_at": null,
    *             "expire_date": null
    *         }, {}
    *     ]
    * }
    */
    public function get_requirements($id) {
        $modality = ProcedureModality::findOrFail($id);
        return $modality->requirements_list;
    }
}