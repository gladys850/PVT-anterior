<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\VoucherType;

/** @group Tipos de Voucher
*/
class VoucherTypeController extends Controller
{
    /**
    * Lista de tipos de voucher
    * Devuelve el listado de los tipos de voucher
    * @authenticated
    * @responseFile responses/voucher_type/index.200.json
    */
    public function index()
    {
        return VoucherType::orderBy('name')->get();
    }
}
