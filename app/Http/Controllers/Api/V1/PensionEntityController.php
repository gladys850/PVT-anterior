<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\PensionEntity;

/** @group Entidad de Pensiones
* Datos de las entidades de pensiones disponibles en el sistema
*/
class PensionEntityController extends Controller
{
    /**
    * Lista de entidades
    * Devuelve el listado de las entidades de pensiones
    * @authenticated
    * @responseFile responses/pension_entity/index.200.json
     */
    public function index()
    {
        return PensionEntity::orderBy('name')->get();
    }

    /**
    * Detalle de una entidad
    * Devuelve el detalle de una entidad de pensiones mediante su ID
    * @urlParam pension_entity required ID de entidad. Example: 3
    * @authenticated
    * @responseFile responses/pension_entity/show.200.json
    */
    public function show(PensionEntity $pension_entity)
    {
        return $pension_entity;
    }
}
