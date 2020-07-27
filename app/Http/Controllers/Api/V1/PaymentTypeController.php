<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\PaymentType;

/** @group Tipos de Pago
*/
class PaymentTypeController extends Controller
{
    /**
    * Lista de tipos de Pago
    * Devuelve el listado de los tipos de pago
    * @authenticated
    * @responseFile responses/payment_type/index.200.json
    */
    public function index()
    {
        return PaymentType::orderBy('id')->where('name', 'NOT LIKE', '%Descuento automático%')->get();
    }

    /**
    * Detalle de Tipo de Pago
    * Devuelve el detalle de un un tipo de pago mediante su ID
    * @urlParam id required ID de tipo de pago. Example: 3
    * @authenticated
    * @responseFile responses/payment_type/show.200.json
    */
    public function show(PaymentType $payment_type)
    {
        return $payment_type;
    }
}
