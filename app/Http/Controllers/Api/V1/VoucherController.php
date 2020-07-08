<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Voucher;
use Illuminate\Http\Request;
use Util;

/** @group Vouchers
* Datos de los vouchers registrados
*/
class VoucherController extends Controller
{
    /**
    * Registros de Voucher
    * Devuelve el listado con los datos paginados
    * @queryParam user_id Filtro por id de usuario. Example: 122
    * @queryParam loan_payment_id Filtro por id de préstamo. Example: 2
    * @queryParam search Parámetro de búsqueda. Example: PAY00001
    * @queryParam sortBy Vector de ordenamiento. Example: [created_at]
    * @queryParam sortDesc Vector de orden descendente(true) o ascendente(false). Example: [false]
    * @queryParam per_page Número de datos por página. Example: 1
    * @queryParam page Número de página. Example: 1
    * @authenticated
    * @responseFile responses/voucher/index.200.json
    */
    public function index(Request $request)
    {
        $filter = [];
        if ($request->has('user_id')) {
            $filter['user_id'] = $request->user_id;
        }
        if ($request->has('payment_id')) {
            $filter['payable_id'] = $request->loan_payment_id;
            $filter['payable_type'] = "loan_payments";
        }
        return Util::search_sort(new Voucher(), $request, $filter);
    }
    /**
    * Detalle de voucher
    * Devuelve el detalle de un voucher mediante su ID
    * @urlParam voucher required ID de voucher. Example: 1
    * @authenticated
    * @responseFile responses/voucher/show.200.json
    */
    public function show(Voucher $voucher)
    {
        return $voucher;
    }
}
