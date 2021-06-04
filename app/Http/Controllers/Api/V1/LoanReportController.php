<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Module;
use Util;

use DB;
use App\Affiliate;
use App\City;
use App\User;
use App\Loan;
use App\LoanState;
use Carbon;
use App\ProcedureModality;
use App\LoanGlobalParameter;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ArchivoPrimarioExport;
use App\Exports\SheetExportPayment;
use App\Exports\MultipleSheetExportPayment;
use App\Exports\MultipleSheetExportPaymentMora;
use Illuminate\Support\Facades\Storage;
use App\Exports\FileWithMultipleSheetsReport;
use App\Exports\FileWithMultipleSheetsDefaulted;
//use App\Exports\SheetExportPayment;

class LoanReportController extends Controller
{
  /** @group Reportes de Prestamos
   * Reporte de prestamos desembolsados 
   * Reporte de prestamos desembolsados o vigentes 
   * @queryParam initial_date Fecha inicial. Example: 2021-01-01
   * @queryParam final_date Fecha Final. Example: 2021-05-01
   * @authenticated
   * @responseFile responses/report_loans/loan_desembolsado.200.json
   */

  public function report_loan_vigent(Request $request){
    // aumenta el tiempo máximo de ejecución de este script a 150 min:
    ini_set('max_execution_time', 9000);
    // aumentar el tamaño de memoria permitido de este script:
    ini_set('memory_limit', '960M');

    $order_loan = 'Desc';
    $initial_date = request('initial_date') ?? '';
    $final_date = request('final_date') ?? '';
    $state_vigente='Vigente';
    $conditions = [];
    if ($initial_date != '') {
      array_push($conditions, array('loans.disbursement_date', '>=', "%{$initial_date}%"));
    }
    if ($final_date != '') {
      array_push($conditions, array('loans.disbursement_date', '<=', "%{$final_date}%"));
    }
    
    array_push($conditions, array('loan_states.name', 'ilike', "%{$state_vigente}%"));

    $list_loan = DB::table('loans')
            ->join('procedure_modalities','loans.procedure_modality_id', '=', 'procedure_modalities.id')
            ->join('procedure_types','procedure_modalities.procedure_type_id', '=', 'procedure_types.id')
            ->join('loan_states','loans.state_id', '=', 'loan_states.id')
            ->join('cities','loans.city_id', '=', 'cities.id')
            //->join('loan_affiliates','loans.id', '=', 'loan_affiliates.loan_id')
        // ->join('affiliates','loan_affiliates.affiliate_id', '=', 'affiliates.id')
            ->join('affiliates','loans.disbursable_id', '=', 'affiliates.id')
            ->leftjoin('spouses','affiliates.id', '=', 'spouses.affiliate_id')
            ->join('affiliate_states','affiliates.affiliate_state_id', '=', 'affiliate_states.id')
            ->join('affiliate_state_types','affiliate_states.affiliate_state_type_id', '=', 'affiliate_state_types.id')
            ->leftjoin('pension_entities','affiliates.pension_entity_id', '=', 'pension_entities.id')
            ->leftjoin('loan_destinies','loans.destiny_id', '=', 'loan_destinies.id')
            ->whereNull('loans.deleted_at')
            ->where($conditions)
            ->select('loans.id as id_loan','loans.code as code_loan','affiliates.id as id_affiliate','affiliates.identity_card as identity_card_affiliate',
            'affiliates.registration as registration_affiliate','affiliates.last_name as last_name_affiliate','affiliates.mothers_last_name as mothers_last_name_affiliate',
            'affiliates.first_name as first_name_affiliate','affiliates.second_name as second_name_affiliate','affiliates.surname_husband as surname_husband_affiliate',
            'procedure_modalities.name as sub_modality_loan','procedure_types.second_name as modality_loan','loans.amount_approved as amount_approved_loan',
            'affiliate_state_types.name as state_type_affiliate','affiliate_states.name as state_affiliate',
        // 'loan_affiliates.quota_treat as quota_loan', 'loan_affiliates.guarantor as guarantor_loan_affiliate',
            'loan_states.name as state_loan',
            'pension_entities.name as pension_entity_affiliate','loans.disbursement_date as disbursement_date_loan',
            'loans.request_date as request_date_loan','cities.name as name_city','spouses.registration as registration_spouse','loans.num_accounting_voucher as loan_accounting',
            'loans.parent_reason as parent_reason_loan','loans.amount_approved as amount_disbursement',DB::raw("(loans.amount_approved - loans.refinancing_balance) as amount_disbursement_liquido"),
            'loans.loan_term as term_loan','loan_destinies.name as name_destinity_loan')
            //->where('affiliates.identity_card','LIKE'.'%'.$request->identity_card.'%')
            ->orderBy('loans.code', $order_loan)
            ->get();
 
               foreach ($list_loan as $loan) {
                 $padron = Loan::where('id', $loan->id_loan)->first();
                 $loan->balance_loan=$padron->balance;
                 $loan->lender = $padron->lenders;
                 $loan->refinancing_balance=$padron->refinancing_balance;
                 $loan->payment_amount_ampli= $padron->payment_pending_confirmation();
               }
               $File="ListadoPrestamosDesembolsados";
               $data=array(
                   array( "NRO DE PRÉSTAMO", "FECHA DE SOLICITUD", "FECHA DESEMBOLSO",
                   "REGIONAL","TIPO","MODALIDAD","SUB MODALIDAD",
                   "CEDULA DE IDENTIDAD","EXP","MATRICULA","MATRICULA CÓNYUGUE",
                   "PRIMER NOMBRE","SEGUNDO NOMBRE","PATERNO","MATERNO","APELLIDO CASADA",
                   "NRO CBTE CONTABLE","SALDO ACTUAL","AMPLIACIÓN","MONTO DESEMBOLSADO","MONTO REFINANCIADO","LIQUIDO DESEMBOLSADO",
                   "PLAZO","ESTÁDO PRÉSTAMO","DESTINO CREDITO" )
               );
               foreach ($list_loan as $row){
                   array_push($data, array(
                      // $row->id_loan,
                       $row->code_loan,//NRO DE PRESTAMO
                       //$row->request_date_loan,
                       Carbon::parse($row->request_date_loan)->format('d/m/Y'),
                       //$row->disbursement_date_loan,
                       Carbon::parse($row->disbursement_date_loan)->format('d/m/Y H:i:s'),
                       $row->name_city,
                       $row->state_type_affiliate,
                       $row->modality_loan,//modalidad
                       $row->sub_modality_loan,//sub modalidad
                       $row->identity_card_affiliate,
                       $row->lender[0]->expeditionCard,
                       $row->registration_affiliate,//matrifcula 
                       $row->registration_spouse, //matricula esposa

                       $row->first_name_affiliate,
                       $row->second_name_affiliate,
                       $row->last_name_affiliate,
                       $row->mothers_last_name_affiliate,
                       $row->surname_husband_affiliate,

                       $row->loan_accounting,
                       Util::money_format($row->balance_loan),
                       $row->parent_reason_loan,//ampliacion
                       Util::money_format($row->amount_disbursement),//monto desembolsado
                       $row->refinancing_balance? Util::money_format($row->payment_amount_ampli->stimated_date):'0',//MONTO REFINANCIADO//MONTO REFINANCIADO
                       Util::money_format($row->amount_disbursement_liquido),//liquido desembolsado
                       $row->term_loan,//plazo
                       $row->state_loan,//estado del prestamo

                       $row->name_destinity_loan
                   ));
               }
               $export = new ArchivoPrimarioExport($data);
               return Excel::download($export, $File.'.xls');
   }

