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
use App\Affiliate;
use App\LoanGlobalParameter;
use App\Http\Requests\LoanPaymentsForm;
use App\Http\Requests\VoucherForm;
use App\Events\LoanFlowEvent;
use App\RoleSequence;
use Carbon;
use DB;
use App\Helpers\Util;
use App\Http\Controllers\Api\V1\LoanController;
use App\Exports\ArchivoPrimarioExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Loan;
use App\Role;
use App\ProcedureModality;
use App\PaymentType;
use App\AmortizationType;
use App\Imports\LoanPaymentImport;

/** @group Cobranzas
* Datos de los trámites de Cobranzas
*/
class LoanPaymentController extends Controller
{
    public static function append_data(LoanPayment $loanPayment, $with_state = false)
    {
        if ($with_state) $loanPayment->state = $loanPayment->state;
        return $loanPayment;
    }
    /**
    * Lista de Registro de pagos
    * Devuelve el listado con los datos paginados
    * @queryParam role_id integer Ver préstamos del rol, si es 0 se muestra la lista completa. Example: 73
    * @queryParam state_id integer ID del estado del registro de pago. Example 6
    * @queryParam loan_id integer ID del tramite de préstamo. Example 1
    * @queryParam validated Booleano para filtrar trámites válidados. Example: 1
    * @queryParam procedure_type_id ID para filtrar trámites por tipo de trámite. Example: 30
    * @queryParam trashed Booleano para obtener solo eliminados. Example: 1
    * @queryParam search Parámetro de búsqueda. Example: 2000
    * @queryParam sortBy Vector de ordenamiento. Example: []
    * @queryParam sortDesc Vector de orden descendente(true) o ascendente(false). Example: [true]
    * @queryParam per_page Número de datos por página. Example: 8
    * @queryParam page Número de página. Example: 1
    * @authenticated
    * @responseFile responses/loan_payment/index.200.json
     */
    public function index(Request $request){
        $filters = [];
        $relations = [];
        if (!$request->has('role_id')) {
            if (Auth::user()->can('show-all-loan') || Auth::user()->can('show-all-payment-loan')) {
                $request->role_id = 0;
            } else {
                $role = Auth::user()->roles()->whereHas('module', function($query) {
                    return $query->whereName('prestamos');
                })->orderBy('sequence_number')->orderBy('name')->first();
                if ($role) {
                    $request->role_id = $role->id;
                } else {
                    abort(403);
                }
            }
        } else {
            $request->role_id = (integer)$request->role_id;
            if (($request->role_id == 0 && !Auth::user()->can('show-all-loan') && !Auth::user()->can('show-all-payment-loan')) || ($request->role_id != 0 && !Auth::user()->roles->pluck('id')->contains($request->role_id))) {
                abort(403);
            }
        }
        if ($request->role_id != 0) {
            $filters = [
                'role_id' => $request->role_id
            ];
        }
        if ($request->has('validated')) $filters['validated'] = $request->boolean('validated');
        if ($request->has('procedure_type_id')) {
            $relations['modality'] = [
                'procedure_type_id' => $request->procedure_type_id
            ];
        }
        if ($request->has('state_id')) {
            $relations['state'] = [
                'state_id' => $request->state_id
            ];
        }
        if ($request->has('loan_id')) {
            $relations['loan'] = [
                'loan_id' => $request->loan_id
            ];
        }
        $data = Util::search_sort(new LoanPayment(), $request, $filters, $relations);
        $data->getCollection()->transform(function ($loanPayment) {
            return self::append_data($loanPayment, true);
        });
        return $data;
    }

    /**
    * Detalle de Registro de pago
    * Devuelve el detalle de un registro de pago mediante su ID
    * @urlParam loan_payment required ID de registro de pago. Example: 4
    * @authenticated
    * @responseFile responses/loan_payment/show.200.json
    */
    public function show(LoanPayment $loanPayment)
    {
        if (Auth::user()->can('show-all-loan') || Auth::user()->roles()->whereHas('module', function($query) {
            return $query->whereName('prestamos');
        })->pluck('id')->contains($loanPayment->role_id)) {
            return self::append_data($loanPayment, true);
        } else {
            abort(403);
        }
    }

