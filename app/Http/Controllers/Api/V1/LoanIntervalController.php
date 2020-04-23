<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\LoanInterval;

/** @group Intervalos de Préstamos
*/
class LoanIntervalController extends Controller
{
    /**
    * Lista de Intérvalos de Préstamos
    * Devuelve el listado de los intérvalos de montos y plazos por tipo de trámite de préstamo
    * @authenticated
    * @responseFile responses/loan_interval/index.200.json
    */
    public function index()
    {
        return LoanInterval::orderByDesc('maximum_amount')->get();
    }
}