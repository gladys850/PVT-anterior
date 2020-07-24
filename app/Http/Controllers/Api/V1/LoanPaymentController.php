<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Controller;
use App\LoanPayment;
use App\Voucher;
use App\LoanState;
use App\Http\Requests\LoanPaymentForm;
use App\Http\Requests\VoucherForm;
use App\Events\LoanFlowEvent;
use Carbon;
use DB;
use App\Helpers\Util;

class LoanPaymentController extends Controller
{
      /** @group Cobranzas
    * Editar Registro de pago
    * Edita el Registro de Pago realizado.
    * @urlParam loan required ID del prestamo. Example: 2
    * @urlParam loan_payment required ID del pago realizado. Example: 15
	* @bodyParam description string Texto de descripción. Example: Penalizacion regularizada
    * @authenticated
    * @responseFile responses/loan_payment/update_payment.200.json
    */
    public function update_payment(Request $request, LoanPayment $loanPayment)
    {
        DB::beginTransaction();
        try {
            $payment = $loanPayment;
            $payment->description = $request->input('description');
            Util::save_record($loanPayment, 'datos-de-un-registro-pago', Util::concat_action($loanPayment));
            $loanPayment->update($payment->toArray());
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $e;
        }
        return $payment;
    }

       /**
    * Anular Registro de Pago
    * @urlParam loan_payment required ID del pago. Example: 1
    * @authenticated
    * @responseFile responses/loan_payment/destroy_payment.200.json
    */
    public function destroy_payment(LoanPayment $loanPayment)
    {
        DB::beginTransaction();
        try {
            $loanPayment->delete();
            Util::save_record($loanPayment, 'datos-de-un-registro-pago', 'eliminó registro pago: ' . $loanPayment->id);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $e;
        }
        return $loanPayment;
    }

     /** @group Cobranzas
    * Nuevo pago
    * Insertar registro de pago (loan_payment).
    * @urlParam loan_payment required ID del registro de pago. Example: 2
    * @bodyParam payment_type_id integer required ID de tipo de pago. Example: 1
    * @bodyParam voucher_type_id integer required ID de tipo de voucher. Example: 1
    * @bodyParam voucher_number integer número de voucher. Example: 12354121
    * @bodyParam description string Texto de descripción. Example: Penalizacion regularizada
    * @authenticated
    * @responseFile responses/loan_payment/set_voucher.200.json
    */
    public function set_voucher(VoucherForm $request, LoanPayment $loanPayment)
    {
        $Pagado = LoanState::whereName('Pagado')->first()->id;
        $PendientePago = LoanState::whereName('Pendiente de Pago')->first()->id;

        if ($loanPayment->state_id == $PendientePago){
            DB::beginTransaction();
            try {
                $payment = new Voucher;
                $payment->user_id = auth()->id();
                $payment->affiliate_id = $loanPayment->loan->disbursable_id;
                $payment->voucher_type_id = $request->voucher_type_id;
                $payment->total = $loanPayment->estimated_quota;
                $payment->payment_date = $loanPayment->estimated_date;
                $payment->paid_amount = $loanPayment->estimated_quota;
                $payment->payment_type_id = $request->payment_type_id;
                $payment->description = $request->input('description', null);
                $payment->voucher_number = $request->input('voucher_number', null);
                $voucher = $loanPayment->voucher()->create($payment->toArray());
                $loanPayment->update(['state_id' => $Pagado]);
                Util::save_record($voucher, 'datos-de-un-pago', 'registró pago : '. $voucher->code);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return $e;
            }
        }
        abort(403, 'Registro de Pago finalizado');
    }

      /**
    * Derivar en lote
    * Deriva o devuelve trámites en un lote mediante sus IDs
    * @bodyParam ids array required Lista de IDs de los trámites a derivar. Example: [1,2,3]
    * @bodyParam to_role integer required ID del rol al cual derivar o devolver. Example: 82
    * @authenticated
    * @responseFile responses/loan_payment/derivation_amortization.200.json
    */
    public function derivation_amortization(Request $request)
    {
        $PendientePago = LoanState::whereName('Pendiente de Pago')->first()->id;
        $loanPayment =  LoanPayment::whereIn('id',$request->ids)->where('role_id', '!=', $request->role_id)->where('state_id', $PendientePago)->orderBy('code');
        $derived = $loanPayment->get();
        $derived = Util::derivation($request->to_role, $derived, $loanPayment);
        return $derived;
    }
}