    /**
    * Editar Registro de pago
    * Edita el Registro de Pago realizado.
    * @urlParam loan_payment required ID del pago realizado. Example: 1
	* @bodyParam description string Texto de descripción. Example: Penalizacion regularizada
    * @bodyParam validated boolean Estado validación del tramite de cobro. Example: true
    * @bodyParam voucher string Comprobante de pago GAR-ABV o D-10/20 o CONT-123. Example: CONT-123
    * @bodyParam amortization_type_id integer ID del tipo de pago. Example: 1
    * @bodyParam affiliate_id integer ID del afiliado. Example: 57950
    * @bodyParam paid_by enum Pago realizado por Titular(T) o Garante(G). Example: T
    * @bodyParam procedure_modality_id integer ID de la modalidad de amortización. Example: 53
    * @authenticated
    * @responseFile responses/loan_payment/update.200.json
    */
    public function update(Request $request, LoanPayment $loanPayment)
    {
        $request->validate([
            'description' => 'nullable|string|min:2',
            'validated' => 'boolean',
            'procedure_modality_id'=> 'exists:procedure_modalities,id',
            'amortization_type_id'=> 'exists:amortization_types,id',
            'affiliate_id'=> 'exists:affiliates,id',
            'voucher'=> 'nullable|string|min:3',
            'paid_by'=> 'string|in:T,G',
        ]);
        if (Auth::user()->can('update-payment-loan')) {
            $update = $request->only('description', 'validated','procedure_modality_id','amortization_type_id','affiliate_id','voucher','paid_by');
        }
        $loanPayment->fill($update);
        $loanPayment->save();
        return  $loanPayment;
    }

    /**
    * Anular Registro de Pago
    * @urlParam loan_payment required ID del pago. Example: 1
    * @authenticated
    * @responseFile responses/loan_payment/destroy.200.json
    */
    public function destroy(LoanPayment $loanPayment)
    {
        $PendientePago = LoanState::whereName('Pendiente de Pago')->first()->id;
        if ($loanPayment->state_id != $PendientePago){
            abort(403, 'El registro a eliminar no está pendiente de pago');
        }else{
            $loanPayment->delete();
            return $loanPayment;
        }
    }

    /** @group Tesoreria
    * Registro de cobro de Préstamo
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
                $voucher = $loanPayment->voucher_treasury()->create($payment->toArray());
                $loanPayment->update(['state_id' => $Pagado]);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return $e;
            }
            return $payment;
        }
        abort(403, 'Registro de Pago no realizado porque no está pendiente de pago');
    }

    /**
    * Derivar en lote
    * Deriva o devuelve trámites en un lote mediante sus IDs
    * @bodyParam ids array required Lista de IDs de los trámites a derivar. Example: [1,2]
    * @bodyParam role_id integer required ID del rol al cual derivar o devolver. Example: 89
    * @authenticated
    * @responseFile responses/loan_payment/bullk_update_role.200.json
    */
    public function bulk_update_role( LoanPaymentsForm $request)
    {
        $PendientePago = LoanState::whereName('Pendiente de Pago')->first()->id;
        $to_role = $request->role_id;
        $loanPayment =  LoanPayment::whereIn('id',$request->ids)->where('role_id', '!=', $request->role_id)->where('state_id', $PendientePago)->orderBy('code');
        $derived = $loanPayment->get();
        $derived = Util::derivation($request, $to_role, $derived, $loanPayment);
        return $derived;
    }

    /**
    * Impresión del Registro de Pago de Préstamo
    * Devuelve un pdf del Pago acorde a un ID de registro de pago
    * @urlParam loan_payment required ID del pago. Example: 1
    * @queryParam copies Número de copias del documento. Example: 2
    * @authenticated
    * @responseFile responses/loan_payment/print_loan_payment.200.json
    */
    public function print_loan_payment(Request $request, LoanPayment $loan_payment, $standalone = true)
    {
        $loan = LoanPayment::findOrFail($loan_payment->id)->loan;
        $procedure_modality = $loan->modality;
        $lenders = []; $is_dead = false;
        foreach ($loan->lenders as $lender) {
            $lenders[] = LoanController::verify_spouse_disbursable($lender)->disbursable;
            if($lender->dead) $is_dead = true;
        }
        $persons = collect([]);
        foreach ($lenders as $lender) {
            $persons->push([
                'id' => $lender->id,
                'full_name' => implode(' ', [$lender->title, $lender->full_name]),
                'identity_card' => $lender->identity_card_ext,
                'position' => 'SOLICITANTE'
            ]);
        }
        $data = [
            'header' => [
                'direction' => 'DIRECCIÓN DE ESTRATEGIAS SOCIALES E INVERSIONES',
                'unity' => 'UNIDAD DE INVERSIÓN EN PRÉSTAMOS',
                'table' => [
                    ['Tipo', $loan->modality->procedure_type->second_name],
                    ['Modalidad', $loan->modality->shortened],
                    ['Usuario', Auth::user()->username]
                ]
            ],
            'title' => 'AMORTIZACIÓN DE CUOTA',
            'loan' => $loan,
            'lenders' => collect($lenders),
            'loan_payment' => $loan_payment,
            'signers' => $persons,
            'is_dead'=> $is_dead
        ];
        $file_name = implode('_', ['pagos', $procedure_modality->shortened, $loan->code]) . '.pdf';
        $view = view()->make('loan.payments.payment_loan')->with($data)->render();
        if ($standalone) return Util::pdf_to_base64([$view], $file_name, 'legal', $request->copies ?? 1);
        return $view;
    }