   /** @group Reportes de Prestamos
   * Reporte de prestamos del estado de cartera 
   * Reporte de prestamos del estado de cartera (vigente - cancelado)
   * @queryParam initial_date Fecha inicial. Example: 2021-01-01
   * @queryParam final_date Fecha Final. Example: 2021-05-01
   * @authenticated
   * @responseFile responses/report_loans/loan_desembolsado.200.json
   */

  public function report_loan_state_cartera(Request $request){
    // aumenta el tiempo máximo de ejecución de este script a 150 min:
    ini_set('max_execution_time', 9000);
    // aumentar el tamaño de memoria permitido de este script:
    ini_set('memory_limit', '960M');

    $order_loan = 'Desc';
    $initial_date = request('initial_date') ?? '';
    $final_date = request('final_date') ?? '';
    $state_vigente='Vigente';
    $state_liquidado='Liquidado';
    $conditions = [];
    $conditions_liq = [];
    if ($initial_date != '') {
      array_push($conditions, array('loans.disbursement_date', '>=', "%{$initial_date}%"));
      array_push($conditions_liq, array('loans.disbursement_date', '>=', "%{$initial_date}%"));
    }
    if ($final_date != '') {
      array_push($conditions, array('loans.disbursement_date', '<=', "%{$final_date}%"));
      array_push($conditions_liq, array('loans.disbursement_date', '<=', "%{$final_date}%"));
    }else{
        $final_date=Carbon::now()->format('Y-m-d');
        //array_push($conditions, array('loans.disbursement_date', '<=', "%{$final_date}%"));
        //array_push($conditions_liq, array('loans.disbursement_date', '<=', "%{$final_date}%"));
    }
    
    array_push($conditions, array('loan_states.name', 'ilike', "%{$state_vigente}%"));
    array_push($conditions_liq, array('loan_states.name', 'ilike', "%{$state_liquidado}%"));

       $list_loan = DB::table('loans')
               ->join('procedure_modalities','loans.procedure_modality_id', '=', 'procedure_modalities.id')
               ->join('procedure_types','procedure_modalities.procedure_type_id', '=', 'procedure_types.id')
               ->join('loan_states','loans.state_id', '=', 'loan_states.id')
               ->join('cities','loans.city_id', '=', 'cities.id')
               ->join('loan_affiliates','loans.id', '=', 'loan_affiliates.loan_id')
               ->join('affiliates','loan_affiliates.affiliate_id', '=', 'affiliates.id')
               ->leftjoin('spouses','affiliates.id', '=', 'spouses.affiliate_id')
               ->join('affiliate_states','affiliates.affiliate_state_id', '=', 'affiliate_states.id')
               ->join('affiliate_state_types','affiliate_states.affiliate_state_type_id', '=', 'affiliate_state_types.id')
               ->leftjoin('pension_entities','affiliates.pension_entity_id', '=', 'pension_entities.id')
               ->leftjoin('loan_destinies','loans.destiny_id', '=', 'loan_destinies.id')
               ->whereNull('loans.deleted_at')
               ->where($conditions)
               ->select('loans.id as id_loan','loans.code as code_loan','affiliates.id as id_affiliate','affiliates.identity_card as identity_card_affiliate',
               'affiliates.registration as registration_affiliate','affiliates.last_name as last_name_affiliate','affiliates.mothers_last_name as mothers_last_name_affiliate',
               'affiliates.first_name as first_name_affiliate','affiliates.second_name as second_name_affiliate','affiliates.surname_husband as surname_husband_affiliate',
               'procedure_modalities.name as sub_modality_loan','procedure_types.second_name as modality_loan','loans.amount_approved as amount_approved_loan',
               'affiliate_state_types.name as state_type_affiliate','affiliate_states.name as state_affiliate','loan_affiliates.quota_treat as quota_loan','loan_states.name as state_loan',
               'loan_affiliates.guarantor as guarantor_loan_affiliate','loan_affiliates.indebtedness_calculated as indebtedness_calculated_loan_affiliate','pension_entities.name as pension_entity_affiliate','loans.disbursement_date as disbursement_date_loan',
               'loans.request_date as request_date_loan','cities.name as name_city','spouses.registration as registration_spouse','loans.num_accounting_voucher as loan_accounting',
               'loans.parent_reason as parent_reason_loan','loans.amount_approved as amount_disbursement',DB::raw("(loans.amount_approved - loans.refinancing_balance) as amount_disbursement_liquido"),
               'loans.loan_term as term_loan','loan_destinies.name as name_destinity_loan')
               //->where('affiliates.identity_card','LIKE'.'%'.$request->identity_card.'%')
               ->distinct('loans.code')
               ->orderBy('loans.code', $order_loan)
               ->get();
 
               foreach ($list_loan as $loan) {
                 $padron = Loan::where('id', $loan->id_loan)->first();
                 $loan->balance_loan=$padron->balance;
                 $loan->last_payment=$padron->getLastPaymentDateAttribute($final_date);
                 $loan->amount_approved=$padron->amount_approved;
                 $loan->refinancing_balance=$padron->refinancing_balance;
                 $loan->payment_amount_ampli= $padron->payment_pending_confirmation();
                 $loan->parent_reason= $padron->parent_reason;
                 //$loan->record = $padron->records;
               }
               //return $list_loan;
               $File="ListadoPrestamosDesembolsados";
               $data=array(
                   array( "NRO DE PRÉSTAMO", "FECHA DE SOLICITUD", "FECHA DESEMBOLSO",
                   "REGIONAL","TIPO","MODALIDAD","SUB MODALIDAD",
                   "CEDULA DE IDENTIDAD","MATRICULA","MATRICULA CÓNYUGUE",
                   "PRIMER NOMBRE","SEGUNDO NOMBRE","PATERNO","MATERNO","APELLIDO CASADA",
                   "NRO CBTE CONTABLE","SALDO ACTUAL","MONTO DESEMBOLSADO","LIQUIDO DESEMBOLSADO",
                   "PLAZO","ESTÁDO PRÉSTAMO","DESTINO CREDITO","CAPITAL PAGADO FECHA DE CORTE","SALDO A FECHA DE CORTE",
                   "MONTO APROBADO","MONTO DESEMBOLSADO DE REFINANCIAMIENTO" ,"MONTO REFINANCIADO","AMPLIACIÓN?","INDICE DE ENDEUDAMIENTO")
               );
               foreach ($list_loan as $row){
                   array_push($data, array(
                      // $row->id_loan,
                       $row->code_loan,//NRO DE PRESTAMO
                       //$row->request_date_loan,
                       Carbon::parse($row->request_date_loan)->format('d/m/Y'),
                       //$row->disbursement_date_loan,
                       Carbon::parse($row->disbursement_date_loan)->format('d/m/Y H:i:s'),
                       $row->name_city,
                       $row->state_type_affiliate,
                       $row->modality_loan, //MOdalidad
                       $row->sub_modality_loan, //Sub modalidad

                       $row->identity_card_affiliate,
                       $row->registration_affiliate,//matrifcula 
                       $row->registration_spouse, //matricula esposa

                       $row->first_name_affiliate,
                       $row->second_name_affiliate,
                       $row->last_name_affiliate,
                       $row->mothers_last_name_affiliate,
                       $row->surname_husband_affiliate,

                       $row->loan_accounting,
                       Util::money_format($row->balance_loan),
                     
                       Util::money_format($row->amount_disbursement),//monto desembolsado
                       Util::money_format($row->amount_disbursement_liquido),//liquido desembolsado
                       $row->term_loan,//plazo
                       $row->state_loan,//estado del prestamo

                       $row->name_destinity_loan,
                      // $row->last_payment? $row->last_payment:' sin registro',

                       $row->last_payment? Util::money_format($row->amount_approved - $row->last_payment->previous_balance+$row->last_payment->capital_payment):' sin registro',//capital pagado 
                       $row->last_payment? Util::money_format($row->last_payment->previous_balance-$row->last_payment->capital_payment):' sin registro',//Saldo a fecha de corte
                       Util::money_format($row->amount_approved),//monto aprobado 
                       $row->refinancing_balance? Util::money_format($row->refinancing_balance):Util::money_format($row->amount_approved),//MONTO DESEMBOLSADO
                       $row->refinancing_balance? Util::money_format($row->payment_amount_ampli->stimated_date):'0',//MONTO REFINANCIADO
                       $row->parent_reason? $row->parent_reason:'no',//SI ES AMPLIACION
                       Util::money_format($row->indebtedness_calculated_loan_affiliate)//indice de endeudamineto
                      
                   ));
               }

               //liquidacion
               $list_loan_liq = DB::table('loans')
               ->join('procedure_modalities','loans.procedure_modality_id', '=', 'procedure_modalities.id')
               ->join('procedure_types','procedure_modalities.procedure_type_id', '=', 'procedure_types.id')
               ->join('loan_states','loans.state_id', '=', 'loan_states.id')
               ->join('cities','loans.city_id', '=', 'cities.id')
               ->join('loan_affiliates','loans.id', '=', 'loan_affiliates.loan_id')
               ->join('affiliates','loan_affiliates.affiliate_id', '=', 'affiliates.id')
               ->leftjoin('spouses','affiliates.id', '=', 'spouses.affiliate_id')
               ->join('affiliate_states','affiliates.affiliate_state_id', '=', 'affiliate_states.id')
               ->join('affiliate_state_types','affiliate_states.affiliate_state_type_id', '=', 'affiliate_state_types.id')
               ->leftjoin('pension_entities','affiliates.pension_entity_id', '=', 'pension_entities.id')
               ->leftjoin('loan_destinies','loans.destiny_id', '=', 'loan_destinies.id')
               ->whereNull('loans.deleted_at')
               ->where($conditions_liq)
               ->select('loans.id as id_loan','loans.code as code_loan','affiliates.id as id_affiliate','affiliates.identity_card as identity_card_affiliate',
               'affiliates.registration as registration_affiliate','affiliates.last_name as last_name_affiliate','affiliates.mothers_last_name as mothers_last_name_affiliate',
               'affiliates.first_name as first_name_affiliate','affiliates.second_name as second_name_affiliate','affiliates.surname_husband as surname_husband_affiliate',
               'procedure_modalities.name as sub_modality_loan','procedure_types.second_name as modality_loan','loans.amount_approved as amount_approved_loan',
               'affiliate_state_types.name as state_type_affiliate','affiliate_states.name as state_affiliate','loan_affiliates.quota_treat as quota_loan','loan_states.name as state_loan',
               'loan_affiliates.guarantor as guarantor_loan_affiliate','pension_entities.name as pension_entity_affiliate','loans.disbursement_date as disbursement_date_loan',
               'loans.request_date as request_date_loan','cities.name as name_city','spouses.registration as registration_spouse','loans.num_accounting_voucher as loan_accounting',
               'loans.parent_reason as parent_reason_loan','loans.amount_approved as amount_disbursement',DB::raw("(loans.amount_approved - loans.refinancing_balance) as amount_disbursement_liquido"),
               'loans.loan_term as term_loan','loan_destinies.name as name_destinity_loan','loan_affiliates.indebtedness_calculated as indebtedness_calculated_loan_affiliate')
               //->where('affiliates.identity_card','LIKE'.'%'.$request->identity_card.'%')
               ->distinct('loans.code')
               ->orderBy('loans.code', $order_loan)
               ->get();
 
               foreach ($list_loan_liq as $loan) {
                $padron = Loan::where('id', $loan->id_loan)->first();
                $loan->balance_loan=$padron->balance;
                $loan->last_payment=$padron->getLastPaymentDateAttribute($final_date);
                $loan->amount_approved=$padron->amount_approved;
                $loan->refinancing_balance=$padron->refinancing_balance;
                $loan->payment_amount_ampli= $padron->payment_pending_confirmation();
                $loan->parent_reason= $padron->parent_reason;
               }
               //return $list_loan_liq;
               $File="ListadoPrestamosVigenteLiquidado";
               $data_liq=array(
                    array( "NRO DE PRÉSTAMO", "FECHA DE SOLICITUD", "FECHA DESEMBOLSO",
                    "REGIONAL","TIPO","MODALIDAD","SUB MODALIDAD",
                    "CEDULA DE IDENTIDAD","MATRICULA","MATRICULA CÓNYUGUE",
                    "PRIMER NOMBRE","SEGUNDO NOMBRE","PATERNO","MATERNO","APELLIDO CASADA",
                    "NRO CBTE CONTABLE","SALDO ACTUAL","MONTO DESEMBOLSADO","LIQUIDO DESEMBOLSADO",
                    "PLAZO","ESTÁDO PRÉSTAMO","DESTINO CREDITO","CAPITAL PAGADO FECHA DE CORTE","SALDO A FECHA DE CORTE",
                    "MONTO APROBADO","MONTO DESEMBOLSADO DE REFINANCIAMIENTO" ,"MONTO REFINANCIADO","AMPLIACIÓN?","INDICE DE ENDEUDAMIENTO")
               );
               foreach ($list_loan_liq as $row){
                   array_push($data_liq, array(
                      // $row->id_loan,
                      $row->code_loan,//NRO DE PRESTAMO
                      //$row->request_date_loan,
                      Carbon::parse($row->request_date_loan)->format('d/m/Y'),
                      //$row->disbursement_date_loan,
                      Carbon::parse($row->disbursement_date_loan)->format('d/m/Y H:i:s'),
                      $row->name_city,
                      $row->state_type_affiliate,
                      $row->modality_loan, //MOdalidad
                      $row->sub_modality_loan, //Sub modalidad

                      $row->identity_card_affiliate,
                      $row->registration_affiliate,//matrifcula 
                      $row->registration_spouse, //matricula esposa

                      $row->first_name_affiliate,
                      $row->second_name_affiliate,
                      $row->last_name_affiliate,
                      $row->mothers_last_name_affiliate,
                      $row->surname_husband_affiliate,

                      $row->loan_accounting,
                      Util::money_format($row->balance_loan),
                      
                      Util::money_format($row->amount_disbursement),//monto desembolsado
                      Util::money_format($row->amount_disbursement_liquido),//liquido desembolsado
                      $row->term_loan,//plazo
                      $row->state_loan,//estado del prestamo

                      $row->name_destinity_loan,
                     // $row->last_payment? $row->last_payment:' sin registro',

                      $row->last_payment? Util::money_format($row->amount_approved - $row->last_payment->previous_balance+$row->last_payment->capital_payment):' sin registro',//capital pagado 
                      $row->last_payment? Util::money_format($row->last_payment->previous_balance-$row->last_payment->capital_payment):' sin registro',//Saldo a fecha de corte
                      Util::money_format($row->amount_approved),//monto aprobado 
                      $row->refinancing_balance? Util::money_format($row->refinancing_balance):Util::money_format($row->amount_approved),//MONTO DESEMBOLSADO
                      $row->refinancing_balance? Util::money_format($row->payment_amount_ampli->stimated_date):'0',//MONTO REFINANCIADO
                      $row->parent_reason? $row->parent_reason:'no',//SI ES AMPLIACION
                      Util::money_format($row->indebtedness_calculated_loan_affiliate)//indice de endeudamineto
                   ));
               }

               $export = new MultipleSheetExportPayment($data, $data_liq,'PRE-VIGENTE','PRE-LIQUIDADO');
               return Excel::download($export, $File.'.xls');
   }

