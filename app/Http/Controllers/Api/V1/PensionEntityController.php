<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\PensionEntity;

/** @group Entidad de pensiones
* Datos de las entidades de pensiones disponibles en el sistema
*/
class PensionEntityController extends Controller
{
    /**
    * Lista de entidades
    * Devuelve el listado de las entidades de pensiones
    * @response
    * [
    *     {
    *         "id": 1,
    *         "type": "APS",
    *         "name": "AFP FUTURO"
    *     }, {}
    * ]
     */
    public function index()
    {
        return PensionEntity::orderBy('name', 'asc')->get();
    }

    /**
    * Detalle de una entidad
    * Devuelve el detalle de una entidad de pensiones mediante su ID
    * @urlParam id required ID de entidad. Example: 3
    * @response
    * {
    *     "id": 3,
    *     "type": "APS",
    *     "name": "LA VITALICIA"
    * }
    */
    public function show($id)
    {
        return PensionEntity::findOrFail($id);
    }
}