    /**
    * Reactivar Registro de Pago
    * Reactiva un registro de pago
    * @urlParam loan_payment required ID del registro de pago. Example: 2
    * @authenticated
    * @responseFile responses/loan_payment/reactivate.200.json
    */
    public function reactivate($id)
    {
        $loanPayment = LoanPayment::withTrashed()->where('id', '=', $id)->first();
        if(!$loanPayment){
            abort(403, 'No existe el registro de pago a reactivar');
        }
        $loanPayment->restore();
        return $loanPayment;
    }

    public function deleteCanceledPaymentRecord(){
        $PendientePago = LoanState::whereName('Pendiente de Pago')->first()->id;
        $dias = LoanGlobalParameter::latest()->first()->date_delete_payment;
        $loanPayment = LoanPayment::where('estimated_date','<=',Carbon::now()->subDay($dias))->whereStateId($PendientePago);
        $loanPayment->delete();
    }

    /**
    * Estado del Trámite de Cobro
    * Devuelve el estado del trámite de cobro
    * @urlParam loan_payment required ID de trámite de cobro. Example: 12
    * @authenticated
    * @responseFile responses/loan_payment/get_state.200.json
    */
    public function get_state(LoanPayment $loan_payment)
    {
        if ($loan_payment->state) return $loan_payment->state;
    }

    /**
    * Flujo de trabajo
    * Devuelve la lista de roles anteriores para devolver o posteriores para derivar el trámite
    * @urlParam loan_payment required ID del tramite de cobro. Example: 2
    * @authenticated
    * @responseFile responses/loan_payment/get_flow.200.json
    */
    public function get_flow(LoanPayment $loan_payment)
    {
        return response()->json(RoleSequence::flow($loan_payment->modality->procedure_type->id, $loan_payment->role_id));
    }

    /**
    * Voucher del registro de cobro
    * Devuelve el voucher del registro de cobro realizado por Tesoreria
    * @urlParam loan_payment required ID de trámite de cobro. Example: 1
    * @authenticated
    * @responseFile responses/loan_payment/get_voucher.200.json
    */
    public function get_voucher(LoanPayment $loan_payment)
    {
        if ($loan_payment->voucher_treasury) return $loan_payment->voucher_treasury;
        abort(403, 'No existe el registro de pago');
    }

    public function download(Request $request)
    {
        $File="Pagos";
        $data=array(
            array("Nro", "Codigo", "Cuota Estimada", "Numero de Cuota", "Pago Penal", "Fecha estimada" )
        );
        $pay=LoanPayment::get();
        foreach ($pay as $row){
            array_push($data, array(
                $row->id,
                $row->loan_id,
                $row->estimated_quota,
                $row->quota_number,
                $row->penal_payment,
                $row->estimated_date,
            ));
        }
        $export = new ArchivoPrimarioExport($data);
        return Excel::download($export, $File.'.xlsx');
    }

    /**
    * Registrar trámites de pagos en lote activos y pasivos
	* @bodyParam estimated_date date Fecha para el cálculo del interés. Example: 2020-12-31
    * @bodyParam description string Texto de descripción. Example: Por descuento automatico
    * @bodyParam voucher string Comprobante de pago A-12/20 o CONT-123. Example: A-12/20
    * @authenticated
    * @responseFile responses/loan_payment/command_senasir_save_payment.200.json
    */
    public function command_senasir_save_payment(Request $request)
    {
        $estimated_date = $request->estimated_date? Carbon::parse($request->estimated_date) : Carbon::now()->endOfMonth();
        $loans = Loan::get();
        $payment_type = AmortizationType::get();
        $payment_type_desc = $payment_type->where('name', 'LIKE', 'Descuento automático')->first();
        $description = $request->description? $request->description : 'Por descuento automatico';
        $procedure_modality = ProcedureModality::whereName('Amortización Automática')->first();
        $voucher = $request->voucher? $request->voucher : "AUTOMATICO";
        $paid_by = "T"; $loans_quantity = 0;
        foreach($loans as $loan){
            if($loan->balance != 0){
                foreach($loan->lenders as $lender){
                    $percentage = $lender->pivot->payment_percentage;
                    $percentage_quota = ($percentage)*($loan->next_payment()->estimated_quota)/100;
                    if($lender->affiliate_state->name == 'Servicio' || $lender->affiliate_state->name == 'Disponibilidad' || $lender->affiliate_state->name == 'Jubilado' || $lender->affiliate_state->name == 'Jubilado Invalidez'){
                        $disbursement_date = $loan->disbursement_date;
                        if($disbursement_date->lessThan($estimated_date)){
                            if($disbursement_date->year == $estimated_date->year && $disbursement_date->month == $estimated_date->month){
                                if($disbursement_date->day<LoanGlobalParameter::latest()->first()->offset_interest_day){
                                    LoanPayment::registry_payment($loan, $estimated_date, $description, $procedure_modality, $voucher, $paid_by, $payment_type_desc, $percentage_quota);
                                    $loans_quantity++;
                                }
                            }else{
                                LoanPayment::registry_payment($loan, $estimated_date, $description, $procedure_modality, $voucher, $paid_by, $payment_type_desc, $percentage_quota);
                                $loans_quantity++;
                            }
                        }
                    }
                }
            }
        }
        return response()->json([
            'loans_quantity' => $loans_quantity
        ]);
    }