   /** @group Reportes de Prestamos
   * Reporte de prestamos en mora 
   * Reporte de prestamos prestamos en mora PARCIAL TOTAL MORA
   * @authenticated
   * @responseFile responses/report_loans/loan_desembolsado.200.json
   */

  public function report_loans_mora(Request $request){
    $state = LoanState::whereName('Vigente')->first();

    $loans=Loan::where('state_id',$state->id)->get();
    $loans_mora_total = collect();
    $loans_mora_parcial = collect();
    $loans_mora = collect();

    //mora
    foreach($loans as $loan){
        if($loan->defaulted && count($loan->payments) > 0){
            if(count($loan->guarantors)>0){
                $loan->guarantor = $loan->guarantors;
            }
            if(count($loan->guarantors)>=2){
                $loan->guarantor_b = $loan->guarantors;
            }
            $loan->lenders = $loan->lenders;
            if(count($loan->personal_references)>0){
                $loan->personal_ref = $loan->personal_references;
            }
            $loan->separation="*";
            $loans_mora->push($loan);
          }
         //return $loan->guarantors[1];

          if($loan->defaulted && count($loan->payments)== 0){
            if(count($loan->guarantors)>0){
                $loan->guarantor = $loan->guarantors;
            }
            if(count($loan->guarantors)>=2){
                $loan->guarantor_b = $loan->guarantors;
            }
            $loan->lenders = $loan->lenders;
            if(count($loan->personal_references)>0){
                $loan->personal_ref = $loan->personal_references;
            }
            $loan->separation="*";
    
            $loans_mora_total->push($loan);
          }
          if($loan->getdelay_parcial() && !$loan->defaulted ){
            if(count($loan->guarantors)>0){
                $loan->guarantor = $loan->guarantors;
            }
            if(count($loan->guarantors)>=2){
                $loan->guarantor_b = $loan->guarantors;
            }
            $loan->lenders = $loan->lenders;
            if(count($loan->personal_references)>0){
                $loan->personal_ref = $loan->personal_references;
            }
            $loan->separation="*";
            $loans_mora_parcial->push($loan);
          }
    }
    //return $loans_mora;
//prestamomora total
    $File="PrestamosMora";
        $data_mora_total=array(
            array("MATRICULA","CI","EXP","NOMBRE COMPLETO","NRO DE CEL.2","NRO FIJO","CIUDAD","DIRECCIÓN","PTMO","FECHA DESEMBOLSO",
            "NRO DE CUOTAS","TASA ANUAL","FECHA DEL ÚLTIMO PAGO","TIPO DE PAGO","CUOTA MENSUAL","SALDO ACTUAL","ÉSTADO DEL AFILIADO","MODALIDAD","SUB MODALIDAD","DÍAS MORA",
            "*","NOMBRE COMPLETO (Ref. Personal)","NRO DE TEL. FIJO (Ref. Personal)","NRO DE CEL (Ref. Personal)","DIRECCIÓN(Ref. Personal)",
            "**","MATRICULA","CI","EXP","NOMBRE COMPLETO","NRO DE TEL. FIJO","NRO DE CEL","ESTADO DEL AFILIADO",
            "***","MATRICULA","CI","EXP","NOMBRE COMPLETO","NRO DE TEL. FIJO","NRO DE CEL","ESTADO DEL AFILIADO")
        );
        foreach ($loans_mora_total as $row){
            array_push($data_mora_total, array(
                $row->lenders[0]->affiliate_registration_number,
                $row->lenders[0]->identity_card,
                $row->lenders[0]->expeditionCard,
                $row->lenders[0]->first_name.' '.$row->lenders[0]->second_name.' ' .$row->lenders[0]->last_name.' '.$row->lenders[0]->mothers_last_name,
                $row->lenders[0]->cell_phone_number,
                $row->lenders[0]->phone_number,
                $row->lenders[0]->city_birth->name,
                $row->lenders[0]->address->full_address,
                $row->code,
                //$row->disbursement_date,
                Carbon::parse($row->disbursement_date)->format('d/m/Y H:i:s'),
                $row->loan_term,
                $row->interest->annual_interest,
                $row->lastPaymentValidated? Carbon::parse($row->lastPaymentValidated->estimated_date)->format('d-m-Y'):'sin registro',
                $row->lastPaymentValidated? $row->lastPaymentValidated->modality->shortened:'sin registro',
                Util::money_format($row->estimated_quota),
                Util::money_format($row->balance),
                $row->lenders[0]->affiliate_state->affiliate_state_type->name,
                $row->modality->procedure_type->second_name,
                $row->modality->shortened,
                $row->getdelay()->penal,
                $row->separation,
                $row->personal_ref ? $row->personal_ref[0]->first_name.' '.$row->personal_ref[0]->second_name.' '.$row->personal_ref[0]->last_name.' '.$row->personal_ref[0]->mothers_last_name.' '.$row->personal_ref[0]->surname_husband:'no tiene registro',
                $row->personal_ref ? $row->personal_ref[0]->phone_number:'S/R',
                $row->personal_ref ? $row->personal_ref[0]->cell_phone_number:'S/R',
                $row->personal_ref ? $row->personal_ref[0]->address:'S/R',
                $row->separation,
                $row->guarantor ? $row->guarantor[0]->affiliate_registration_number:'',
                $row->guarantor ? $row->guarantor[0]->identity_card:'',
                $row->guarantor_b ? $row->guarantor_b[1]->expeditionCard:'',
                $row->guarantor ? $row->guarantor[0]->first_name.' '.$row->guarantor[0]->second_name.' ' .$row->guarantor[0]->last_name.' '.$row->guarantor[0]->mothers_last_name:'no tiene garante',
                $row->guarantor ? $row->guarantor[0]->phone_number:'S/R',
                $row->guarantor ? $row->guarantor[0]->cell_phone_number:'S/R',
                $row->guarantor ? $row->guarantor[0]->affiliate_state->affiliate_state_type->name:'',
                //$row->guarantor ? $row->guarantor[0]->address->full_address:'S/R',
                $row->separation,
                $row->guarantor_b ? $row->guarantor_b[1]->affiliate_registration_number:'',
                $row->guarantor_b ? $row->guarantor_b[1]->identity_card:'',
                $row->guarantor_b ? $row->guarantor_b[1]->expeditionCard:'',
                $row->guarantor_b ? $row->guarantor_b[1]->first_name.' '.$row->guarantor_b[1]->second_name.' ' .$row->guarantor_b[1]->last_name.' '.$row->guarantor_b[1]->mothers_last_name:'no tiene garante',
                $row->guarantor_b ? $row->guarantor_b[1]->phone_number:'S/R',
                $row->guarantor_b ? $row->guarantor_b[1]->cell_phone_number:'S/R',
                $row->guarantor ? $row->guarantor[0]->affiliate_state->affiliate_state_type->name:'',
                //$row->guarantor_b ? $row->guarantor_b[1]->address->full_address:'S/R'
         
            ));
        }
        //prestamomora parcial
        $File="PrestamosMoraParcial";
        $data_mora_parcial=array(
            array("MATRICULA","CI","EXP","NOMBRE COMPLETO","NRO DE CEL.2","NRO FIJO","CIUDAD","DIRECCIÓN","PTMO","FECHA DESEMBOLSO",
            "NRO DE CUOTAS","TASA ANUAL","FECHA DEL ÚLTIMO PAGO","TIPO DE PAGO","CUOTA MENSUAL","SALDO ACTUAL","ÉSTADO DEL AFILIADO","MODALIDAD","SUB MODALIDAD","DÍAS MORA",
            "*","NOMBRE COMPLETO (Ref. Personal)","NRO DE TEL. FIJO (Ref. Personal)","NRO DE CEL (Ref. Personal)","DIRECCIÓN(Ref. Personal)",
            "**","MATRICULA","CI","EXP","NOMBRE COMPLETO","NRO DE TEL. FIJO","NRO DE CEL","ESTADO DEL AFILIADO",
            "***","MATRICULA","CI","EXP","NOMBRE COMPLETO","NRO DE TEL. FIJO","NRO DE CEL","ESTADO DEL AFILIADO")
        );
        foreach ($loans_mora_parcial as $row){
            array_push($data_mora_parcial, array(
                $row->lenders[0]->affiliate_registration_number,
                $row->lenders[0]->identity_card,
                $row->lenders[0]->expeditionCard,
                $row->lenders[0]->first_name.' '.$row->lenders[0]->second_name.' ' .$row->lenders[0]->last_name.' '.$row->lenders[0]->mothers_last_name,
                $row->lenders[0]->cell_phone_number,
                $row->lenders[0]->phone_number,
                $row->lenders[0]->city_birth->name,
                $row->lenders[0]->address->full_address,
                $row->code,
                //$row->disbursement_date,
                Carbon::parse($row->disbursement_date)->format('d/m/Y H:i:s'),
                $row->loan_term,
                $row->interest->annual_interest,
                $row->lastPaymentValidated? Carbon::parse($row->lastPaymentValidated->estimated_date)->format('d-m-Y'):'sin registro',
                $row->lastPaymentValidated? $row->lastPaymentValidated->modality->shortened:'sin registro',
                Util::money_format($row->estimated_quota),
                Util::money_format($row->balance),
                $row->lenders[0]->affiliate_state->affiliate_state_type->name,
                $row->modality->procedure_type->second_name,
                $row->modality->shortened,
                $row->getdelay()->penal,
                $row->separation,
                $row->personal_ref ? $row->personal_ref[0]->first_name.' '.$row->personal_ref[0]->second_name.' '.$row->personal_ref[0]->last_name.' '.$row->personal_ref[0]->mothers_last_name.' '.$row->personal_ref[0]->surname_husband:'no tiene registro',
                $row->personal_ref ? $row->personal_ref[0]->phone_number:'S/R',
                $row->personal_ref ? $row->personal_ref[0]->cell_phone_number:'S/R',
                $row->personal_ref ? $row->personal_ref[0]->address:'S/R',
                $row->separation.$row->separation,
                $row->guarantor ? $row->guarantor[0]->affiliate_registration_number:'',
                $row->guarantor ? $row->guarantor[0]->identity_card:'',
                $row->guarantor_b ? $row->guarantor_b[1]->expeditionCard:'',
                $row->guarantor ? $row->guarantor[0]->first_name.' '.$row->guarantor[0]->second_name.' ' .$row->guarantor[0]->last_name.' '.$row->guarantor[0]->mothers_last_name:'no tiene garante',
                $row->guarantor ? $row->guarantor[0]->phone_number:'S/R',
                $row->guarantor ? $row->guarantor[0]->cell_phone_number:'S/R',
                $row->guarantor ? $row->guarantor[0]->affiliate_state->affiliate_state_type->name:'',
                //$row->guarantor ? $row->guarantor[0]->address->full_address:'S/R',
                $row->separation.$row->separation.$row->separation,
                $row->guarantor_b ? $row->guarantor_b[1]->affiliate_registration_number:'',
                $row->guarantor_b ? $row->guarantor_b[1]->identity_card:'',
                $row->guarantor_b ? $row->guarantor_b[1]->expeditionCard:'',
                $row->guarantor_b ? $row->guarantor_b[1]->first_name.' '.$row->guarantor_b[1]->second_name.' ' .$row->guarantor_b[1]->last_name.' '.$row->guarantor_b[1]->mothers_last_name:'no tiene garante',
                $row->guarantor_b ? $row->guarantor_b[1]->phone_number:'S/R',
                $row->guarantor_b ? $row->guarantor_b[1]->cell_phone_number:'S/R',
                $row->guarantor ? $row->guarantor[0]->affiliate_state->affiliate_state_type->name:'',
                //$row->guarantor_b ? $row->guarantor_b[1]->address->full_address:'S/R'
              
            ));
        }
        //prestamomora 
        $File="PrestamosMora";
        $data_mora=array(
            array("MATRICULA","CI","EXP","NOMBRE COMPLETO","NRO DE CEL.2","NRO FIJO","CIUDAD","DIRECCIÓN","PTMO","FECHA DESEMBOLSO",
            "NRO DE CUOTAS","TASA ANUAL","FECHA DEL ÚLTIMO PAGO","TIPO DE PAGO","CUOTA MENSUAL","SALDO ACTUAL","ÉSTADO DEL AFILIADO","MODALIDAD","SUB MODALIDAD","DÍAS MORA",
            "*","NOMBRE COMPLETO (Ref. Personal)","NRO DE TEL. FIJO (Ref. Personal)","NRO DE CEL (Ref. Personal)","DIRECCIÓN(Ref. Personal)",
            "**","MATRICULA","CI","EXP","NOMBRE COMPLETO","NRO DE TEL. FIJO","NRO DE CEL","ESTADO DEL AFILIADO",
            "***","MATRICULA","CI","EXP","NOMBRE COMPLETO","NRO DE TEL. FIJO","NRO DE CEL","ESTADO DEL AFILIADO")
        );
        foreach ($loans_mora as $row){
            array_push($data_mora, array(
                $row->lenders[0]->affiliate_registration_number,
                $row->lenders[0]->identity_card,
                $row->lenders[0]->expeditionCard,
                $row->lenders[0]->first_name.' '.$row->lenders[0]->second_name.' ' .$row->lenders[0]->last_name.' '.$row->lenders[0]->mothers_last_name,
                $row->lenders[0]->cell_phone_number,
                $row->lenders[0]->phone_number,
                $row->lenders[0]->city_birth->name,
                $row->lenders[0]->address->full_address,
                $row->code,
                //$row->disbursement_date,
                Carbon::parse($row->disbursement_date)->format('d/m/Y H:i:s'),
                $row->loan_term,
                $row->interest->annual_interest,
                $row->lastPaymentValidated? Carbon::parse($row->lastPaymentValidated->estimated_date)->format('d-m-Y'):'sin registro',
                $row->lastPaymentValidated? $row->lastPaymentValidated->modality->shortened:'sin registro',
                Util::money_format($row->estimated_quota),
                Util::money_format($row->balance),
                $row->lenders[0]->affiliate_state->affiliate_state_type->name,
                $row->modality->procedure_type->second_name,
                $row->modality->shortened,
                $row->getdelay()->penal,
                $row->separation,
                $row->personal_ref ? $row->personal_ref[0]->first_name.' '.$row->personal_ref[0]->second_name.' '.$row->personal_ref[0]->last_name.' '.$row->personal_ref[0]->mothers_last_name.' '.$row->personal_ref[0]->surname_husband:'no tiene registro',
                $row->personal_ref ? $row->personal_ref[0]->phone_number:'S/R',
                $row->personal_ref ? $row->personal_ref[0]->cell_phone_number:'S/R',
                $row->personal_ref ? $row->personal_ref[0]->address:'S/R',
                $row->separation.$row->separation,
                $row->guarantor ? $row->guarantor[0]->affiliate_registration_number:'',
                $row->guarantor ? $row->guarantor[0]->identity_card:'',
                $row->guarantor_b ? $row->guarantor_b[1]->expeditionCard:'',
                $row->guarantor ? $row->guarantor[0]->first_name.' '.$row->guarantor[0]->second_name.' ' .$row->guarantor[0]->last_name.' '.$row->guarantor[0]->mothers_last_name:'no tiene garante',
                $row->guarantor ? $row->guarantor[0]->phone_number:'S/R',
                $row->guarantor ? $row->guarantor[0]->cell_phone_number:'S/R',
                $row->guarantor ? $row->guarantor[0]->affiliate_state->affiliate_state_type->name:'',
                //$row->guarantor ? $row->guarantor[0]->address->full_address:'S/R',
                $row->separation.$row->separation.$row->separation,
                $row->guarantor_b ? $row->guarantor_b[1]->affiliate_registration_number:'',
                $row->guarantor_b ? $row->guarantor_b[1]->identity_card:'',
                $row->guarantor_b ? $row->guarantor_b[1]->expeditionCard:'',
                $row->guarantor_b ? $row->guarantor_b[1]->first_name.' '.$row->guarantor_b[1]->second_name.' ' .$row->guarantor_b[1]->last_name.' '.$row->guarantor_b[1]->mothers_last_name:'no tiene garante',
                $row->guarantor_b ? $row->guarantor_b[1]->phone_number:'S/R',
                $row->guarantor_b ? $row->guarantor_b[1]->cell_phone_number:'S/R',
                $row->guarantor ? $row->guarantor[0]->affiliate_state->affiliate_state_type->name:'',
                //$row->guarantor_b ? $row->guarantor_b[1]->address->full_address:'S/R'
            ));
        }

        //$export = new MultipleSheetExportPaymentMora($data,$data_mora_parcial,$data_mora,'MORA TOTAL','MORA PARCIAL','MORA');
        $export = new MultipleSheetExportPaymentMora($data_mora_total,$data_mora_parcial,$data_mora,'MORA TOTAL','MORA PARCIAL','MORA');
        return Excel::download($export, $File.'.xls');
  }

