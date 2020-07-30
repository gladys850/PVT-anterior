<?php

namespace App\Http\Controllers\Api\V1;

use App\FinancialEntity;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/** @group Entidades Financieras
* Datos de las entidades financieras registradas en el sistema
*/
class FinancialEntityController extends Controller
{
    /**
    * Lista de entidades financieras
    * Devuelve el listado de las las entidades financieras
    * @authenticated
    * @responseFile responses/financial_entity/index.200.json
    */
    public function index()
    {
        return FinancialEntity::orderBy('name')->get();
    }

    /**
    * Detalle de la entidad finaciera
    * Devuelve el detalle de una entidad financiera mediante su ID
    * @urlParam financial_entity required ID de ciudad. Example: 1
    * @authenticated
    * @responseFile responses/financial_entity/show.200.json
    */
    public function show(FinancialEntity $financialEntity)
    {
        return $financialEntity;
    }

}
