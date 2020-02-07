<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

/** @group Configuración
* Parámetros de la aplicación compartidos entre servidor y cliente
*/

class ConfigController extends Controller
{
    /**
    * Datos generales
    * Devuelve los parámetros necesarios para sincronización cliente-servidor
    * @response
    * {
    *     "date": "2020-02-07"
    * }
    */
    public function index()
    {
        return response()->json([
            'date' => Carbon::now()->format('Y-m-d')
        ]);
    }
}