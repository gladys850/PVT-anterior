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
    * Parámetros del servidor
    * Devuelve los parámetros necesarios para sincronización cliente-servidor
    * @responseFile responses/config/invoke.200.json
    */
    public function __invoke()
    {
        return response()->json([
            'date' => Carbon::now()->format('Y-m-d')
        ]);
    }
}