    /**
    * Importación de Pagos Comando SENASIR
    * Realiza la importación de pagos.
	* @bodyParam file file required Archivo de importación. Example: file.xls
    * @bodyParam state boolean required Tipo importacion Activo(1) o Pasivo(0). Example: 1
    * @bodyParam estimated_date date Fecha estimada para la importacion. Example: 2020-12-31
    * @bodyParam voucher_payment string Comprobante de pago. Example: D-12/20
    * @authenticated
    * @responseFile responses/loan_payment/importation_payments.200.json
    */
    public function importation_command_senasir(Request $request)
    {
        $request->validate([
            'file' => 'required',
            'state'=> 'required|boolean',
            'estimated_date'=> 'nullable|date_format:"Y-m-d"',
            'voucher_payment' => 'nullable|string|min:3'
        ]);

        $file = $request->file('file');
        $json = collect([]);
        $payment_automatic = collect([]);//pagos automaticos confirmados
        $payment_no_automatic = collect([]);//pagos no efectivizados
        $array = Excel::toArray(new LoanPaymentImport, $file);
        $pendientePago = LoanState::whereName('Pendiente de Pago')->first()->id;
        $pagado = LoanState::whereName('Pagado')->first()->id;
        $procedure_modality = ProcedureModality::whereName('Amortización Automática')->first();
        $estimated_date_importation = $request->estimated_date? Carbon::parse($request->estimated_date) : Carbon::now()->endOfMonth();
        
            for($i=1;$i<count($array[0]);$i++){   
                
                $totalLoanAmount = 0; 
                $have_payment=false;

                if($request->state){
                    $ci=(int)$array[0][$i][0];
                    $affiliate = Affiliate::where('identity_card', '=',$ci)->first();
                }else{
                    $matricula= $array[0][$i][0];
                    $affiliate = Affiliate::where('registration', '=',$matricula)->first();
                }
               
                $loanPayments = LoanPayment::where('affiliate_id',$affiliate->id)->get();

                foreach ($loanPayments as $loanPayment){
                    $payment_estimated_date=Carbon::parse($loanPayment->estimated_date);
                    if($loanPayment->procedure_modality_id == $procedure_modality->id && $loanPayment->state_id == $pendientePago){
                        if($payment_estimated_date->year == $estimated_date_importation->year && $payment_estimated_date->month == $estimated_date_importation->month){
                            $totalLoanAmount = $totalLoanAmount + $loanPayment->estimated_quota;
                            $have_payment=true;
                        }
                    }
                }
                
                if ($totalLoanAmount == $array[0][$i][1] && $have_payment){
                    foreach ($loanPayments as $loanPayment){
                        $payment_estimated_date=Carbon::parse($loanPayment->estimated_date);
                        if($loanPayment->procedure_modality_id == $procedure_modality->id && $loanPayment->state_id == $pendientePago){
                            if($payment_estimated_date->year == $estimated_date_importation->year && $payment_estimated_date->month == $estimated_date_importation->month){
                                $loanPayment->state_id = $pagado;
                                if($request->voucher_payment){
                                    $loanPayment->voucher = $request->voucher_payment;
                                }
                                $loanPayment->validated = true;
                                $loanPayment->update();
                                $payment_automatic->push($loanPayment);
                            }
                        }
                    }
                }else{
                    foreach ($loanPayments as $loanPayment){
                        $payment_estimated_date=Carbon::parse($loanPayment->estimated_date);
                        if($loanPayment->procedure_modality_id == $procedure_modality->id && $loanPayment->state_id == $pendientePago){
                            if($payment_estimated_date->year == $estimated_date_importation->year && $payment_estimated_date->month == $estimated_date_importation->month){
                                $payment_no_automatic->push($loanPayment);
                            }
                        }
                    }
                }
            }

            return response()->json([
                    'payments_automatic' => $payment_automatic,
                    'payments_no_automatic' => $payment_no_automatic
                ]);
    }
}
    
