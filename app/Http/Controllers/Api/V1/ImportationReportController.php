<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ArchivoPrimarioExport;
use Carbon;
use Util;
use Illuminate\Support\Facades\Log;
use App\LoanPaymentPeriod;
use App\Loan;
use App\ProcedureModality;
use App\LoanState;


/** @group Report.Solicitud y Amortizacion por periodos
* Reportes de amortizaciones por periodos Comando  o Senasir
*/

class ImportationReportController extends Controller
{
    /**
    * Reports Amortizaciones por periodos
    * @queryParam origin required Tipo de importacion C (Comando general) o S (Senasir). Example: C
    * @queryParam period required id_del periodo . Example: 1
    * @queryParam category_name Nombre de la categoria de pagos "Refinancimiento" o  "Regular" . Example: Refinancimiento
    * @queryParam state_name  Nombre del estado de los pagos "Pagado" o "Pendiente por confirmar". Example: Pendiente por confirmar
    * @authenticated
    * @responseFile responses/reports_amortizations/amortization_for_periods.200.json
    */
    public function report_amortization_importation_payments(Request $request)
    {
        $request->validate([
        'origin'=>'required|string|in:C,S',
        'period'=>'required|exists:loan_payment_periods,id',
        'category_name'=>'string|in:Refinanciamiento,Regular',
        'state_name'=>'string|in:Pagado,Pendiente por confirmar'
    ]);

        // aumenta el tiempo máximo de ejecución de este script a 150 min:
        ini_set('max_execution_time', 9000);
        // aumentar el tamaño de memoria permitido de este script:
        ini_set('memory_limit', '960M');

        //origin y period
        if ($request->origin == 'C') {
            $procedure_modality_id = ProcedureModality::whereShortened('DES-COMANDO')->first()->id;
        }
        if ($request->origin == 'S') {
            $procedure_modality_id = ProcedureModality::whereShortened('DES-SENASIR')->first()->id;
        }

        $period = LoanPaymentPeriod::whereId($request->period)->first();
        $estimated_date = Carbon::create($period->year, $period->month, 1);
        $estimated_date = Carbon::parse($estimated_date)->endOfMonth()->format('Y-m-d');

        $order_loan = 'Desc';

        $conditions = [];
        //filtros
        $initial_date = $estimated_date? $estimated_date:'';
        $final_date = $estimated_date;
        $category_name = $request->category_name? $request->category_name:'Regular' ;
        $state_pagado= $request->state_name? $request->state_name:'Pagado';

        $final_date = $estimated_date;

        if ($initial_date != '') {
            array_push($conditions, array('loan_payments.estimated_date', '=', "%{$initial_date}%"));
        }
        if ($final_date != '') {
            array_push($conditions, array('loan_payments.estimated_date', '=', "%{$final_date}%"));
        }
        array_push($conditions, array('loan_payment_states.name', 'ilike', "%{$state_pagado}%"));
        array_push($conditions, array('loan_payment_categories.name', 'ilike', "%{$category_name}%"));
        //prodedure
        array_push($conditions, array('loan_payments.procedure_modality_id', '=', "{$procedure_modality_id}"));

        $list_loan = DB::table('loan_payments')
                ->join('procedure_modalities', 'loan_payments.procedure_modality_id', '=', 'procedure_modalities.id')
                ->join('procedure_types', 'procedure_modalities.procedure_type_id', '=', 'procedure_types.id')
                ->join('loan_payment_states', 'loan_payments.state_id', '=', 'loan_payment_states.id')
                ->join('affiliates', 'loan_payments.affiliate_id', '=', 'affiliates.id')
                ->leftjoin('spouses', 'affiliates.id', '=', 'spouses.affiliate_id')
                ->join('affiliate_states', 'affiliates.affiliate_state_id', '=', 'affiliate_states.id')
                ->join('affiliate_state_types', 'affiliate_states.affiliate_state_type_id', '=', 'affiliate_state_types.id')
                ->leftjoin('pension_entities', 'affiliates.pension_entity_id', '=', 'pension_entities.id')
                ->join('loans', 'loan_payments.loan_id', '=', 'loans.id')
                ->join('loan_payment_categories', 'loan_payments.categorie_id', '=', 'loan_payment_categories.id')
                ->leftJoin('vouchers', 'loan_payments.id', '=', 'vouchers.payable_id')
                ->leftJoin('voucher_types', 'vouchers.voucher_type_id', '=', 'voucher_types.id')
                ->whereNull('loan_payments.deleted_at')
                ->where($conditions)
                ->select(
                    'loans.id as id_loan',
                    'loans.code as code_loan',
                    'loans.disbursement_date as disbursement_date_loan',
                    'affiliate_state_types.name as state_type_affiliate',
                    'affiliate_states.name as state_affiliate',
                    'affiliates.id as id_affiliate',
                    'affiliates.identity_card as identity_card_affiliate',
                    'affiliates.registration as registration_affiliate',
                    'affiliates.last_name as last_name_affiliate',
                    'affiliates.mothers_last_name as mothers_last_name_affiliate',
                    'affiliates.first_name as first_name_affiliate',
                    'affiliates.second_name as second_name_affiliate',
                    'affiliates.surname_husband as surname_husband_affiliate',
                    'pension_entities.name as pension_entity_affiliate',
                    'loan_payments.code as code_payment',
                    'loan_payments.estimated_date as estimated_date_payment',
                    'loan_payments.loan_payment_date as loan_payment_date',
                    'loan_payments.estimated_quota as estimated_quota_payment',
                    'loan_payments.voucher as voucher_payment',
                    'procedure_modalities.name as sub_modality_payment',
                    'procedure_modalities.shortened as sub_modality_shortened_payment',
                    'procedure_types.name as modality_payment',
                    'loan_payment_states.name as state_payment',
                    'voucher_types.name as name_voucher_type',
                    'spouses.registration as registration_spouse',
                    'loan_payments.paid_by as payment_by',
                    'loan_payments.capital_payment as capital_payment',
                    'loan_payments.interest_payment as interest_payment',
                    'loan_payments.penal_payment as penal_payment',
                    'loan_payments.interest_remaining as interest_current_pending',
                    'loan_payments.penal_remaining as interest_penal_pending',
                    'loan_payments.estimated_quota as estimated_quota_payment',
                    'loan_payments.previous_balance as previous_balance',
                    DB::raw("(loan_payments.previous_balance - loan_payments.capital_payment) as current_balance"),
                    'loan_payments.id as id_payment',
                    'loan_payment_categories.name as category_name'
                )
                ->orderBy('loan_payments.code', $order_loan)
                ->get();

        foreach ($list_loan as $loan) {
            $padron = Loan::where('id', $loan->id_loan)->first();
            $loan->modality=$padron->modality->procedure_type->second_name;
            $loan->sub_modality=$padron->modality->shortened;
        }

        $File="Amortizaciones_".$request->origin.'_'.$period->month.'_'.$period->year;
        $data=array(
                   array("NRO DE PRÉSTAMO", "FECHA DE DESEMBOLSO", "TIPO","FECHA DE PAGO","FECHA DE TRANSACCIÓN","MODALIDAD","SUB MODALIDAD",
                   "MATRICULA AFILIADO","MATRICULA CÓNYUGUE", "CI", "APELLIDO PATERNO","APELLIDO CASADA","APELLIDO MATERNO",
                   "PRIMER NOMBRE","SEGUNDO NOMBRE","CAPITAL","INTERÉS CORRIENTE","INTERÉS PENAL","INTERÉS CORRIENTE PENDIENTE",
                   "INTERÉS PENAL PENDIENTE","TOTAL PAGADO","SALDO ANTERIOR", "SALDO ACTUAL","PAGADO POR","TIPO DESCUENTO","CBTE","NRO DE COBRO","ESTADO DEL COBRO","CATEGORIA DEL COBRO")
               );
        foreach ($list_loan as $row) {
            array_push($data, array(
                       $row->code_loan,//nro de prestamo
                       Carbon::parse($row->disbursement_date_loan)->format('d/m/Y H:i:s'),//fecha de desembolso
                       $row->state_type_affiliate,//tipo (pasivo o activo)

                       Carbon::parse($row->estimated_date_payment)->format('d/m/Y'),//fecha de pago
                       Carbon::parse($row->loan_payment_date)->format('d/m/Y H:i:s'),//fecha de transacción
                       $row->modality, //MOdalidad
                       $row->sub_modality, //Sub modalidad

                       $row->registration_affiliate,//matricula afiliado
                       $row->registration_spouse,//matricula esposa
                       $row->identity_card_affiliate,//CI

                       $row->last_name_affiliate,//ap paterno
                       $row->surname_husband_affiliate,//ap casada
                       $row->mothers_last_name_affiliate,//ap materno
                       $row->first_name_affiliate,//primer nombre
                       $row->second_name_affiliate,//segundo nombre
                       Util::money_format($row->capital_payment),//capital .. pagado
                       Util::money_format($row->interest_payment), //interes corriente
                       Util::money_format($row->penal_payment),// interes penal
                       Util::money_format($row->interest_current_pending),//interes corriente pendiente
                       Util::money_format($row->interest_penal_pending),//interes penal pendiente
                       Util::money_format($row->estimated_quota_payment),//total pagado
                       Util::money_format($row->previous_balance),// saldo anterior
                       Util::money_format($row->current_balance),//saldo actual

                       $row->payment_by,//pagado por
                       $row->sub_modality_shortened_payment,// tipo de descuento

                       $row->voucher_payment, //comprobante
                       $row->code_payment, //Nro de cobro
                       $row->state_payment, //Estado del cobro
                       $row->category_name //categoria de cobro
                   ));
        }
        $export = new ArchivoPrimarioExport($data);
        return Excel::download($export, $File.'.xls');
    }

