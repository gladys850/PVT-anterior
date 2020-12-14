<?php

namespace App\Http\Controllers\Api\V1;

use App\AmortizationType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/** @group Tipos de Amortización
*/
class AmortizationTypeController extends Controller
{
    /**
    * Lista de tipos de Amortización
    * Devuelve el listado de los tipos de amortización
    * @authenticated
    * @responseFile responses/amortization_type/index.200.json
    */
    public function index()
    {
        return AmortizationType::orderBy('name')->get();
    }

    /**
    * Detalle de Tipo de Amortización
    * Devuelve el detalle de un un tipo de amortización mediante su ID
    * @urlParam id required ID de tipo de amortización. Example: 1
    * @authenticated
    * @responseFile responses/amortization_type/show.200.json
    */
    public function show(AmortizationType $amortizationType)
    {
        return $amortizationType;
    }

}
