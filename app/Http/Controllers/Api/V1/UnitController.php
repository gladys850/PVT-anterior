<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Unit;
use Illuminate\Http\Request;

/** @group Unidad Policial
* Datos de las unidades policiales de destino
*/
class UnitController extends Controller
{
    /**
    * Lista de unidades
    * Devuelve el listado de las unidades policiales
    * @authenticated
    * @responseFile responses/unit/index.200.json
    */
    public function index()
    {
        return Unit::get();
    }

    /**
    * Detalle de unidad
    * Devuelve el detalle de una unidad policial mediante su ID
    * @urlParam unit required ID de unidad. Example: 1
    * @authenticated
    * @responseFile responses/unit/show.200.json
    */
    public function show(Unit $unit)
    {
        return $unit;
    }
}
