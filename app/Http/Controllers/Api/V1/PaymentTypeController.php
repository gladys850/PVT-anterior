<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\PaymentType;

/** @group Tipos de Desembolso
*/
class PaymentTypeController extends Controller
{
    /**
    * Lista de tipos de desembolso
    * Devuelve el listado de los tipos de desembolso
    * @authenticated
    * @responseFile responses/payment_type/index.200.json
    */
    public function index()
    {
        return PaymentType::orderBy('name')->get();
    }
}