    //reporte de senasir

    public function report_request_senasir_payments($period_id = null, $estimated_date_entry = null)
    {
        // aumenta el tiempo máximo de ejecución de este script a 150 min:
        ini_set('max_execution_time', 9000);
        // aumentar el tamaño de memoria permitido de este script:
        ini_set('memory_limit', '960M');

        //origin y period
        $procedure_modality_id = ProcedureModality::whereShortened('DES-SENASIR')->first()->id;


        if ($period_id) {
            $period = LoanPaymentPeriod::whereId($period_id)->first();
            $estimated_date = Carbon::create($period->year, $period->month, 1);
            $estimated_date = Carbon::parse($estimated_date)->format('Y-m-d');
        } else {
            if ($estimated_date_entry) {
                $estimated_date  = $estimated_date_entry;
            } else {
                $estimated_date = Carbon::now()->format('Y-m-d');
            }
        }
        //comversión de la fecha estimada
        $estimated_date = Carbon::create(Carbon::parse($estimated_date)->format('Y'), Carbon::parse($estimated_date)->format('m'), 15);
        $estimated_date = Carbon::parse($estimated_date)->format('Y-m-d');
        //filtros
        $final_date = $estimated_date? $estimated_date:'';

        $loans_request = Loan::where('state_id', LoanState::where('name', 'Vigente')->first()->id)->where('disbursement_date', '<=', $final_date)->get();


        $id_senasir = array();
        foreach (ProcedureModality::where('name', 'like', '%SENASIR')->get() as $procedure) {
            array_push($id_senasir, $procedure->id);
        }

        $senasir_ancient=array(
            array("Nro Préstamo", "Fecha de desembolso", "Ciudad", "tipo", "Matricula Titular",
            "Matricula Derecho Habiente", "CI", "Extensión", "Primer Nombre", "Segundo Nombre", "Paterno",
             "Materno","Ap de Casada", "Saldo Actual", "Cuota Fija Mensual", "Descuento Programado", "Interés","Amort. TIT o GAR?",
            "GAR Estado","GAR Tipo de estado","Matricula garante","GAR CI", "GAR Exp","GAR Primer Nombre","GAR Segundo Nombre",
            "GAR 1er Apellido","GAR 2do Apellido","GAR Apellido de Casada","GAR Cuota fija","GAR descuento"));


        foreach ($loans_request as $loan) {
            if (in_array($loan->procedure_modality_id, $id_senasir)) {
                foreach ($loan->lenders as $lender) {
                    array_push($senasir_ancient, array(
                            $loan->code,
                            //$loan->disbursement_date,
                            Carbon::parse($loan->disbursement_date)->format('d/m/Y H:i:s'),
                            $loan->city->name,

                            $lender->affiliate_state->name,
                            $lender->registration,
                            $lender->spouse ? $lender->spouse->registration : 0,
                            $lender->identity_card,
                            $lender->city_identity_card->first_shortened,
                            $lender->first_name,
                            $lender->second_name,
                            $lender->last_name,
                            $lender->mothers_last_name,
                            $lender->surname_husband,
                            Util::money_format($loan->balance),
                            Util::money_format($loan->estimated_quota),
                            Util::money_format($loan->get_amount_payment($estimated_date,false,'T')),
                            $loan->interest->annual_interest,
                            $loan->guarantor_amortizing? 'Amort. Garante':'Amort. Titular',
                            $loan->guarantor_amortizing? $loan->guarantors[0]->affiliate_state->affiliate_state_type->name: '',
                            $loan->guarantor_amortizing? $loan->guarantors[0]->affiliate_state->name : '',
                            $loan->guarantor_amortizing? $loan->guarantors[0]->registration : '',
                            $loan->guarantor_amortizing? $loan->guarantors[0]->identity_card : '',
                            $loan->guarantor_amortizing? $loan->guarantors[0]->city_identity_card->first_shortened : '',
                            $loan->guarantor_amortizing? $loan->guarantors[0]->first_name : '',
                            $loan->guarantor_amortizing? $loan->guarantors[0]->second_name : '',
                            $loan->guarantor_amortizing? $loan->guarantors[0]->last_name : '',
                            $loan->guarantor_amortizing? $loan->guarantors[0]->mothers_last_name : '',
                            $loan->guarantor_amortizing? $loan->guarantors[0]->surname_husband : '',
                            $loan->guarantor_amortizing? $loan->guarantors[0]->pivot->quota_treat : '',
                            $loan->guarantor_amortizing? Util::money_format($loan->get_amount_payment($estimated_date,false,'G')) : '',
                    ));
                }
            }
        }

        $file_name="solicitud_Senasir";
        $extension = '.xls';

        $export = new ArchivoPrimarioExport($senasir_ancient);
        return Excel::download($export, $file_name.$extension);
    }
    public function report_rquest_command_payments(request $request){
        // $mes
        // aumenta el tiempo máximo de ejecución de este script a 150 min:
        ini_set('max_execution_time', 9000);
        // aumentar el tamaño de memoria permitido de este script:
        ini_set('memory_limit', '960M');
        $request->validate([
            'period_id'=>'integer|exists:loan_payment_periods,id',
            'estimated_date'=> 'nullable|date_format:"Y-m-d"'
        ]);
         $period_id = $request->period_id;
         $origin_name="cobros-comando-";
         if($period_id){
            $period = LoanPaymentPeriod::find($request->period_id);
            $name_month = Carbon::parse($period->year.'-'.$period->month)->isoFormat('MMMM');
            $year = $period->year;
            $month = $period->month;
            $file_name = $origin_name.$name_month.'-'.$year;
         }else{
             
            $name_month = Carbon::parse($request->estimated_date)->isoFormat('MMMM');
            $year = Carbon::parse($request->estimated_date)->format('Y');
            $month = Carbon::parse($request->date)->format('m');
            $file_name = $origin_name.$name_month.'-'.$year;      
         }
         $data = array(
            array("Nro Préstamo", "Fecha de desembolso", "Ciudad", "tipo", "Matricula Titular",
            "Matricula Derecho Habiente", "CI", "Extensión", "Primer Nombre", "Segundo Nombre", "Paterno",
             "Materno","Ap de Casada", "Saldo Actual", "Cuota Fija Mensual", "Descuento Programado", "Interés","Amort. TIT o GAR?",
            "GAR Estado","GAR Tipo de estado","Matricula garante","GAR CI", "GAR Exp","GAR Primer Nombre","GAR Segundo Nombre",
            "GAR 1er Apellido","GAR 2do Apellido","GAR Apellido de Casada")
        );
         $export = new ArchivoPrimarioExport($data);
         return Excel::download($export, $file_name.'.xls');
    
     }

     /**
    * Reporte de solicitud a senasir o COMANDO
    * @queryParam origin required Tipo de Solicitud C (Comando general) o S (Senasir). Example: C
    * @queryParam estimated_date date Fecha para el periiodo de solicitud. Example: 2021-05-02
    * @queryParam period_id integer id_del periodo . Example: 40
    * @authenticated
    * @responseFile responses/reports_request_payments/request_senasir.200.json
    */
     public function report_request_institution(request $request){
        $request->validate([
            'origin'=>'required|string|in:C,S',
            'period_id'=>'integer|exists:loan_payment_periods,id',
            'estimated_date'=> 'nullable|date_format:"Y-m-d"'
        ]);

        if ($request->origin == 'C') {
            return $this->report_rquest_command_payments($period_id,$estimated_date);
        }else{
            return $this->report_request_senasir_payments($request->period_id, $request->estimated_date);
        }

     }

}
