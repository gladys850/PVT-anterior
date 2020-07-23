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
            Util::save_record($voucher, 'datos-de-un-pago', 'registró pago : '. $voucher->code);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $e;
        }
        return $payment;
    }

      /**
    * Derivar en lote
    * Deriva o devuelve trámites en un lote mediante sus IDs
    * @bodyParam ids array required Lista de IDs de los trámites a derivar. Example: [1,2,3]
    * @bodyParam role_id integer required ID del rol al cual derivar o devolver. Example: 82
    * @authenticated
    * @responseFile responses/loan/bulk_update_role.200.json
    */
    public function derivation_amortization(Request $request)
    {
        $derived = LoanPayment::whereIn('id', $request->ids)->where('role_id', '!=', $request->role_id)->orderBy('code')->get();
        Util::derivation($request->to_role, $derived);
        $derived->map(function ($item, $key) use ($from_role, $to_role, $flow_message) {
            if (!$from_role) {
                $item['from_role_id'] = $item['role_id'];
                $from_role = Role::find($item['role_id']);
                $flow_message = $this->flow_message($item->modality->procedure_type->id, $from_role, $to_role);
            }
            $item['role_id'] = $to_role->id;
            $item['validated'] = false;
            Util::save_record($item, $flow_message['type'], $flow_message['message']);
        });
        $loans->update(array_merge($request->only('role_id'), ['validated' => false]));
        $derived->transform(function ($loan) {
            return self::append_data($loan, false);
        });
        event(new LoanFlowEvent($derived));
        // PDF template
        $data = [
            'type' => 'loan',
            'header' => [
                'direction' => 'DIRECCIÓN DE ESTRATEGIAS SOCIALES E INVERSIONES',
                'unity' => 'Área de ' . $from_role->display_name,
                'table' => [
                    ['Fecha', Carbon::now()->isoFormat('L')],
                    ['Hora', Carbon::now()->format('H:i')],
                    ['Usuario', Auth::user()->username]
                ]
            ],
            'title' => ($flow_message['type'] == 'derivacion' ? 'DERIVACIÓN' : 'DEVOLUCIÓN') . ' DE TRÁMITES - MODALIDAD ' . $derived->first()->modality->procedure_type->second_name,
            'procedures' => $derived,
            'roles' => [
                'from' => $from_role,
                'to' => $to_role
            ]
        ];
        $file_name = implode('_', ['derivacion', 'prestamos', Str::slug(Carbon::now()->isoFormat('LLL'), '_')]) . '.pdf';
        $view = view()->make('flow.bulk_flow_procedures')->with($data)->render();
        return response()->json([
            'attachment' => Util::pdf_to_base64([$view], $file_name, 'letter', $request->copies ?? 1, false),
            'derived' => $derived
        ]);
    }
}