  /** @group Reportes de Prestamos
    * generar reporte de nuevos prestamos desembolsados
    * reporte de los nuevos desembolsados por periodo
	  * @bodyParam date date Fecha para el periodo a consultar. Example: 16-06-2021
    * @responseFile responses/report_loans/loan_desembolsado.200.json
    * @authenticated
    */
    public function loan_information(Request $request)
    {
        $month = Carbon::parse($request->date)->format('m');
        $year = Carbon::parse($request->date)->format('Y');
        $loans = Loan::whereMonth('disbursement_date', $month)->whereYear('disbursement_date', $year)->get();
        $id_senasir = array();
        foreach(ProcedureModality::where('name', 'like', '%SENASIR')->get() as $procedure)
             array_push($id_senasir, $procedure->id);
        $id_comando = array();
        foreach(ProcedureModality::where('name', 'like', '%Activo')->orWhere('name', 'like', '%Activo%')->get() as $procedure)
             array_push($id_comando, $procedure->id);
 
         $command_sheet_before=array(
             array("Nro Prestamo", "Fecha de desembolso", "Ciudad", "tipo", "Matricula Titular", "Matricula Derecho Habiente", "CI", "Extension", "Primer Nombre", "Segundo Nombre", "Paterno", "Materno", "Saldo Actual", "Cuota Fija Mensual", "Descuento Programado", "Interes")
         );
         $command_sheet_later=array(
             array("Nro Prestamo", "Fecha de desembolso", "Ciudad", "tipo", "Matricula Titular", "Matricula Derecho Habiente", "CI", "Extension", "Primer Nombre", "Segundo Nombre", "Paterno", "Materno", "Saldo Actual", "Cuota Fija Mensual", "Descuento Programado", "Interes")
         );
         $senasir_sheet_before=array(
             array("Nro Prestamo", "Fecha de desembolso", "Ciudad", "tipo", "Matricula Titular", "Matricula Derecho Habiente", "CI", "Extension", "Primer Nombre", "Segundo Nombre", "Paterno", "Materno", "Saldo Actual", "Cuota Fija Mensual", "Descuento Programado", "Interes")
         );
         $senasir_sheet_later=array(
             array("Nro Prestamo", "Fecha de desembolso", "Ciudad", "tipo", "Matricula Titular", "Matricula Derecho Habiente", "CI", "Extension", "Primer Nombre", "Segundo Nombre", "Paterno", "Materno", "Saldo Actual", "Cuota Fija Mensual", "Descuento Programado", "Interes")
         );
         foreach($loans as $loan){
             if(Carbon::parse($loan->disbursement_date)->day < LoanGlobalParameter::first()->offset_interest_day){
                 if(in_array($loan->procedure_modality_id, $id_comando))
                 {
                     foreach($loan->lenders as $lender)
                     {
                         array_push($command_sheet_before, array(
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
                             Util::money_format($loan->balance),
                             Util::money_format($loan->estimated_quota),
                             Util::money_format($lender->pivot->quota_treat),
                             $loan->interest->annual_interest,
                         ));
                     }
                 }
                 else{
                     if(in_array($loan->procedure_modality_id, $id_senasir))
                     {
                         foreach($loan->lenders as $lender)
                         {
                             array_push($senasir_sheet_before, array(
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
                                 Util::money_format($loan->balance),
                                 Util::money_format($loan->estimated_quota),
                                 Util::money_format($lender->pivot->quota_treat),
                                 $loan->interest->annual_interest,
                             ));
                         }
                     }
                 }
             }
         }
         $sub_month = Carbon::parse($request->date)->subMonth()->format('m');
         $loans_before = Loan::whereMonth('disbursement_date', $sub_month)->whereYear('disbursement_date', $year)->get();//considerar caso fin de año
         foreach($loans_before as $loan){
             if(Carbon::parse($loan->disbursement_date)->day >= LoanGlobalParameter::first()->offset_interest_day){
                 if(in_array($loan->procedure_modality_id, $id_comando))
                 {
                     foreach($loan->lenders as $lender)
                     {
                         array_push($command_sheet_later, array(
                             $loan->code,
                            // $loan->disbursement_date,
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
                             Util::money_format($loan->balance),
                             Util::money_format($loan->estimated_quota),
                             Util::money_format($lender->pivot->quota_treat),
                             $loan->interest->annual_interest,
                         ));
                     }
                 }
                 else{
                     if(in_array($loan->procedure_modality_id, $id_senasir))
                     {
                         foreach($loan->lenders as $lender)
                         {
                             array_push($senasir_sheet_later, array(
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
                                 Util::money_format($loan->balance),
                                 Util::money_format($loan->estimated_quota),
                                 Util::money_format($lender->pivot->quota_treat),
                                 $loan->interest->annual_interest,
                             ));
                         }
                     }
                 }
             }
         }
         $file_name = $month.'-'.$year;
         $extension = '.xls';
         $export = new FileWithMultipleSheetsReport($command_sheet_later, $command_sheet_before, $senasir_sheet_later, $senasir_sheet_before);
         return Excel::download($export, $file_name.$extension);
    }
 
    
    /** @group Reportes de Prestamos
     * Reporte descuentos por garantia
     * reporte de prestamos amortizados por los garantes
     * @responseFile responses/report_loans/loan_desembolsado.200.json
     * @authenticated
     */
    public function loan_defaulted_guarantor()
    {
         $month = Carbon::now()->format('m');
         $year = Carbon::now()->format('Y');
         $loans = Loan::where('state_id', LoanState::where('name', 'Vigente')->first()->id)->where('guarantor_amortizing', true)->get();
         $command_sheet_dafaulted=array(
             array("Nro Prestamo", "Fecha de desembolso", "Ciudad", "tipo", "Matricula Titular", "Matricula Derecho Habiente", "CI", "Extension", "Primer Nombre", "Segundo Nombre", "Paterno", "Materno", "Saldo Actual", "Cuota Fija Mensual", "Descuento Programado", "Interes")
         );
         $senasir_sheet_defaulted=array(
             array("Nro Prestamo", "Fecha de desembolso", "Ciudad", "tipo", "Matricula Titular", "Matricula Derecho Habiente", "CI", "Extension", "Primer Nombre", "Segundo Nombre", "Paterno", "Materno", "Saldo Actual", "Cuota Fija Mensual", "Descuento Programado", "Interes")
         );
         $id_senasir = array();
        foreach(ProcedureModality::where('name', 'like', '%SENASIR')->get() as $procedure)
             array_push($id_senasir, $procedure->id);
        $id_comando = array();
        foreach(ProcedureModality::where('name', 'like', '%Activo')->orWhere('name', 'like', '%Activo%')->get() as $procedure)
             array_push($id_comando, $procedure->id);
 
         foreach($loans as $loan){
             if(in_array($loan->procedure_modality_id, $id_comando))
             {
                 foreach($loan->lenders as $lender)
                 {
                     array_push($command_sheet_dafaulted, array(
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
                         Util::money_format($loan->balance),
                         Util::money_format($loan->estimated_quota),
                         Util::money_format($lender->pivot->quota_treat),
                         $loan->interest->annual_interest,
                     ));
                 }
                 foreach($loan->guarantors as $guarantor)
                 {
                     array_push($command_sheet_dafaulted, array(
                         $loan->code,
                        // $loan->disbursement_date,
                         Carbon::parse($loan->disbursement_date)->format('d/m/Y H:i:s'),
                         $loan->city->name,
                         $guarantor->affiliate_state->name,
                         $guarantor->registration,
                         $guarantor->spouse ? $guarantor->spouse->registration : 0,
                         $guarantor->identity_card,
                         $guarantor->city_identity_card->first_shortened,
                         $guarantor->first_name,
                         $guarantor->second_name,
                         $guarantor->last_name,
                         $guarantor->mothers_last_name,
                         Util::money_format($loan->balance),
                         Util::money_format($loan->estimated_quota),
                         Util::money_format($guarantor->pivot->quota_treat),
                         $loan->interest->annual_interest,
                     ));
                 }
             }
             if(in_array($loan->procedure_modality_id, $id_senasir))
             {
                 foreach($loan->lenders as $lender)
                 {
                     array_push($senasir_sheet_defaulted, array(
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
                         Util::money_format($loan->balance),
                         Util::money_format($loan->estimated_quota),
                         Util::money_format($lender->pivot->quota_treat),
                         $loan->interest->annual_interest,
                     ));
                 }
                 foreach($loan->guarantors as $guarantor)
                 {
                     array_push($senasir_sheet_defaulted, array(
                         $loan->code,
                         //$loan->disbursement_date,
                         Carbon::parse($loan->disbursement_date)->format('d/m/Y H:i:s'),
                         $loan->city->name,
                         $guarantor->affiliate_state->name,
                         $guarantor->registration,
                         $guarantor->spouse ? $guarantor->spouse->registration : 0,
                         $guarantor->identity_card,
                         $guarantor->city_identity_card->first_shortened,
                         $guarantor->first_name,
                         $guarantor->second_name,
                         $guarantor->last_name,
                         $guarantor->mothers_last_name,
                         Util::money_format($loan->balance),
                         Util::money_format($loan->estimated_quota),
                         Util::money_format($guarantor->pivot->quota_treat),
                         $loan->interest->annual_interest,
                     ));
                 }
             }
         }
         $file_name = $month.'-'.$year;
         $extension = '.xls';
         $export = new FileWithMultipleSheetsDefaulted($command_sheet_dafaulted, $senasir_sheet_defaulted);
         return Excel::download($export, $file_name.$extension);
    }
}
