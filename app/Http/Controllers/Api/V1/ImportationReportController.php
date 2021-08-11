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
use App\AffiliateState;



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
            $procedure_modality_id = 'DES-COMANDO';
        }
        if ($request->origin == 'S') {
            $procedure_modality_id = 'DES-SENASIR';
        }

        $period = LoanPaymentPeriod::whereId($request->period)->first();
        $estimated_date = Carbon::create($period->year, $period->month, 1);
        $estimated_date = Carbon::parse($estimated_date)->endOfMonth()->format('Y-m-d');

        $order_loan = 'Desc';

        $conditions = [];
        //filtros
        $initial_date = $estimated_date? $estimated_date:'';
        $final_date = $estimated_date;
        //$category_name = $request->category_name? $request->category_name:'Regular' ;//para imprementar otro listado de refis y repros  descomentar
        $state_pagado= $request->state_name? $request->state_name:'Pagado';

        $final_date = $estimated_date;

        if ($initial_date != '') {
            array_push($conditions, array('view_loan_amortizations.estimated_date_loan_payment', '=', "%{$initial_date}%"));
        }
        if ($final_date != '') {
            array_push($conditions, array('view_loan_amortizations.estimated_date_loan_payment', '=', "%{$final_date}%"));
        }
        array_push($conditions, array('view_loan_amortizations.states_loan_payment', 'ilike', "%{$state_pagado}%"));
       // array_push($conditions, array('view_loan_amortizations.name_category', 'ilike', "%{$category_name}%")); //listado de refis y repros  descomentar
        //prodedure
        array_push($conditions, array('view_loan_amortizations.modality_shortened_loan_payment', 'like', "{$procedure_modality_id}"));

        $list_loan = DB::table('view_loan_amortizations')
                    ->where($conditions)
                    ->select('*')
                    ->orderBy('code_loan', $order_loan)
                    ->get();

      //  return $list_loan;

      foreach ($list_loan as $loan) {
        $padron = Loan::where('id', $loan->id_loan)->first();
        $loan->modality=$padron->modality->procedure_type->second_name;
        $loan->sub_modality=$padron->modality->shortened;
        $loan->separation='***';
        }

        $File="Amortizaciones_".$request->origin.'_'.$period->month.'_'.$period->year;
        $data=array(
            array("CI AFILIADO","MATRICULA AFILIADO","NOMBRE COMPLETO AFILIADO","***","COD PRÉSTAMO", "FECHA DE DESEMBOLSO", "TIPO","FECHA DE CALCULO","FECHA DE TRANSACCIÓN",
            "MODALIDAD PRÉSTAMO","SUB MODALIDAD PRÉSTAMO",
            "MATRICULA", "CI", "PRIMER NOMBRE","SEGUNDO NOMBRE","APELLIDO PATERNO","APELLIDO MATERNO","APELLIDO CASADA",
            "CAPITAL","INTERÉS CORRIENTE","INTERÉS PENAL","INTERÉS CORRIENTE PENDIENTE",
            "INTERÉS PENAL PENDIENTE","TOTAL PAGADO","SALDO ANTERIOR", "SALDO ACTUAL","PAGADO POR","TIPO DESCUENTO","NRO DE COBRO","ESTADO AMORTIZACIÓN","CATEGORIA")
        );
               foreach ($list_loan as $row){
                array_push($data, array(
                    $row->identity_card_affiliate,
                    $row->registration_affiliate,
                    $row->full_name_affiliate,
                    $row->separation,
                    $row->code_loan,//nro de prestamo
                    Carbon::parse($row->disbursement_date_loan)->format('d/m/Y H:i:s'),//fecha de desembolso
                    $row->state_affiliate_loan_payment,//tipo (pasivo o activo)

                    Carbon::parse($row->estimated_date_loan_payment)->format('d/m/Y'),//fecha de pago
                    Carbon::parse($row->date_loan_payment)->format('d/m/Y H:i:s'),//fecha de transacción
                    $row->modality, //MOdalidad
                    $row->sub_modality, //Sub modalidad

                    $row->registration_borrower,//matricula del prestatario
                    $row->identity_card_borrower,//CI del prestatario
                    $row->first_name_borrower,//primer nombre del prestatario
                    $row->second_name_borrower,//segundo nombre del prestatario
                    $row->last_name_borrower,// aprellido paterno del prestatario
                    $row->mothers_last_name_borrower,//apellido matyerno del prestatario
                    $row->surname_husband_borrower,//apellido de casado del prestatario

                    Util::money_format($row->capital_payment),//capital .. pagado
                    Util::money_format($row->interest_payment), //interes corriente
                    Util::money_format($row->penal_payment),// interes penal
                    Util::money_format($row->interest_accumulated),//interes corriente pendiente
                    Util::money_format($row->penal_accumulated),//interes penal pendiente
                    Util::money_format($row->quota_loan_payment),//total pagado
                    Util::money_format($row->previous_balance),// saldo anterior
                    Util::money_format($row->previous_balance - $row->capital_payment),//saldo actual

                    $row->paid_by_loan_payment,//pagado por
                    $row->modality_shortened_loan_payment,// tipo de descuento

                    $row->code_loan_payment, //Nro de cobro
                    $row->states_loan_payment, //estado del cobro
                    $row->name_category
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

        $loans_request = Loan::where('state_id', LoanState::where('name', 'Vigente')->first()->id)->where('disbursement_date', '<=', $final_date)->orderby('disbursement_date')->get();


        $id_senasir = array();
        foreach (ProcedureModality::where('name', 'like', '%SENASIR')->get() as $procedure) {
            array_push($id_senasir, $procedure->id);
        }

        $senasir_ancient=array(
            array("CI Afiliado", "Matricula Afiliado", "Nombre Completo Afiliado", "***",
                "Nro Préstamo", "Fecha de desembolso", "Ciudad", "tipo", "Matricula Titular",
                "Matricula Prestatario", "CI", "Extensión", "Primer Nombre", "Segundo Nombre", "Paterno",
                "Materno","Ap de Casada", "Saldo Actual", "Cuota Fija Mensual", "Descuento Programado", "Interés","Amort. TIT o GAR?",
                "CI GAR", "Matricula GAR", "Nombre Completo Garante", "***",
                "GAR Estado","GAR Tipo de estado","Matricula garante","GAR CI", "GAR Exp","GAR Primer Nombre","GAR Segundo Nombre",
                "GAR 1er Apellido","GAR 2do Apellido","GAR Apellido de Casada","GAR Cuota fija","GAR descuento"));


        foreach ($loans_request as $loan) {
            if (in_array($loan->procedure_modality_id, $id_senasir)) {
                foreach ($loan->lenders as $lender) {
                    array_push($senasir_ancient, array(
                            $lender->identity_card,
                            $lender->registration,
                            $lender->full_name,
                            "***",
                            $loan->code,
                            Carbon::parse($loan->disbursement_date)->format('d/m/Y H:i:s'),
                            $loan->city->name,
                            $lender->affiliate_state->name,
                            $loan->getBorrowers()->where('id_affiliate', $lender->id)->first()->registration_affiliate,
                            $loan->getBorrowers()->where('id_affiliate', $lender->id)->first()->registration_borrower,
                            $loan->getBorrowers()->where('id_affiliate', $lender->id)->first()->identity_card_borrower,
                            $loan->getBorrowers()->where('id_affiliate', $lender->id)->first()->city_exp_first_shortened_affiliate,
                            $loan->getBorrowers()->where('id_affiliate', $lender->id)->first()->first_name_borrower,
                            $loan->getBorrowers()->where('id_affiliate', $lender->id)->first()->second_name_borrower,
                            $loan->getBorrowers()->where('id_affiliate', $lender->id)->first()->last_name_borrower,
                            $loan->getBorrowers()->where('id_affiliate', $lender->id)->first()->mothers_last_name_borrower,
                            $loan->getBorrowers()->where('id_affiliate', $lender->id)->first()->surname_husband_borrower,
                            Util::money_format($loan->balance),
                            Util::money_format($loan->estimated_quota),
                            Util::money_format($loan->get_amount_payment($estimated_date,false,'T')),
                            $loan->interest->annual_interest,
                            $loan->guarantor_amortizing? 'Amort. Garante':'Amort. Titular',
                            $loan->guarantor_amortizing? $loan->getGuarantors()->first()->identity_card_affiliate : '',
                            $loan->guarantor_amortizing? $loan->getGuarantors()->first()->registration_affiliate : '',
                            $loan->guarantor_amortizing? $loan->getGuarantors()->first()->full_name_affiliate : '',
                            "***",
                            $loan->guarantor_amortizing? $loan->guarantors[0]->affiliate_state->affiliate_state_type->name: '',
                            $loan->guarantor_amortizing? $loan->guarantors[0]->affiliate_state->name : '',
                            $loan->guarantor_amortizing? $loan->getGuarantors()->first()->registration_affiliate : '',
                            $loan->guarantor_amortizing? $loan->getGuarantors()->first()->registration_borrower : '',
                            $loan->guarantor_amortizing? $loan->getGuarantors()->first()->identity_card_borrower : '',
                            $loan->guarantor_amortizing? $loan->getGuarantors()->first()->city_exp_first_shortened_affiliate : '',
                            $loan->guarantor_amortizing? $loan->getGuarantors()->first()->first_name_borrower : '',
                            $loan->guarantor_amortizing? $loan->getGuarantors()->first()->second_name_borrower : '',
                            $loan->guarantor_amortizing? $loan->getGuarantors()->first()->last_name_borrower : '',
                            $loan->guarantor_amortizing? $loan->getGuarantors()->first()->mothers_last_name_borrower : '',
                            $loan->guarantor_amortizing? $loan->getGuarantors()->first()->surname_husband_borrower : '',
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
    //reporte de solicitud comando
    public function report_request_command_payments($period_id,$date){
        // aumenta el tiempo máximo de ejecución de este script a 150 min:
        ini_set('max_execution_time', 9000);
        // aumentar el tamaño de memoria permitido de este script:
        ini_set('memory_limit', '960M');
         $origin_name="cobros-comando-";
         if($period_id){
            $period = LoanPaymentPeriod::find($period_id);
            $name_month = Carbon::parse($period->year.'-'.$period->month)->isoFormat('MMMM');
            $year = $period->year;
            $month = $period->month;
            $file_name = $origin_name.$name_month.'-'.$year;
         }else{
            if ($date){
                $estimated_date  = $date;
            } else {
                $estimated_date = Carbon::now()->format('Y-m-d');
            }           
            $name_month = Carbon::parse($estimated_date)->isoFormat('MMMM');
            $year = Carbon::parse($estimated_date)->format('Y');
            $month = Carbon::parse($estimated_date)->format('m');
            $file_name = $origin_name.$name_month.'-'.$year;      
         }
         $estimated_date = Carbon::create($year,$month, 15);
         $estimated_date = Carbon::parse($estimated_date)->format('Y-m-d');
         //todos los prestamos menores o iguales a la fecha de corte
         $current_loans =  "select id_loan as id from view_loan_borrower
         where  CAST(disbursement_date_loan AS date) <= CAST('$estimated_date' AS date) and state_affiliate in('Servicio','Disponibilidad') and state_loan ='Vigente'"; 
         $current_loans = DB::select($current_loans);
         $data = array(
            array("Nro Préstamo", "Fecha de desembolso", "Ciudad", "tipo", "Matricula Titular",
            "CI", "Extensión", "Primer Nombre", "Segundo Nombre", "Paterno",
            "Materno","Ap de Casada", "Saldo Actual", "Cuota Fija Mensual", "Descuento Programado", "Interés","Amort. TIT o GAR?",
            "GAR Estado","GAR Tipo de estado","Matricula garante","GAR CI", "GAR Exp","GAR Primer Nombre","GAR Segundo Nombre",
            "GAR 1er Apellido","GAR 2do Apellido","GAR Apellido de Casada","GAR Cuota fija","GAR descuento","GAR2 Estado","GAR2 Tipo de estado","Matricula garante","GAR2 CI", "GAR2 Exp","GAR2 Primer Nombre","GAR2 Segundo Nombre",
            "GAR2 1er Apellido","GAR2 2do Apellido","GAR2 Apellido de Casada","GAR2 Cuota fija","GAR2 descuento")
        );
        foreach ($current_loans as $loan) {
            $loan = Loan::find($loan->id);
            foreach ($loan->lenders as $lender) {
                array_push($data, array(
                        $loan->code,
                        Carbon::parse($loan->disbursement_date)->format('d/m/Y H:i:s'),
                        $loan->city->name,
                        $lender->affiliate_state->name,
                        $lender->registration,
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
                        $loan->guarantor_amortizing? Util::money_format($loan->get_amount_payment($estimated_date=null,false,'G')) : '',
                        $loan->guarantor_amortizing? ((isset($loan->guarantors[1])) ? $loan->guarantors[1]->affiliate_state->affiliate_state_type->name : ''): '' ,
                        $loan->guarantor_amortizing? ((isset($loan->guarantors[1])) ? $loan->guarantors[1]->affiliate_state->name : ''): '' ,
                        $loan->guarantor_amortizing? ((isset($loan->guarantors[1])) ? $loan->guarantors[1]->registration : ''): '' ,
                        $loan->guarantor_amortizing? ((isset($loan->guarantors[1])) ? $loan->guarantors[1]->identity_card : ''): '' ,
                        $loan->guarantor_amortizing? ((isset($loan->guarantors[1])) ? $loan->guarantors[1]->city_identity_card->first_shortened : ''): '' ,
                        $loan->guarantor_amortizing? ((isset($loan->guarantors[1])) ? $loan->guarantors[1]->first_name : ''): '' ,
                        $loan->guarantor_amortizing? ((isset($loan->guarantors[1])) ? $loan->guarantors[1]->second_name : ''): '' ,
                        $loan->guarantor_amortizing? ((isset($loan->guarantors[1])) ? $loan->guarantors[1]->last_name : ''): '' ,
                        $loan->guarantor_amortizing? ((isset($loan->guarantors[1])) ? $loan->guarantors[1]->mothers_last_name : ''): '' ,
                        $loan->guarantor_amortizing? ((isset($loan->guarantors[1])) ? $loan->guarantors[1]->surname_husband : ''): '' ,
                        $loan->guarantor_amortizing? ((isset($loan->guarantors[1])) ? $loan->guarantors[1]->pivot->quota_treat  : ''): '' ,
                        $loan->guarantor_amortizing? ((isset($loan->guarantors[1])) ? Util::money_format($loan->get_amount_payment($estimated_date=null,false,'G')) : ''):'',           
                ));
            }     
        }    
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
            'period'=>'integer|exists:loan_payment_periods,id',
            'date'=> 'nullable|date_format:"Y-m-d"'
        ]);

        if ($request->origin == 'C') {
            return $this->report_request_command_payments($request->period,$request->date);
        }else{
            return $this->report_request_senasir_payments($request->period, $request->date);
        }

     }

}
