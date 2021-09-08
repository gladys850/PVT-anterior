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
        $date = $request->final_date.' 23:59:59';
      array_push($conditions, array('loans.disbursement_date', '<=', "%{$date}%"));
    }
    
    array_push($conditions, array('loan_states.name', 'ilike', "%{$state_vigente}%"));
    //desde aqui
    if ($initial_date != '' && $final_date != '') {
        $date_ini = $request->initial_date.' 00:00:00';
        $date_fin = $request->final_date.' 23:59:59';

        $list_loan = Loan::where('state_id', LoanState::where('name', 'Vigente')->first()->id)->whereBetween('disbursement_date', [$date_ini, $date_fin])->get();
    }else{
        if ($final_date != '') {
            $date_fin = $request->final_date.' 23:59:59';
            $list_loan = Loan::where('state_id', LoanState::where('name', 'Vigente')->first()->id)->where('disbursement_date', '<=', $date_fin)->get();

        }else{
            if ($initial_date != '') {
                $date_ini = $request->initial_date.' 00:00:00';
                $list_loan = Loan::where('state_id', LoanState::where('name', 'Vigente')->first()->id)->where('disbursement_date', '>=', $date_ini)->get();
            }else{
                $list_loan = Loan::where('state_id', LoanState::where('name', 'Vigente')->first()->id)->get();
            }
        } 
    }
               $File="ListadoPrestamosDesembolsados";
               $data=array(
                   array( "CI AFILIADO","MATRICULA AFILIADO","NOMBRE COMPLETO AFILIADO","***","COD PRESTAMO", "FECHA DE SOLICITUD", "FECHA DE DESEMBOLSO",
                   "DPTO","TIPO ESTADO","ESTADO AFILIADO","MODALIDAD","SUB MODALIDAD",
                   "CEDULA DE IDENTIDAD","EXP","MATRICULA",
                   "PRIMER NOMBRE","SEGUNDO NOMBRE","PATERNO","MATERNO","APELLIDO CASADA","***",
                   "NRO CBTE CONTABLE","SALDO ACTUAL","AMPLIACIÓN","MONTO DESEMBOLSADO","MONTO REFINANCIADO","LIQUIDO DESEMBOLSADO",
                   "PLAZO","ESTÁDO PRÉSTAMO","DESTINO CREDITO" )
               );
               foreach ($list_loan as $loan){
               foreach($loan->getBorrowers() as $lender){
                   array_push($data, array(
                    $lender->identity_card_affiliate,
                    $lender->registration_affiliate,
                    $lender->full_name_affiliate,
                    $loan->guarantor_amortizing? '***' : '***',
                    $loan->code,
                    Carbon::parse($loan->request_date)->format('d/m/Y'),
                    Carbon::parse($loan->disbursement_date)->format('d/m/Y H:i:s'),
                    $loan->city->name,
                    $lender->state_type_affiliate,
                    $lender->state_affiliate,
                    $loan->modality->procedure_type->name,
                    $loan->modality->shortened,
                    $lender->identity_card_borrower,
                    $lender->city_exp_first_shortened_borrower,
                    $lender->registration_borrower,
                    $lender->first_name_borrower,
                    $lender->second_name_borrower,
                    $lender->last_name_borrower,
                    $lender->mothers_last_name_borrower,
                    $lender->surname_husband_borrower,'***',
                    $loan->num_accounting_voucher,
                    Util::money_format($loan->balance),//SALDO ACTUAL
                    $loan->parent_reason,
                    Util::money_format($loan->amount_approved),
                    $loan->parent_reason? Util::money_format($loan->amount_approved - $loan->refinancing_balance) : '0,00',//MONTO REFINANCIADO//MONTO REFINANCIADO
                    $loan->parent_reason? Util::money_format($loan->refinancing_balance):Util::money_format($loan->amount_approved),// LIQUIDO DESEMBOLSADO
                    $loan->loan_term,//plazo
                    $loan->state->name,//estado del prestamo
                    $loan->destiny->name
                   ));
               }
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

    $initial_date = request('initial_date') ?? '';
    $final_date = request('final_date') ?? '';

    //desde aqui
    if ($initial_date != '' && $final_date != '') {
        $date_ini = $request->initial_date.' 00:00:00';
        $date_fin = $request->final_date.' 23:59:59';

        //$list_loan = Loan::where('state_id', LoanState::where('name', 'Vigente')->first()->id)->whereBetween('disbursement_date', [$date_ini, $date_fin])->get();
        $list_loan = DB::table('view_loan_borrower')
               ->whereBetween('view_loan_borrower.disbursement_date_loan', [$date_ini, $date_fin])
              ->where("view_loan_borrower.state_loan", "Vigente")
              ->select('*')
              ->orderBy('code_loan')
              ->get();
    }else{
        if ($final_date != '') {
            $date_fin = $request->final_date.' 23:59:59';
            //$list_loan = Loan::where('state_id', LoanState::where('name', 'Vigente')->first()->id)->where('disbursement_date', '<=', $date_fin)->get();
            $list_loan = DB::table('view_loan_borrower')
              ->where("view_loan_borrower.disbursement_date_loan", "<=", $date_fin)
              ->where("view_loan_borrower.state_loan", "Vigente")
              ->select('*')
              ->orderBy('code_loan')
              ->get();

        }else{
            $date_fin = Carbon::now();
            if ($initial_date != '') {
                $date_ini = $request->initial_date.' 00:00:00';
                //$list_loan = Loan::where('state_id', LoanState::where('name', 'Vigente')->first()->id)->where('disbursement_date', '>=', $date_ini)->get();
                $list_loan = DB::table('view_loan_borrower')
                    ->where("view_loan_borrower.disbursement_date_loan", ">=", $date_ini)
                    ->where("view_loan_borrower.state_loan", "Vigente")
                    ->select('*')
                    ->orderBy('code_loan')
                    ->get();
            }else{
                //$list_loan = Loan::where('state_id', LoanState::where('name', 'Vigente')->first()->id)->get();
                $list_loan = DB::table('view_loan_borrower')
                    ->where("view_loan_borrower.state_loan", "Vigente")
                    ->select('*')
                    ->orderBy('code_loan')
                    ->get();
            }
        }
    }
    $File="ListadoPrestamosDesembolsados";

    $data=array(
    array( "NRO DE PRÉSTAMO", "FECHA DE SOLICITUD", "FECHA DESEMBOLSO",
            "INDICE DE ENDEUDAMIENTO", "SECTOR", "PRODUCTO", 
            "CI AFILIADO", "EXP", "MATRICULA AFILIADO", "NOMBRE COMPLETO AFILIADO", "GRADO", "***",
            "CI PRESTATARIIO", "EXP", "MATRICULA PRESTATARIO", "APELLIDO PATERNO PRESTATARIO", "APELLIDO MATERNO PRESTATARIO", "APE. CASADA PRESTATARIO", "1er NOMPRE PRESTATARIO", "2DO NOMBRE PRESTATARIO",
            "NRO. CBTE. CONTABLE", "CAPITAL PAGADO A FECHA DE CORTE", "SALDO A LA FECHA DE CORTE", "MONTO DESEMBOLSADO",
            "MONTO REFINANCIADO", "LIQUIDO DESEMBOLSADO", "ESTADO PTMO", "AMPLIACION",
            "FECHA ULTIMO PAGO DE INTERES")
        );
    foreach ($list_loan as $loan){
        array_push($data, array(
            $loan->code_loan,
            Carbon::parse($loan->request_date_loan)->format('d/m/Y'),
            Carbon::parse($loan->disbursement_date_loan)->format('d/m/Y H:i:s'),

            $loan->indebtedness_borrower,
            $loan->state_type_affiliate,
            $loan->name_modality_loan,

            $loan->identity_card_affiliate,
            $loan->city_exp_first_shortened_affiliate,
            $loan->registration_affiliate,
            $loan->full_name_affiliate,
            $loan->name_degree,
            "***",

            $loan->identity_card_borrower,
            $loan->city_exp_first_shortened_borrower,
            $loan->registration_borrower,
            $loan->last_name_borrower,
            $loan->mothers_last_name_borrower,
            $loan->surname_husband_borrower,
            $loan->first_name_borrower,
            $loan->second_name_borrower,

            $loan->num_accounting_voucher_loan,
            Loan::whereId($loan->id_loan)->first()->last_payment_date($date_fin) ? $loan->amount_approved_loan - (Loan::whereId($loan->id_loan)->first()->last_payment_date($date_fin)->previous_balance-Loan::whereId($loan->id_loan)->first()->last_payment_date($date_fin)->capital_payment) : 0,
            Loan::whereId($loan->id_loan)->first()->last_payment_date($date_fin) ? Loan::whereId($loan->id_loan)->first()->last_payment_date($date_fin)->previous_balance-Loan::whereId($loan->id_loan)->first()->last_payment_date($date_fin)->capital_payment : $loan->amount_approved_loan,
            $loan->amount_approved_loan,

            $loan->parent_loan_id ? Loan::whereId($loan->parent_loan_id)->first()->last_payment_validated->capital_payment :"",
            $loan->parent_loan_id ? $loan->amount_approved-Loan::whereId($loan->parent_loan_id)->first()->last_payment_validated->capital_payment : "",
            $loan->state_loan,
            $loan->parent_reason_loan,
            Loan::whereId($loan->id_loan)->first()->last_payment_date($date_fin) ? Loan::whereId($loan->id_loan)->first()->last_payment_date($date_fin)->loan_payment_date : "",
        ));
    }

    //liquidacion
    if ($initial_date != '' && $final_date != '') {
        $date_ini = $request->initial_date.' 00:00:00';
        $date_fin = $request->final_date.' 23:59:59';
        
        //$list_loan = Loan::where('state_id', LoanState::where('name', 'Vigente')->first()->id)->whereBetween('disbursement_date', [$date_ini, $date_fin])->get();
        $list_loan_liq = DB::table('view_loan_borrower')
                        ->where("view_loan_borrower.disbursement_date_loan", ">=", $date_ini)
                        ->where("view_loan_borrower.disbursement_date_loan", "<=", $date_fin)
                        ->where("view_loan_borrower.state_loan", "Liquidado")
                        ->select('*')
                        ->orderBy('code_loan')
                        ->get();
    }else{
        if ($final_date != '') {
            $date_fin = $request->final_date.' 23:59:59';
            //$list_loan = Loan::where('state_id', LoanState::where('name', 'Vigente')->first()->id)->where('disbursement_date', '<=', $date_fin)->get();
            $list_loan_liq = DB::table('view_loan_borrower')
                            ->where("view_loan_borrower.disbursement_date_loan", "<=", $date_fin)
                            ->where("view_loan_borrower.state_loan", "Liquidado")
                            ->select('*')
                            ->orderBy('code_loan')
                            ->get();
        }else{
            if ($initial_date != '') {
                $date_ini = $request->initial_date.' 00:00:00';
                //$list_loan = Loan::where('state_id', LoanState::where('name', 'Vigente')->first()->id)->where('disbursement_date', '>=', $date_ini)->get();
                $list_loan_liq = DB::table('view_loan_borrower')
                            ->where("view_loan_borrower.disbursement_date_loan", ">=", $date_ini)
                            ->where("view_loan_borrower.state_loan", "Liquidado")
                            ->select('*')
                            ->orderBy('code_loan')
                            ->get();
            }else{
                        //$list_loan = Loan::where('state_id', LoanState::where('name', 'Vigente')->first()->id)->get();
                $list_loan_liq = DB::table('view_loan_borrower')
                                ->where("view_loan_borrower.state_loan", "Liquidado")
                                ->select('*')
                                ->orderBy('code_loan')
                                ->get();
            }
        }
    }

    //return $list_loan_liq;
    $File="ListadoPrestamosVigenteLiquidado";
    $data_liq=array(
                array( "NRO DE PRÉSTAMO", "FECHA DE SOLICITUD", "FECHA DESEMBOLSO",
                "INDICE DE ENDEUDAMIENTO", "SECTOR", "PRODUCTO", 
                "CI AFILIADO", "EXP", "MATRICULA AFILIADO", "NOMBRE COMPLETO AFILIADO", "GRADO", "***",
                "CI PRESTATARIIO", "EXP", "MATRICULA PRESTATARIO", "APELLIDO PATERNO PRESTATARIO", "APELLIDO MATERNO PRESTATARIO", "APE. CASADA PRESTATARIO", "1er NOMPRE PRESTATARIO", "2DO NOMBRE PRESTATARIO",
                "NRO. CBTE. CONTABLE", "CAPITAL PAGADO A FECHA DE CORTE", "SALDO A LA FECHA DE CORTE", "MONTO DESEMBOLSADO",
                "MONTO REFINANCIADO", "LIQUIDO DESEMBOLSADO", "ESTADO PTMO", "AMPLIACION",
                "FECHA ULTIMO PAGO DE INTERES")
    );
    foreach ($list_loan_liq as $loan){
        array_push($data_liq, array(
            $loan->code_loan,
            Carbon::parse($loan->request_date_loan)->format('d/m/Y'),
            Carbon::parse($loan->disbursement_date_loan)->format('d/m/Y H:i:s'),
        
            $loan->indebtedness_borrower,
            $loan->state_type_affiliate,
            $loan->name_modality_loan,
        
            $loan->identity_card_affiliate,
            $loan->city_exp_first_shortened_affiliate,
            $loan->registration_affiliate,
            $loan->full_name_affiliate,
            $loan->name_degree,
            "***",
        
            $loan->identity_card_borrower,
            $loan->city_exp_first_shortened_borrower,
            $loan->registration_borrower,
            $loan->last_name_borrower,
            $loan->mothers_last_name_borrower,
            $loan->surname_husband_borrower,
            $loan->first_name_borrower,
            $loan->second_name_borrower,
        
            $loan->num_accounting_voucher_loan,
            Loan::whereId($loan->id_loan)->first()->last_payment_date($date_fin) ? $loan->amount_approved_loan - (Loan::whereId($loan->id_loan)->first()->last_payment_date($date_fin)->previous_balance-Loan::whereId($loan->id_loan)->first()->last_payment_date($date_fin)->capital_payment) : 0,
            Loan::whereId($loan->id_loan)->first()->last_payment_date($date_fin) ? Loan::whereId($loan->id_loan)->first()->last_payment_date($date_fin)->previous_balance-Loan::whereId($loan->id_loan)->first()->last_payment_date($date_fin)->capital_payment : $loan->amount_approved_loan,
            $loan->amount_approved_loan,

            $loan->parent_loan_id ? Loan::whereId($loan->parent_loan_id)->first()->last_payment_validated->capital_payment :"",
            $loan->parent_loan_id ? $loan->amount_approved-Loan::whereId($loan->parent_loan_id)->first()->last_payment_validated->capital_payment : "",
            $loan->state_loan,
            $loan->parent_reason_loan,
            Loan::whereId($loan->id_loan)->first()->last_payment_date($date_fin) ? Loan::whereId($loan->id_loan)->first()->last_payment_date($date_fin)->loan_payment_date : "",
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
    $final_date = request('date') ? Carbon::parse(request('date'))->endOfDay()->format('d-m-Y') : Carbon::now()->endOfDay()->format('d-m-Y');
    $state = LoanState::whereName('Vigente')->first();

    $loans=Loan::where('state_id',$state->id)->orderBy('code')->where('disbursement_date', '<', Carbon::parse($final_date))->get();
    $loans_mora_total = collect();
    $loans_mora_parcial = collect();
    $loans_mora = collect();
    //$date = Carbon::parse(Loan::find(1)->last_payment_validated->estimated_date)->endOfDay()->diffInDays($final_date);return $date;
    //mora
    foreach($loans as $loan){
        if(count($loan->payments) > 0 && $loan->last_payment_validated->estimated_date < $final_date && Carbon::parse($loan->last_payment_validated->estimated_date)->endOfDay()->diffInDays($final_date) > LoanGlobalParameter::first()->days_current_interest){
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

          if(count($loan->payments)== 0 && Carbon::parse($loan->disbursement_date)->diffInDays($final_date) > LoanGlobalParameter::first()->days_current_interest){
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
          if($loan->last_payment_validated && $loan->last_payment_validated->interest_accumulated > 0){
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
            array("MATRICULA AFILIADO","CI AFILIADO", "EXP","NOMBRE COMPLETO AFILIADO", "***","MATRICULA PRESTATARIO","CI PRESTATARIO","EXP","NOMBRE COMPLETO PRESTATARIO","NRO DE CEL.1","NRO DE CEL.2","NRO FIJO","CIUDAD","DIRECCIÓN","PTMO","FECHA DESEMBOLSO",
            "NRO DE CUOTAS","TASA ANUAL","FECHA DEL ÚLTIMO PAGO","TIPO DE PAGO","CUOTA MENSUAL","SALDO ACTUAL","ÉSTADO DEL AFILIADO","MODALIDAD","SUB MODALIDAD","DÍAS MORA",
            "*","NOMBRE COMPLETO (Ref. Personal)","NRO DE TEL. FIJO (Ref. Personal)","NRO DE CEL (Ref. Personal)","DIRECCIÓN(Ref. Personal)",
            "**","MATRICULA AFILIADO (GARANTE 1)", "CI AFILIADO (GARANTE 1)", "EXP (GARANTE 1)", "NOMBRE COMPLETO AFILIADO (GARANTE 1)", "*-->*","MATRICULA (GARANTE TITULAR 1)","CI (GARANTE TITULAR 1)","EXP (GARANTE TITULAR 1)","NOMBRE COMPLETO (GARANTE TITULAR 1)","NRO DE TEL. FIJO","NRO DE CEL. 1","NRO DE CEL. 2","ESTADO DEL AFILIADO",
            "***", "MATRICULA AFILIADO (GARANTE 2)", " CI AFILIADO (GARANTE 2)", "EXP (GARANTE 2)", "NOMBRE COMPLETO AFILIADO (GARANTE 2)", "*-->*","MATRICULA (GARANTE TITULAR 2)","CI (GARANTE TITULAR 2)","EXP (GARANTE TITULAR 2)","NOMBRE COMPLETO (GARANTE TITULAR 2)","NRO DE TEL. FIJO","NRO DE CEL. 1","NRO DE CEL. 2","ESTADO DEL AFILIADO")
        );
        foreach ($loans_mora_total as $row){
            $phone_number = str_replace(array("(", ")", "-"), '', $row->lenders[0]->cell_phone_number);
            $phone_number = explode(",",$phone_number);
            $sw = false;
            $sw_g = false;
            $sw_g2 = false;
            if(count($phone_number)>1)
                $sw = true;
            if(isset($row->guarantor[0])){
                $phone_number_g1 = str_replace(array("(", ")", "-"), '', $row->guarantor[0]->cell_phone_number);
                $phone_number_g1 = explode(",",$phone_number_g1);
                if(count($phone_number_g1)>1)
                    $sw_g = true;
            }
            if(isset($row->guarantor[1])){
                $phone_number_g2 = str_replace(array("(", ")", "-"), '', $row->guarantor[1]->cell_phone_number);
                $phone_number_g2 = explode(",",$phone_number_g2);
                if(count($phone_number)>1)
                    $sw_g2 = true;
            }
            array_push($data_mora_total, array(
                $row->getBorrowers()->where('id_affiliate',$row->lenders[0]->id)->first()->registration_affiliate,
                $row->getBorrowers()->where('id_affiliate',$row->lenders[0]->id)->first()->identity_card_affiliate,
                $row->getBorrowers()->where('id_affiliate',$row->lenders[0]->id)->first()->city_exp_first_shortened_affiliate,
                $row->getBorrowers()->where('id_affiliate',$row->lenders[0]->id)->first()->full_name_affiliate,
                "***",
                $row->getBorrowers()->where('id_affiliate',$row->lenders[0]->id)->first()->registration_borrower,
                $row->getBorrowers()->where('id_affiliate',$row->lenders[0]->id)->first()->identity_card_borrower,
                $row->getBorrowers()->where('id_affiliate',$row->lenders[0]->id)->first()->city_exp_first_shortened_borrower,
                $row->getBorrowers()->where('id_affiliate',$row->lenders[0]->id)->first()->full_name_borrower,
                $phone_number[0],
                $sw ? $phone_number[1] : "",
                //$row->lenders[0]->cell_phone_number,
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
                Carbon::parse($row->disbursement_date)->startOfDay()->diffInDays(Carbon::parse($final_date)->endOfDay()) - LoanGlobalParameter::first()->days_current_interest,
                $row->separation,
                $row->personal_ref ? $row->personal_ref[0]->first_name.' '.$row->personal_ref[0]->second_name.' '.$row->personal_ref[0]->last_name.' '.$row->personal_ref[0]->mothers_last_name.' '.$row->personal_ref[0]->surname_husband:'no tiene registro',
                $row->personal_ref ? $row->personal_ref[0]->phone_number:'S/R',
                $row->personal_ref ? str_replace(array("(", ")", "-"), '', $row->personal_ref[0]->cell_phone_number) :'S/R',
                $row->personal_ref ? $row->personal_ref[0]->address:'S/R',
                $row->separation,
                $row->guarantor ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[0]->id)->first()->registration_affiliate : '',
                $row->guarantor ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[0]->id)->first()->identity_card_affiliate : '',
                $row->guarantor ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[0]->id)->first()->city_exp_first_shortened_affiliate : '',
                $row->guarantor ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[0]->id)->first()->full_name_affiliate : '',
                "*Titular-->*",
                $row->guarantor ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[0]->id)->first()->registration_borrower : '',
                $row->guarantor ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[0]->id)->first()->identity_card_borrower : '',
                $row->guarantor ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[0]->id)->first()->city_exp_first_shortened_borrower : '',
                $row->guarantor ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[0]->id)->first()->full_name_borrower : '',
                $row->guarantor ? $row->guarantor[0]->phone_number:'S/R',
                isset($row->guarantor[0]) ? $phone_number_g1[0] : '',
                $sw_g ? $phone_number_g1[1] : "",
                $row->guarantor ? $row->guarantor[0]->affiliate_state->affiliate_state_type->name:'',
                //$row->guarantor ? $row->guarantor[0]->address->full_address:'S/R',
                $row->separation,
                isset($row->guarantor[1]) ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[1]->id)->first()->registration_affiliate : '',
                isset($row->guarantor[1]) ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[1]->id)->first()->identity_card_affiliate : '',
                isset($row->guarantor[1]) ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[1]->id)->first()->city_exp_first_shortened_affiliate : '',
                isset($row->guarantor[1]) ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[1]->id)->first()->full_name_affiliate : '',
                "*Titular-->*",
                isset($row->guarantor[1]) ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[1]->id)->first()->registration_borrower : '',
                isset($row->guarantor[1]) ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[1]->id)->first()->identity_card_borrower : '',
                isset($row->guarantor[1]) ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[1]->id)->first()->city_exp_first_shortened_borrower : '',
                isset($row->guarantor[1]) ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[1]->id)->first()->full_name_borrower : '',
                $row->guarantor_b ? $row->guarantor_b[1]->phone_number:'S/R',
                isset($row->guarantor[1]) ? $phone_number_g2[0] : '',
                $sw_g2 ? $phone_number_g2[1] : "",
                $row->guarantor ? $row->guarantor[0]->affiliate_state->affiliate_state_type->name:'',
                //$row->guarantor_b ? $row->guarantor_b[1]->address->full_address:'S/R'
         
            ));
        }
        //prestamomora parcial
        $File="PrestamosMoraParcial";
        $data_mora_parcial=array(
            array("MATRICULA AFILIADO","CI AFILIADO", "EXP","NOMBRE COMPLETO AFILIADO", "***","MATRICULA","CI","EXP","NOMBRE COMPLETO","NRO DE CEL. 1","NRO DE CEL. 2","NRO FIJO","CIUDAD","DIRECCIÓN","PTMO","FECHA DESEMBOLSO",
            "NRO DE CUOTAS","TASA ANUAL","FECHA DEL ÚLTIMO PAGO","TIPO DE PAGO","CUOTA MENSUAL","SALDO ACTUAL","ÉSTADO DEL AFILIADO","MODALIDAD","SUB MODALIDAD","DÍAS",
            "*","NOMBRE COMPLETO (Ref. Personal)","NRO DE TEL. FIJO (Ref. Personal)","NRO DE CEL (Ref. Personal)","DIRECCIÓN(Ref. Personal)",
            "**","MATRICULA AFILIADO (GARANTE 1)", "CI AFILIADO (GARANTE 1)", "EXP (GARANTE 1)", "NOMBRE COMPLETO AFILIADO (GARANTE 1)", "*-->*","MATRICULA (GARANTE TITULAR 1)","CI (GARANTE TITULAR 1)","EXP (GARANTE TITULAR 1)","NOMBRE COMPLETO (GARANTE TITULAR 1)","NRO DE TEL. FIJO","NRO DE CEL. 1","NRO DE CEL. 2","ESTADO DEL AFILIADO",
            "***","MATRICULA AFILIADO (GARANTE 2)", "CI AFILIADO (GARANTE 2)", "EXP (GARANTE 2)", "NOMBRE COMPLETO AFILIADO (GARANTE 2)", "*-->*","MATRICULA (GARANTE TITULAR 2)","CI (GARANTE TITULAR 2)","EXP (GARANTE TITULAR 2)","NOMBRE COMPLETO (GARANTE TITULAR 2)","NRO DE TEL. FIJO","NRO DE CEL. 1","NRO DE CEL. 2","ESTADO DEL AFILIADO")
        );
        foreach ($loans_mora_parcial as $row){
            $phone_number = str_replace(array("(", ")", "-"), '', $row->lenders[0]->cell_phone_number);
            $phone_number = explode(",",$phone_number);
            $sw = false;
            $sw_g = false;
            $sw_g2 = false;
            if(count($phone_number)>1)
                $sw = true;
            if(isset($row->guarantor[0])){
                $phone_number_g1 = str_replace(array("(", ")", "-"), '', $row->guarantor[0]->cell_phone_number);
                $phone_number_g1 = explode(",",$phone_number_g1);
                if(count($phone_number_g1)>1)
                    $sw_g = true;
            }
            if(isset($row->guarantor[1])){
                $phone_number_g2 = str_replace(array("(", ")", "-"), '', $row->guarantor[1]->cell_phone_number);
                $phone_number_g2 = explode(",",$phone_number_g2);
                if(count($phone_number)>1)
                    $sw_g2 = true;
            }
            array_push($data_mora_parcial, array(
                $row->getBorrowers()->where('id_affiliate',$row->lenders[0]->id)->first()->registration_affiliate,
                $row->getBorrowers()->where('id_affiliate',$row->lenders[0]->id)->first()->identity_card_affiliate,
                $row->getBorrowers()->where('id_affiliate',$row->lenders[0]->id)->first()->city_exp_first_shortened_affiliate,
                $row->getBorrowers()->where('id_affiliate',$row->lenders[0]->id)->first()->full_name_affiliate,
                "***",
                $row->getBorrowers()->where('id_affiliate',$row->lenders[0]->id)->first()->registration_borrower,
                $row->getBorrowers()->where('id_affiliate',$row->lenders[0]->id)->first()->identity_card_borrower,
                $row->getBorrowers()->where('id_affiliate',$row->lenders[0]->id)->first()->city_exp_first_shortened_borrower,
                $row->getBorrowers()->where('id_affiliate',$row->lenders[0]->id)->first()->full_name_borrower,
                $phone_number[0],
                $sw ? $phone_number[1] : "",
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
                $row->last_payment_validated ? Carbon::parse($row->last_payment_validated->estimated_date)->diffInDays(Carbon::parse($final_date)) : Carbon::parse($row->disbursement_date)->diffInDays(Carbon::parse($final_date)),
                $row->separation,
                $row->personal_ref ? $row->personal_ref[0]->first_name.' '.$row->personal_ref[0]->second_name.' '.$row->personal_ref[0]->last_name.' '.$row->personal_ref[0]->mothers_last_name.' '.$row->personal_ref[0]->surname_husband:'no tiene registro',
                $row->personal_ref ? $row->personal_ref[0]->phone_number:'S/R',
                $row->personal_ref ? str_replace(array("(", ")", "-"), '', $row->personal_ref[0]->cell_phone_number) :'S/R',
                $row->personal_ref ? $row->personal_ref[0]->address:'S/R',
                $row->separation.$row->separation,
                $row->guarantor ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[0]->id)->first()->registration_affiliate : '',
                $row->guarantor ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[0]->id)->first()->identity_card_affiliate : '',
                $row->guarantor ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[0]->id)->first()->city_exp_first_shortened_affiliate : '',
                $row->guarantor ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[0]->id)->first()->full_name_affiliate : '',
                "*Titular-->*",
                $row->guarantor ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[0]->id)->first()->registration_borrower : '',
                $row->guarantor ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[0]->id)->first()->identity_card_borrower : '',
                $row->guarantor ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[0]->id)->first()->city_exp_first_shortened_borrower : '',
                $row->guarantor ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[0]->id)->first()->full_name_borrower : '',
                $row->guarantor ? $row->guarantor[0]->phone_number:'S/R',
                $row->guarantor ? $row->guarantor[0]->phone_number:'S/R',
                isset($row->guarantor[0]) ? $phone_number_g1[0] : '',
                $sw_g ? $phone_number_g1[1] : "",
                $row->guarantor ? $row->guarantor[0]->affiliate_state->affiliate_state_type->name:'',
                //$row->guarantor ? $row->guarantor[0]->address->full_address:'S/R',
                $row->separation.$row->separation.$row->separation,
                isset($row->guarantor[1]) ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[1]->id)->first()->registration_affiliate : '',
                isset($row->guarantor[1]) ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[1]->id)->first()->identity_card_affiliate : '',
                isset($row->guarantor[1]) ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[1]->id)->first()->city_exp_first_shortened_affiliate : '',
                isset($row->guarantor[1]) ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[1]->id)->first()->full_name_affiliate : '',
                "*Titular-->*",
                isset($row->guarantor[1]) ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[1]->id)->first()->registration_borrower : '',
                isset($row->guarantor[1]) ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[1]->id)->first()->identity_card_borrower : '',
                isset($row->guarantor[1]) ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[1]->id)->first()->city_exp_first_shortened_borrower : '',
                isset($row->guarantor[1]) ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[1]->id)->first()->full_name_borrower : '',
                $row->guarantor_b ? $row->guarantor_b[1]->phone_number:'S/R',
                $row->guarantor_b ? $row->guarantor_b[1]->phone_number:'S/R',
                isset($row->guarantor[1]) ? $phone_number_g2[0] : '',
                $sw_g2 ? $phone_number_g2[1] : "",
                $row->guarantor ? $row->guarantor[0]->affiliate_state->affiliate_state_type->name:'',
                //$row->guarantor_b ? $row->guarantor_b[1]->address->full_address:'S/R'
                
            ));
        }
        //prestamomora 
        $File="PrestamosMora";
        $data_mora=array(
            array("MATRICULA AFILIADO","CI AFILIADO", "EXP","NOMBRE COMPLETO AFILIADO", "***","MATRICULA","CI","EXP","NOMBRE COMPLETO","NRO DE CEL.1","NRO DE CEL.2","NRO FIJO","CIUDAD","DIRECCIÓN","PTMO","FECHA DESEMBOLSO",
            "NRO DE CUOTAS","TASA ANUAL","FECHA DEL ÚLTIMO PAGO","TIPO DE PAGO","CUOTA MENSUAL","SALDO ACTUAL","ÉSTADO DEL AFILIADO","MODALIDAD","SUB MODALIDAD","DÍAS MORA",
            "*","NOMBRE COMPLETO (Ref. Personal)","NRO DE TEL. FIJO (Ref. Personal)","NRO DE CEL (Ref. Personal)","DIRECCIÓN(Ref. Personal)",
            "**","MATRICULA AFILIADO (GARANTE 1)", "CI AFILIADO (GARANTE 1)", "EXP (GARANTE 1)", "NOMBRE COMPLETO AFILIADO (GARANTE 1)", "*-->*","MATRICULA (GARANTE TITULAR 1)","CI (GARANTE TITULAR 1)","EXP (GARANTE TITULAR 1)","NOMBRE COMPLETO (GARANTE TITULAR 1)","NRO DE TEL. FIJO","NRO DE CEL1","NRO DE CEL2","ESTADO DEL AFILIADO",
            "***","MATRICULA AFILIADO (GARANTE 2)", "CI AFILIADO (GARANTE 2)", "EXP (GARANTE 2)", "NOMBRE COMPLETO AFILIADO (GARANTE 2)", "*-->*","MATRICULA (GARANTE TITULAR 2)","CI (GARANTE TITULAR 2)","EXP (GARANTE TITULAR 2)","NOMBRE COMPLETO (GARANTE TITULAR 2)","NRO DE TEL. FIJO","NRO DE CEL1","NRO DE CEL.2","ESTADO DEL AFILIADO")
        );
        foreach ($loans_mora as $row){
            $phone_number = str_replace(array("(", ")", "-"), '', $row->lenders[0]->cell_phone_number);
            $phone_number = explode(",",$phone_number);
            $sw = false;
            $sw_g = false;
            $sw_g2 = false;
            if(count($phone_number)>1)
                $sw = true;
            if(isset($row->guarantor[0])){
                $phone_number_g1 = str_replace(array("(", ")", "-"), '', $row->guarantor[0]->cell_phone_number);
                $phone_number_g1 = explode(",",$phone_number_g1);
                if(count($phone_number_g1)>1)
                    $sw_g = true;
            }
            if(isset($row->guarantor[1])){
                $phone_number_g2 = str_replace(array("(", ")", "-"), '', $row->guarantor[1]->cell_phone_number);
                $phone_number_g2 = explode(",",$phone_number_g2);
                if(count($phone_number)>1)
                    $sw_g2 = true;
            }
            array_push($data_mora, array(
                $row->getBorrowers()->where('id_affiliate',$row->lenders[0]->id)->first()->registration_affiliate,
                $row->getBorrowers()->where('id_affiliate',$row->lenders[0]->id)->first()->identity_card_affiliate,
                $row->getBorrowers()->where('id_affiliate',$row->lenders[0]->id)->first()->city_exp_first_shortened_affiliate,
                $row->getBorrowers()->where('id_affiliate',$row->lenders[0]->id)->first()->full_name_affiliate,
                "***",
                $row->getBorrowers()->where('id_affiliate',$row->lenders[0]->id)->first()->registration_borrower,
                $row->getBorrowers()->where('id_affiliate',$row->lenders[0]->id)->first()->identity_card_borrower,
                $row->getBorrowers()->where('id_affiliate',$row->lenders[0]->id)->first()->city_exp_first_shortened_borrower,
                $row->getBorrowers()->where('id_affiliate',$row->lenders[0]->id)->first()->full_name_borrower,
                $phone_number[0],
                $sw ? $phone_number[1] : "",
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
                Carbon::parse($row->last_payment_validated->estimated_date)->diffInDays(Carbon::parse($final_date)) - LoanGlobalParameter::first()->days_current_interest,
                $row->separation,
                $row->personal_ref ? $row->personal_ref[0]->first_name.' '.$row->personal_ref[0]->second_name.' '.$row->personal_ref[0]->last_name.' '.$row->personal_ref[0]->mothers_last_name.' '.$row->personal_ref[0]->surname_husband:'no tiene registro',
                $row->personal_ref ? $row->personal_ref[0]->phone_number:'S/R',
                $row->personal_ref ? str_replace(array("(", ")", "-"), '', $row->personal_ref[0]->cell_phone_number) :'S/R',
                $row->personal_ref ? $row->personal_ref[0]->address:'S/R',
                $row->separation.$row->separation,
                $row->guarantor ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[0]->id)->first()->registration_affiliate : '',
                $row->guarantor ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[0]->id)->first()->identity_card_affiliate : '',
                $row->guarantor ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[0]->id)->first()->city_exp_first_shortened_affiliate : '',
                $row->guarantor ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[0]->id)->first()->full_name_affiliate : '',
                "*Titular-->*",
                $row->guarantor ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[0]->id)->first()->registration_borrower : '',
                $row->guarantor ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[0]->id)->first()->identity_card_borrower : '',
                $row->guarantor ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[0]->id)->first()->city_exp_first_shortened_borrower : '',
                $row->guarantor ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[0]->id)->first()->full_name_borrower : '',
                $row->guarantor ? $row->guarantor[0]->phone_number:'S/R',
                $row->guarantor ? $row->guarantor[0]->phone_number:'S/R',
                isset($row->guarantor[0]) ? $phone_number_g1[0] : '',
                $sw_g ? $phone_number_g1[1] : "",
                $row->guarantor ? $row->guarantor[0]->affiliate_state->affiliate_state_type->name:'',
                //$row->guarantor ? $row->guarantor[0]->address->full_address:'S/R',
                $row->separation.$row->separation.$row->separation,
                isset($row->guarantor[1]) ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[1]->id)->first()->registration_affiliate : '',
                isset($row->guarantor[1]) ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[1]->id)->first()->identity_card_affiliate : '',
                isset($row->guarantor[1]) ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[1]->id)->first()->city_exp_first_shortened_affiliate : '',
                isset($row->guarantor[1]) ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[1]->id)->first()->full_name_affiliate : '',
                "*Titular-->*",
                isset($row->guarantor[1]) ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[1]->id)->first()->registration_borrower : '',
                isset($row->guarantor[1]) ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[1]->id)->first()->identity_card_borrower : '',
                isset($row->guarantor[1]) ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[1]->id)->first()->city_exp_first_shortened_borrower : '',
                isset($row->guarantor[1]) ? $row->getGuarantors()->where('id_affiliate',$row->guarantor[1]->id)->first()->full_name_borrower : '',
                $row->guarantor_b ? $row->guarantor_b[1]->phone_number:'S/R',
                $row->guarantor_b ? $row->guarantor_b[1]->phone_number:'S/R',
                isset($row->guarantor[1]) ? $phone_number_g2[0] : '',
                $sw_g2 ? $phone_number_g2[1] : "",
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
        $loans = Loan::whereMonth('disbursement_date', $month)->whereYear('disbursement_date', $year)->orderBy('disbursement_date')->get();
        $date_previous = Carbon::parse($request->date)->startOfMonth()->subMonth()->endOfMonth()->format('Y-m-d');

        $date_calculate = Carbon::parse($request->date)->endOfMonth()->format('Y-m-d');

        $date_limit = Carbon::create(Carbon::parse($date_previous)->format('Y'), Carbon::parse($date_previous)->format('m'), 15);
        $date_limit = Carbon::parse($date_limit)->format('Y-m-d');
        $date_limit = Carbon::parse($date_limit)->endOfDay();

        $loans_request = Loan::where('state_id', LoanState::where('name', 'Vigente')->first()->id)->where('disbursement_date','<=', $date_limit)->orderBy('disbursement_date')->get();

        $id_senasir = array();
        foreach(ProcedureModality::where('name', 'like', '%SENASIR%')->get() as $procedure)
             array_push($id_senasir, $procedure->id);
        $id_comando = array();
        foreach(ProcedureModality::where('name', 'like', '%Activo%')->orWhere('name', 'like', '%Disponibilidad%')->get() as $procedure)
             array_push($id_comando, $procedure->id);

             $command_sheet_before=array(
                array("CI AFILIADO","EXP AFILIADO","MATRICULA AFILIADO","NOMBRE COMPLETO AFILIADO","***",
                "COD PRESTAMO","MODALIDAD","SUB MODALIDAD", "FECHA DE DESEMBOLSO", "DPTO", "TIPO ESTADO","ESTADO AFILIADO",
                "MATRICULA PRESTATARIO", "CI PRESTATARIO","EXP PRESTATARIO", "1ER NOMBRE", "2DO NOMBRE", "APELLIDO PATERNO", "APELLIDO MATERNO","APELLIDO DE CASADA", "SALDO ACTUAL", "CUOTA FIJA MENSUAL", "DESCUENTO PROGRAMADO", "INTERES","Amort. TIT o GAR?","***",
                "GAR Estado","GAR Tipo de estado","Matricula garante","GAR CI", "GAR Exp","GAR Primer Nombre","GAR Segundo Nombre",
                "GAR 1er Apellido","GAR 2do Apellido","GAR Apellido de Casada","GAR Cuota fija","GAR descuento","***","GAR2 Estado","GAR2 Tipo de estado","Matricula garante","GAR2 CI", "GAR2 Exp","GAR2 Primer Nombre","GAR2 Segundo Nombre",
                "GAR2 1er Apellido","GAR2 2do Apellido","GAR2 Apellido de Casada","GAR2 Cuota fija","GAR2 descuento")
            );
         $command_sheet_later=array(
            array("CI AFILIADO","EXP AFILIADO","MATRICULA AFILIADO","NOMBRE COMPLETO AFILIADO","***",
            "COD PRESTAMO","MODALIDAD","SUB MODALIDAD", "FECHA DE DESEMBOLSO", "DPTO", "TIPO ESTADO","ESTADO AFILIADO",
            "MATRICULA PRESTATARIO", "CI PRESTATARIO","EXP PRESTATARIO", "1ER NOMBRE", "2DO NOMBRE", "APELLIDO PATERNO", "APELLIDO MATERNO","APELLIDO DE CASADA", "SALDO ACTUAL", "CUOTA FIJA MENSUAL", "DESCUENTO PROGRAMADO", "INTERES","Amort. TIT o GAR?","***",
            "GAR Estado","GAR Tipo de estado","Matricula garante","GAR CI", "GAR Exp","GAR Primer Nombre","GAR Segundo Nombre",
            "GAR 1er Apellido","GAR 2do Apellido","GAR Apellido de Casada","GAR Cuota fija","GAR descuento","***","GAR2 Estado","GAR2 Tipo de estado","Matricula garante","GAR2 CI", "GAR2 Exp","GAR2 Primer Nombre","GAR2 Segundo Nombre",
            "GAR2 1er Apellido","GAR2 2do Apellido","GAR2 Apellido de Casada","GAR2 Cuota fija","GAR2 descuento")
        );
         $senasir_sheet_before=array(
            array("CI AFILIADO","EXP AFILIADO","MATRICULA AFILIADO","NOMBRE COMPLETO AFILIADO","***",
            "COD PRESTAMO","MODALIDAD","SUB MODALIDAD", "FECHA DE DESEMBOLSO", "DPTO", "TIPO ESTADO","ESTADO AFILIADO",
            "MATRICULA PRESTATARIO", "CI PRESTATARIO","EXP PRESTATARIO", "1ER NOMBRE", "2DO NOMBRE", "APELLIDO PATERNO", "APELLIDO MATERNO","APELLIDO DE CASADA", "SALDO ACTUAL", "CUOTA FIJA MENSUAL", "DESCUENTO PROGRAMADO", "INTERES","Amort. TIT o GAR?","***",
            "GAR Estado","GAR Tipo de estado","Matricula garante","GAR CI", "GAR Exp","GAR Primer Nombre","GAR Segundo Nombre",
            "GAR 1er Apellido","GAR 2do Apellido","GAR Apellido de Casada","GAR Cuota fija","GAR descuento","***","GAR2 Estado","GAR2 Tipo de estado","Matricula garante","GAR2 CI", "GAR2 Exp","GAR2 Primer Nombre","GAR2 Segundo Nombre",
            "GAR2 1er Apellido","GAR2 2do Apellido","GAR2 Apellido de Casada","GAR2 Cuota fija","GAR2 descuento")
        );
         $senasir_sheet_later=array(
            array("CI AFILIADO","EXP AFILIADO","MATRICULA AFILIADO","NOMBRE COMPLETO AFILIADO","***",
            "COD PRESTAMO","MODALIDAD","SUB MODALIDAD", "FECHA DE DESEMBOLSO", "DPTO", "TIPO ESTADO","ESTADO AFILIADO",
            "MATRICULA PRESTATARIO", "CI PRESTATARIO","EXP PRESTATARIO", "1ER NOMBRE", "2DO NOMBRE", "APELLIDO PATERNO", "APELLIDO MATERNO","APELLIDO DE CASADA", "SALDO ACTUAL", "CUOTA FIJA MENSUAL", "DESCUENTO PROGRAMADO", "INTERES","Amort. TIT o GAR?","***",
            "GAR Estado","GAR Tipo de estado","Matricula garante","GAR CI", "GAR Exp","GAR Primer Nombre","GAR Segundo Nombre",
            "GAR 1er Apellido","GAR 2do Apellido","GAR Apellido de Casada","GAR Cuota fija","GAR descuento","***","GAR2 Estado","GAR2 Tipo de estado","Matricula garante","GAR2 CI", "GAR2 Exp","GAR2 Primer Nombre","GAR2 Segundo Nombre",
            "GAR2 1er Apellido","GAR2 2do Apellido","GAR2 Apellido de Casada","GAR2 Cuota fija","GAR2 descuento")
        );

         $command_ancient=array(
            array("CI AFILIADO","EXP AFILIADO","MATRICULA AFILIADO","NOMBRE COMPLETO AFILIADO","***",
            "COD PRESTAMO","MODALIDAD","SUB MODALIDAD", "FECHA DE DESEMBOLSO", "DPTO", "TIPO ESTADO","ESTADO AFILIADO",
            "MATRICULA PRESTATARIO", "CI PRESTATARIO","EXP PRESTATARIO", "1ER NOMBRE", "2DO NOMBRE", "APELLIDO PATERNO", "APELLIDO MATERNO","APELLIDO DE CASADA", "SALDO ACTUAL", "CUOTA FIJA MENSUAL", "DESCUENTO PROGRAMADO", "INTERES","Amort. TIT o GAR?","***",
            "GAR Estado","GAR Tipo de estado","Matricula garante","GAR CI", "GAR Exp","GAR Primer Nombre","GAR Segundo Nombre",
            "GAR 1er Apellido","GAR 2do Apellido","GAR Apellido de Casada","GAR Cuota fija","GAR descuento","***","GAR2 Estado","GAR2 Tipo de estado","Matricula garante","GAR2 CI", "GAR2 Exp","GAR2 Primer Nombre","GAR2 Segundo Nombre",
            "GAR2 1er Apellido","GAR2 2do Apellido","GAR2 Apellido de Casada","GAR2 Cuota fija","GAR2 descuento")
        );
          $senasir_ancient=array(
            array("CI AFILIADO","EXP AFILIADO","MATRICULA AFILIADO","NOMBRE COMPLETO AFILIADO","***",
            "COD PRESTAMO","MODALIDAD","SUB MODALIDAD", "FECHA DE DESEMBOLSO", "DPTO", "TIPO ESTADO","ESTADO AFILIADO",
            "MATRICULA PRESTATARIO", "CI PRESTATARIO","EXP PRESTATARIO", "1ER NOMBRE", "2DO NOMBRE", "APELLIDO PATERNO", "APELLIDO MATERNO","APELLIDO DE CASADA", "SALDO ACTUAL", "CUOTA FIJA MENSUAL", "DESCUENTO PROGRAMADO", "INTERES","Amort. TIT o GAR?","***",
            "GAR Estado","GAR Tipo de estado","Matricula garante","GAR CI", "GAR Exp","GAR Primer Nombre","GAR Segundo Nombre",
            "GAR 1er Apellido","GAR 2do Apellido","GAR Apellido de Casada","GAR Cuota fija","GAR descuento","***","GAR2 Estado","GAR2 Tipo de estado","Matricula garante","GAR2 CI", "GAR2 Exp","GAR2 Primer Nombre","GAR2 Segundo Nombre",
            "GAR2 1er Apellido","GAR2 2do Apellido","GAR2 Apellido de Casada","GAR2 Cuota fija","GAR2 descuento")
        );

         foreach($loans as $loan){
             if(Carbon::parse($loan->disbursement_date)->day <= LoanGlobalParameter::first()->offset_interest_day){
                 if(in_array($loan->procedure_modality_id, $id_comando))
                 {
                     foreach($loan->getBorrowers() as $lender)
                     {
                         array_push($command_sheet_before, array(
                            $lender->identity_card_affiliate,
                            $lender->city_exp_first_shortened_affiliate,
                            $lender->registration_affiliate,
                            $lender->full_name_affiliate,
                            $loan->guarantor_amortizing? '***' : '***',
                             $lender->code_loan,
                             $lender->sub_modality_loan,
                             $lender->shortened_sub_modality_loan,
                             Carbon::parse($lender->disbursement_date_loan)->format('d/m/Y H:i:s'),
                             $lender->city_loan,
                             $lender->state_type_affiliate,
                             $lender->state_affiliate,
                             $lender->registration_borrower,
                             $lender->identity_card_borrower,
                             $lender->city_exp_first_shortened_borrower,
                             $lender->first_name_borrower,
                             $lender->second_name_borrower,
                             $lender->last_name_borrower,
                             $lender->mothers_last_name_borrower,
                             $lender->surname_husband_borrower,
                             Util::money_format($loan->balance),
                             Util::money_format($loan->estimated_quota),
                             Util::money_format($loan->get_amount_payment($date_calculate,false,'T')),
                             $loan->interest->annual_interest,
                             $loan->guarantor_amortizing? 'Amort. Garante':'Amort. Titular',
                             $loan->guarantor_amortizing? '***' : '***',

                          /*  $loan->guarantor_amortizing? 'Amort. Garante':'Amort. Titular',
                            $loan->guarantor_amortizing? '***' : '***',
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
                            $loan->guarantor_amortizing? Util::money_format($loan->get_amount_payment($date_calculate,false,'G')) : '',
                            $loan->guarantor_amortizing? '***' : '***',
                            $loan->guarantor_amortizing?  (count($loan->guarantors)==2? ($loan->guarantors[1]->affiliate_state->affiliate_state_type->name):('SIN')):'',
                            $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->affiliate_state->name) : ('SIN')):'',
                            $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->registration) : ('SIN')):'',
                            $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->identity_card) : ('SIN')):'',
                            $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->city_identity_card->first_shortened) : ('SIN')):'',
                            $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->first_name) : ('SIN')):'',
                            $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->second_name) : ('SIN')):'',
                            $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->last_name) : ('SIN')):'',
                            $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->mothers_last_name) : ('SIN')):'',
                            $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->surname_husband) : ('SIN')):'',
                            $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->pivot->quota_treat) : ('SIN')):'',
                            $loan->guarantor_amortizing? (count($loan->guarantors)==2? (Util::money_format($loan->get_amount_payment($estimated_date=null,false,'G'))) : ('SIN')):'',
                        */
                        ));
                     }
                 }
                 else{
                     if(in_array($loan->procedure_modality_id, $id_senasir))
                     {
                         foreach($loan->getBorrowers() as $lender)
                         {
                            array_push($senasir_sheet_before, array(
                                $lender->identity_card_affiliate,
                                $lender->city_exp_first_shortened_affiliate,
                                $lender->registration_affiliate,
                                $lender->full_name_affiliate,
                                $loan->guarantor_amortizing? '***' : '***',
                                 $lender->code_loan,
                                 $lender->sub_modality_loan,
                                 $lender->shortened_sub_modality_loan,
                                 Carbon::parse($lender->disbursement_date_loan)->format('d/m/Y H:i:s'),
                                 $lender->city_loan,
                                 $lender->state_type_affiliate,
                                 $lender->state_affiliate,
                                 $lender->registration_borrower,
                                 $lender->identity_card_borrower,
                                 $lender->city_exp_first_shortened_borrower,
                                 $lender->first_name_borrower,
                                 $lender->second_name_borrower,
                                 $lender->last_name_borrower,
                                 $lender->mothers_last_name_borrower,
                                 $lender->surname_husband_borrower,
                                 Util::money_format($loan->balance),
                                 Util::money_format($loan->estimated_quota),
                                 Util::money_format($loan->get_amount_payment($date_calculate,false,'T')),
                                 $loan->interest->annual_interest,
                                 $loan->guarantor_amortizing? 'Amort. Garante':'Amort. Titular',
                                 $loan->guarantor_amortizing? '***' : '***',
    
                              /*  $loan->guarantor_amortizing? 'Amort. Garante':'Amort. Titular',
                                $loan->guarantor_amortizing? '***' : '***',
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
                                $loan->guarantor_amortizing? Util::money_format($loan->get_amount_payment($date_calculate,false,'G')) : '',
                                $loan->guarantor_amortizing? '***' : '***',
                                $loan->guarantor_amortizing?  (count($loan->guarantors)==2? ($loan->guarantors[1]->affiliate_state->affiliate_state_type->name):('SIN')):'',
                                $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->affiliate_state->name) : ('SIN')):'',
                                $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->registration) : ('SIN')):'',
                                $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->identity_card) : ('SIN')):'',
                                $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->city_identity_card->first_shortened) : ('SIN')):'',
                                $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->first_name) : ('SIN')):'',
                                $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->second_name) : ('SIN')):'',
                                $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->last_name) : ('SIN')):'',
                                $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->mothers_last_name) : ('SIN')):'',
                                $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->surname_husband) : ('SIN')):'',
                                $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->pivot->quota_treat) : ('SIN')):'',
                                $loan->guarantor_amortizing? (count($loan->guarantors)==2? (Util::money_format($loan->get_amount_payment($estimated_date=null,false,'G'))) : ('SIN')):'',
                            */
                            ));
                         }
                     }
                 }
             }
         }
         $sub_month = Carbon::parse($request->date)->subMonth()->format('m');
         $loans_before = Loan::whereMonth('disbursement_date', $sub_month)->whereYear('disbursement_date', $year)->orderBy('disbursement_date')->get();//considerar caso fin de año
         foreach($loans_before as $loan){
             if(Carbon::parse($loan->disbursement_date)->day > LoanGlobalParameter::first()->offset_interest_day){
                 if(in_array($loan->procedure_modality_id, $id_comando))
                 {
                     foreach($loan->getBorrowers() as $lender)
                     {
                        array_push($command_sheet_later, array(
                            $lender->identity_card_affiliate,
                            $lender->city_exp_first_shortened_affiliate,
                            $lender->registration_affiliate,
                            $lender->full_name_affiliate,
                            $loan->guarantor_amortizing? '***' : '***',
                             $lender->code_loan,
                             $lender->sub_modality_loan,
                             $lender->shortened_sub_modality_loan,
                             Carbon::parse($lender->disbursement_date_loan)->format('d/m/Y H:i:s'),
                             $lender->city_loan,
                             $lender->state_type_affiliate,
                             $lender->state_affiliate,
                             $lender->registration_borrower,
                             $lender->identity_card_borrower,
                             $lender->city_exp_first_shortened_borrower,
                             $lender->first_name_borrower,
                             $lender->second_name_borrower,
                             $lender->last_name_borrower,
                             $lender->mothers_last_name_borrower,
                             $lender->surname_husband_borrower,
                             Util::money_format($loan->balance),
                             Util::money_format($loan->estimated_quota),
                             Util::money_format($loan->get_amount_payment($date_calculate,false,'T')),
                             $loan->interest->annual_interest,
                             $loan->guarantor_amortizing? 'Amort. Garante':'Amort. Titular',
                             $loan->guarantor_amortizing? '***' : '***',

                          /*  $loan->guarantor_amortizing? 'Amort. Garante':'Amort. Titular',
                            $loan->guarantor_amortizing? '***' : '***',
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
                            $loan->guarantor_amortizing? Util::money_format($loan->get_amount_payment($date_calculate,false,'G')) : '',
                            $loan->guarantor_amortizing? '***' : '***',
                            $loan->guarantor_amortizing?  (count($loan->guarantors)==2? ($loan->guarantors[1]->affiliate_state->affiliate_state_type->name):('SIN')):'',
                            $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->affiliate_state->name) : ('SIN')):'',
                            $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->registration) : ('SIN')):'',
                            $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->identity_card) : ('SIN')):'',
                            $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->city_identity_card->first_shortened) : ('SIN')):'',
                            $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->first_name) : ('SIN')):'',
                            $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->second_name) : ('SIN')):'',
                            $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->last_name) : ('SIN')):'',
                            $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->mothers_last_name) : ('SIN')):'',
                            $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->surname_husband) : ('SIN')):'',
                            $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->pivot->quota_treat) : ('SIN')):'',
                            $loan->guarantor_amortizing? (count($loan->guarantors)==2? (Util::money_format($loan->get_amount_payment($estimated_date=null,false,'G'))) : ('SIN')):'',
                        */
                        ));
                     }
                 }
                 else{
                     if(in_array($loan->procedure_modality_id, $id_senasir))
                     {
                         foreach($loan->getBorrowers() as $lender)
                         {
                            array_push($senasir_sheet_later, array(
                                $lender->identity_card_affiliate,
                                $lender->city_exp_first_shortened_affiliate,
                                $lender->registration_affiliate,
                                $lender->full_name_affiliate,
                                $loan->guarantor_amortizing? '***' : '***',
                                 $lender->code_loan,
                                 $lender->sub_modality_loan,
                                 $lender->shortened_sub_modality_loan,
                                 Carbon::parse($lender->disbursement_date_loan)->format('d/m/Y H:i:s'),
                                 $lender->city_loan,
                                 $lender->state_type_affiliate,
                                 $lender->state_affiliate,
                                 $lender->registration_borrower,
                                 $lender->identity_card_borrower,
                                 $lender->city_exp_first_shortened_borrower,
                                 $lender->first_name_borrower,
                                 $lender->second_name_borrower,
                                 $lender->last_name_borrower,
                                 $lender->mothers_last_name_borrower,
                                 $lender->surname_husband_borrower,
                                 Util::money_format($loan->balance),
                                 Util::money_format($loan->estimated_quota),
                                 Util::money_format($loan->get_amount_payment($date_calculate,false,'T')),
                                 $loan->interest->annual_interest,
                                 $loan->guarantor_amortizing? 'Amort. Garante':'Amort. Titular',
                                 $loan->guarantor_amortizing? '***' : '***',
    
                              /*  $loan->guarantor_amortizing? 'Amort. Garante':'Amort. Titular',
                                $loan->guarantor_amortizing? '***' : '***',
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
                                $loan->guarantor_amortizing? Util::money_format($loan->get_amount_payment($date_calculate,false,'G')) : '',
                                $loan->guarantor_amortizing? '***' : '***',
                                $loan->guarantor_amortizing?  (count($loan->guarantors)==2? ($loan->guarantors[1]->affiliate_state->affiliate_state_type->name):('SIN')):'',
                                $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->affiliate_state->name) : ('SIN')):'',
                                $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->registration) : ('SIN')):'',
                                $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->identity_card) : ('SIN')):'',
                                $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->city_identity_card->first_shortened) : ('SIN')):'',
                                $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->first_name) : ('SIN')):'',
                                $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->second_name) : ('SIN')):'',
                                $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->last_name) : ('SIN')):'',
                                $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->mothers_last_name) : ('SIN')):'',
                                $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->surname_husband) : ('SIN')):'',
                                $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->pivot->quota_treat) : ('SIN')):'',
                                $loan->guarantor_amortizing? (count($loan->guarantors)==2? (Util::money_format($loan->get_amount_payment($estimated_date=null,false,'G'))) : ('SIN')):'',
                            */
                            ));
                         }
                     }
                 }
             }
         }
         foreach($loans_request as $loan){
              if(in_array($loan->procedure_modality_id, $id_comando))
              {
                  foreach($loan->getBorrowers() as $lender)
                  {
                    array_push($command_ancient, array(
                        $lender->identity_card_affiliate,
                        $lender->city_exp_first_shortened_affiliate,
                        $lender->registration_affiliate,
                        $lender->full_name_affiliate,
                        $loan->guarantor_amortizing? '***' : '***',
                         $lender->code_loan,
                         $lender->sub_modality_loan,
                         $lender->shortened_sub_modality_loan,
                         Carbon::parse($lender->disbursement_date_loan)->format('d/m/Y H:i:s'),
                         $lender->city_loan,
                         $lender->state_type_affiliate,
                         $lender->state_affiliate,
                         $lender->registration_borrower,
                         $lender->identity_card_borrower,
                         $lender->city_exp_first_shortened_borrower,
                         $lender->first_name_borrower,
                         $lender->second_name_borrower,
                         $lender->last_name_borrower,
                         $lender->mothers_last_name_borrower,
                         $lender->surname_husband_borrower,
                         Util::money_format($loan->balance),
                         Util::money_format($loan->estimated_quota),
                         Util::money_format($loan->get_amount_payment($date_calculate,false,'T')),
                         $loan->interest->annual_interest,
                         $loan->guarantor_amortizing? 'Amort. Garante':'Amort. Titular',
                         $loan->guarantor_amortizing? '***' : '***',

                      /*  $loan->guarantor_amortizing? 'Amort. Garante':'Amort. Titular',
                        $loan->guarantor_amortizing? '***' : '***',
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
                        $loan->guarantor_amortizing? Util::money_format($loan->get_amount_payment($date_calculate,false,'G')) : '',
                        $loan->guarantor_amortizing? '***' : '***',
                        $loan->guarantor_amortizing?  (count($loan->guarantors)==2? ($loan->guarantors[1]->affiliate_state->affiliate_state_type->name):('SIN')):'',
                        $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->affiliate_state->name) : ('SIN')):'',
                        $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->registration) : ('SIN')):'',
                        $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->identity_card) : ('SIN')):'',
                        $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->city_identity_card->first_shortened) : ('SIN')):'',
                        $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->first_name) : ('SIN')):'',
                        $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->second_name) : ('SIN')):'',
                        $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->last_name) : ('SIN')):'',
                        $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->mothers_last_name) : ('SIN')):'',
                        $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->surname_husband) : ('SIN')):'',
                        $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->pivot->quota_treat) : ('SIN')):'',
                        $loan->guarantor_amortizing? (count($loan->guarantors)==2? (Util::money_format($loan->get_amount_payment($estimated_date=null,false,'G'))) : ('SIN')):'',
                    */
                    ));
                  }
              }
              else{
                  if(in_array($loan->procedure_modality_id, $id_senasir))
                  {
                      foreach($loan->getBorrowers() as $lender)
                      {
                        array_push($senasir_ancient, array(
                            $lender->identity_card_affiliate,
                            $lender->city_exp_first_shortened_affiliate,
                            $lender->registration_affiliate,
                            $lender->full_name_affiliate,
                            $loan->guarantor_amortizing? '***' : '***',
                             $lender->code_loan,
                             $lender->sub_modality_loan,
                             $lender->shortened_sub_modality_loan,
                             Carbon::parse($lender->disbursement_date_loan)->format('d/m/Y H:i:s'),
                             $lender->city_loan,
                             $lender->state_type_affiliate,
                             $lender->state_affiliate,
                             $lender->registration_borrower,
                             $lender->identity_card_borrower,
                             $lender->city_exp_first_shortened_borrower,
                             $lender->first_name_borrower,
                             $lender->second_name_borrower,
                             $lender->last_name_borrower,
                             $lender->mothers_last_name_borrower,
                             $lender->surname_husband_borrower,
                             Util::money_format($loan->balance),
                             Util::money_format($loan->estimated_quota),
                             Util::money_format($loan->get_amount_payment($date_calculate,false,'T')),
                             $loan->interest->annual_interest,
                             $loan->guarantor_amortizing? 'Amort. Garante':'Amort. Titular',
                             $loan->guarantor_amortizing? '***' : '***',

                          /*  $loan->guarantor_amortizing? 'Amort. Garante':'Amort. Titular',
                            $loan->guarantor_amortizing? '***' : '***',
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
                            $loan->guarantor_amortizing? Util::money_format($loan->get_amount_payment($date_calculate,false,'G')) : '',
                            $loan->guarantor_amortizing? '***' : '***',
                            $loan->guarantor_amortizing?  (count($loan->guarantors)==2? ($loan->guarantors[1]->affiliate_state->affiliate_state_type->name):('SIN')):'',
                            $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->affiliate_state->name) : ('SIN')):'',
                            $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->registration) : ('SIN')):'',
                            $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->identity_card) : ('SIN')):'',
                            $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->city_identity_card->first_shortened) : ('SIN')):'',
                            $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->first_name) : ('SIN')):'',
                            $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->second_name) : ('SIN')):'',
                            $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->last_name) : ('SIN')):'',
                            $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->mothers_last_name) : ('SIN')):'',
                            $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->surname_husband) : ('SIN')):'',
                            $loan->guarantor_amortizing? (count($loan->guarantors)==2? ($loan->guarantors[1]->pivot->quota_treat) : ('SIN')):'',
                            $loan->guarantor_amortizing? (count($loan->guarantors)==2? (Util::money_format($loan->get_amount_payment($estimated_date=null,false,'G'))) : ('SIN')):'',
                        */
                        ));
                      }
                  }
              }
      }

         $file_name = $month.'-'.$year;
         $extension = '.xls';
         $export = new FileWithMultipleSheetsReport($command_sheet_later, $command_sheet_before, $senasir_sheet_later, $senasir_sheet_before,$command_ancient,$senasir_ancient);
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
         $date_calculate = Carbon::now()->endOfMonth()->format('Y-m-d');
         $command_sheet_dafaulted=array(
             array("CI AFILIADO","EXP AFILIADO","MATRICULA AFILIADO","NOMBRE COMPLETO AFILIADO","***",
             "COD PRESTAMO", "FECHA DE DESEMBOLSO", "DPTO", "TIPO ESTADO","ESTADO AFILIADO",
             "MATRICULA PRESTATARIO", "CI PRESTATARIO","EXP PRESTATARIO", "1ER NOMBRE", "2DO NOMBRE", "APELLIDO PATERNO", "APELLIDO MATERNO","APELLIDO DE CASADA", "SALDO ACTUAL", "CUOTA FIJA MENSUAL", "DESCUENTO PROGRAMADO", "INTERES","Amort. TIT o GAR?","***",
             "CI AFILIADO","EXP AFILIADO","MATRICULA AFILIADO","NOMBRE COMPLETO AFILIADO","***",
             "GAR Estado","GAR Tipo de estado","Matricula garante","GAR CI", "GAR Exp","GAR Primer Nombre","GAR Segundo Nombre",
             "GAR 1er Apellido","GAR 2do Apellido","GAR Apellido de Casada","GAR Cuota fija","GAR descuento","***",
             "CI AFILIADO","EXP AFILIADO","MATRICULA AFILIADO","NOMBRE COMPLETO AFILIADO","***","GAR2 Estado","GAR2 Tipo de estado","Matricula garante","GAR2 CI", "GAR2 Exp","GAR2 Primer Nombre","GAR2 Segundo Nombre",
             "GAR2 1er Apellido","GAR2 2do Apellido","GAR2 Apellido de Casada","GAR2 Cuota fija","GAR2 descuento")
         );
         $senasir_sheet_defaulted=array(
             array("CI AFILIADO","EXP AFILIADO","MATRICULA AFILIADO","NOMBRE COMPLETO AFILIADO","***",
             "COD PRESTAMO", "FECHA DE DESEMBOLSO", "DPTO", "TIPO ESTADO","ESTADO AFILIADO",
             "MATRICULA PRESTATARIO", "CI PRESTATARIO","EXP PRESTATARIO", "1ER NOMBRE", "2DO NOMBRE", "APELLIDO PATERNO", "APELLIDO MATERNO","APELLIDO DE CASADA", "SALDO ACTUAL", "CUOTA FIJA MENSUAL", "DESCUENTO PROGRAMADO", "INTERES","Amort. TIT o GAR?","***",
             "CI AFILIADO","EXP AFILIADO","MATRICULA AFILIADO","NOMBRE COMPLETO AFILIADO","***",
             "GAR Estado","GAR Tipo de estado","Matricula garante","GAR CI", "GAR Exp","GAR Primer Nombre","GAR Segundo Nombre",
             "GAR 1er Apellido","GAR 2do Apellido","GAR Apellido de Casada","GAR Cuota fija","GAR descuento","***",
             "CI AFILIADO","EXP AFILIADO","MATRICULA AFILIADO","NOMBRE COMPLETO AFILIADO","***","GAR2 Estado","GAR2 Tipo de estado","Matricula garante","GAR2 CI", "GAR2 Exp","GAR2 Primer Nombre","GAR2 Segundo Nombre",
             "GAR2 1er Apellido","GAR2 2do Apellido","GAR2 Apellido de Casada","GAR2 Cuota fija","GAR2 descuento")
         );
         $id_senasir = array();
        foreach(ProcedureModality::where('name', 'like', '%SENASIR')->get() as $procedure)
             array_push($id_senasir, $procedure->id);
        $id_comando = array();
        foreach(ProcedureModality::where('name', 'like', '%Activo')->orWhere('name', 'like', '%Activo%')->get() as $procedure)
             array_push($id_comando, $procedure->id);
 
         foreach($loans as $loan){
            // return $loan;
             if(in_array($loan->procedure_modality_id, $id_comando))
             {
                 foreach($loan->lenders as $lender)
                 { 
                      $loan->guarantor = $loan->guarantors;
                     array_push($command_sheet_dafaulted, array(  
                        $loan->getBorrowers()->where('id_affiliate',$loan->lenders[0]->id)->first()->identity_card_affiliate,
                        $loan->getBorrowers()->where('id_affiliate',$loan->lenders[0]->id)->first()->city_exp_first_shortened_affiliate,
                        $loan->getBorrowers()->where('id_affiliate',$loan->lenders[0]->id)->first()->registration_affiliate,
                        $loan->getBorrowers()->where('id_affiliate',$loan->lenders[0]->id)->first()->full_name_affiliate,
                        "*Prestatario-->",
                        $loan->code,
                        Carbon::parse($loan->disbursement_date)->format('d/m/Y H:i:s'),
                        $loan->city->name,
                        $loan->getBorrowers()->where('id_affiliate',$loan->lenders[0]->id)->first()->state_affiliate,
                        $loan->getBorrowers()->where('id_affiliate',$loan->lenders[0]->id)->first()->state_type_affiliate,
                        $loan->getBorrowers()->where('id_affiliate',$loan->lenders[0]->id)->first()->registration_borrower,
                        $loan->getBorrowers()->where('id_affiliate',$loan->lenders[0]->id)->first()->identity_card_borrower,
                        $loan->getBorrowers()->where('id_affiliate',$loan->lenders[0]->id)->first()->city_exp_first_shortened_borrower,
                        $loan->getBorrowers()->where('id_affiliate',$loan->lenders[0]->id)->first()->first_name_borrower,
                        $loan->getBorrowers()->where('id_affiliate',$loan->lenders[0]->id)->first()->second_name_borrower,
                        $loan->getBorrowers()->where('id_affiliate',$loan->lenders[0]->id)->first()->last_name_borrower,
                        $loan->getBorrowers()->where('id_affiliate',$loan->lenders[0]->id)->first()->mothers_last_name_borrower,
                        $loan->getBorrowers()->where('id_affiliate',$loan->lenders[0]->id)->first()->surname_husband_borrower,
                        Util::money_format($loan->balance),
                        Util::money_format($lender->pivot->quota_treat),
                        Util::money_format($loan->get_amount_payment($date_calculate,false,'T')),
                        $loan->interest->annual_interest,
                       // $loan->getBorrowers()->where('id_affiliate',$loan->lenders[0]->id)->first()->indebtedness_borrower,
                        
                        $loan->guarantor_amortizing? 'Amort. Garante':'Amort. Titular',
                        "*Titular-->*",
                        $loan->guarantor_amortizing ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[0]->id)->first()->identity_card_affiliate : '',
                        $loan->guarantor_amortizing ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[0]->id)->first()->city_exp_first_shortened_affiliate : '',
                        $loan->guarantor_amortizing ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[0]->id)->first()->registration_affiliate : '',
                        $loan->guarantor_amortizing ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[0]->id)->first()->full_name_affiliate : '',
                        "*garante-->*",
                        $loan->guarantor_amortizing ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[0]->id)->first()->state_affiliate : '',
                        $loan->guarantor_amortizing ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[0]->id)->first()->state_type_affiliate : '',
                        $loan->guarantor_amortizing ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[0]->id)->first()->registration_borrower : '',
                        $loan->guarantor_amortizing ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[0]->id)->first()->identity_card_borrower : '',
                        $loan->guarantor_amortizing ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[0]->id)->first()->city_exp_first_shortened_borrower : '',
                        $loan->guarantor_amortizing ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[0]->id)->first()->first_name_borrower : '',
                        $loan->guarantor_amortizing ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[0]->id)->first()->second_name_borrower : '',
                        $loan->guarantor_amortizing ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[0]->id)->first()->last_name_borrower : '',
                        $loan->guarantor_amortizing ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[0]->id)->first()->mothers_last_name_borrower : '',
                        $loan->guarantor_amortizing ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[0]->id)->first()->surname_husband_borrower : '',
                        $loan->guarantor_amortizing ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[0]->id)->first()->quota_loan : '',
                        $loan->guarantor_amortizing ? Util::money_format($loan->get_amount_payment($date_calculate,false,'G')) : '',
                        "*Titular-->",
                        isset($loan->guarantor[1]) ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[1]->id)->first()->identity_card_affiliate : '',
                        isset($loan->guarantor[1]) ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[1]->id)->first()->city_exp_first_shortened_affiliate : '',
                        isset($loan->guarantor[1]) ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[1]->id)->first()->registration_affiliate : '',
                        isset($loan->guarantor[1]) ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[1]->id)->first()->full_name_affiliate : '',
                        "*garante-->*",
                        isset($loan->guarantor[1]) ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[1]->id)->first()->state_affiliate: '',
                        isset($loan->guarantor[1]) ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[1]->id)->first()->state_type_affiliate: '',
                        isset($loan->guarantor[1]) ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[1]->id)->first()->registration_borrower : '',
                        isset($loan->guarantor[1]) ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[1]->id)->first()->identity_card_borrower : '',
                        isset($loan->guarantor[1]) ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[1]->id)->first()->city_exp_first_shortened_borrower : '',
                        isset($loan->guarantor[1]) ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[1]->id)->first()->first_name_borrower : '',
                        isset($loan->guarantor[1]) ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[1]->id)->first()->second_name_borrower : '',
                        isset($loan->guarantor[1]) ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[1]->id)->first()->last_name_borrower : '',
                        isset($loan->guarantor[1]) ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[1]->id)->first()->mothers_last_name_borrower : '',
                        isset($loan->guarantor[1]) ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[1]->id)->first()->surname_husband_borrower : '',
                        isset($loan->guarantor[1]) ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[1]->id)->first()->quota_loan : '',
                        isset($loan->guarantor[1]) ? Util::money_format($loan->get_amount_payment($date_calculate,false,'G')): '',
                     ));
                 }              
             }
             if(in_array($loan->procedure_modality_id, $id_senasir))
             {
                foreach($loan->lenders as $lender)
                { 
                     $loan->guarantor = $loan->guarantors;
                    array_push($senasir_sheet_defaulted, array(  
                       $loan->getBorrowers()->where('id_affiliate',$loan->lenders[0]->id)->first()->identity_card_affiliate,
                       $loan->getBorrowers()->where('id_affiliate',$loan->lenders[0]->id)->first()->city_exp_first_shortened_affiliate,
                       $loan->getBorrowers()->where('id_affiliate',$loan->lenders[0]->id)->first()->registration_affiliate,
                       $loan->getBorrowers()->where('id_affiliate',$loan->lenders[0]->id)->first()->full_name_affiliate,
                       "*Prestatario-->",
                       $loan->code,
                       Carbon::parse($loan->disbursement_date)->format('d/m/Y H:i:s'),
                       $loan->city->name,
                       $loan->getBorrowers()->where('id_affiliate',$loan->lenders[0]->id)->first()->state_affiliate,
                       $loan->getBorrowers()->where('id_affiliate',$loan->lenders[0]->id)->first()->state_type_affiliate,
                       $loan->getBorrowers()->where('id_affiliate',$loan->lenders[0]->id)->first()->registration_borrower,
                       $loan->getBorrowers()->where('id_affiliate',$loan->lenders[0]->id)->first()->identity_card_borrower,
                       $loan->getBorrowers()->where('id_affiliate',$loan->lenders[0]->id)->first()->city_exp_first_shortened_borrower,
                       $loan->getBorrowers()->where('id_affiliate',$loan->lenders[0]->id)->first()->first_name_borrower,
                       $loan->getBorrowers()->where('id_affiliate',$loan->lenders[0]->id)->first()->second_name_borrower,
                       $loan->getBorrowers()->where('id_affiliate',$loan->lenders[0]->id)->first()->last_name_borrower,
                       $loan->getBorrowers()->where('id_affiliate',$loan->lenders[0]->id)->first()->mothers_last_name_borrower,
                       $loan->getBorrowers()->where('id_affiliate',$loan->lenders[0]->id)->first()->surname_husband_borrower,
                       Util::money_format($loan->balance),
                       Util::money_format($lender->pivot->quota_treat),
                       Util::money_format($loan->get_amount_payment($date_calculate,false,'T')),
                       $loan->interest->annual_interest,
                      // $loan->getBorrowers()->where('id_affiliate',$loan->lenders[0]->id)->first()->indebtedness_borrower,
                       
                       $loan->guarantor_amortizing? 'Amort. Garante':'Amort. Titular',
                       "*Titular-->*",
                       $loan->guarantor_amortizing ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[0]->id)->first()->identity_card_affiliate : '',
                       $loan->guarantor_amortizing ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[0]->id)->first()->city_exp_first_shortened_affiliate : '',
                       $loan->guarantor_amortizing ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[0]->id)->first()->registration_affiliate : '',
                       $loan->guarantor_amortizing ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[0]->id)->first()->full_name_affiliate : '',
                       "*garante-->*",
                       $loan->guarantor_amortizing ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[0]->id)->first()->state_affiliate : '',
                       $loan->guarantor_amortizing ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[0]->id)->first()->state_type_affiliate : '',
                       $loan->guarantor_amortizing ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[0]->id)->first()->registration_borrower : '',
                       $loan->guarantor_amortizing ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[0]->id)->first()->identity_card_borrower : '',
                       $loan->guarantor_amortizing ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[0]->id)->first()->city_exp_first_shortened_borrower : '',
                       $loan->guarantor_amortizing ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[0]->id)->first()->first_name_borrower : '',
                       $loan->guarantor_amortizing ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[0]->id)->first()->second_name_borrower : '',
                       $loan->guarantor_amortizing ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[0]->id)->first()->last_name_borrower : '',
                       $loan->guarantor_amortizing ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[0]->id)->first()->mothers_last_name_borrower : '',
                       $loan->guarantor_amortizing ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[0]->id)->first()->surname_husband_borrower : '',
                       $loan->guarantor_amortizing ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[0]->id)->first()->quota_loan : '',
                       $loan->guarantor_amortizing ? Util::money_format($loan->get_amount_payment($date_calculate,false,'G')) : '',
                       "*Titular-->*",
                       isset($loan->guarantor[1]) ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[1]->id)->first()->identity_card_affiliate : '',
                       isset($loan->guarantor[1]) ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[1]->id)->first()->city_exp_first_shortened_affiliate : '',
                       isset($loan->guarantor[1]) ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[1]->id)->first()->registration_affiliate : '',
                       isset($loan->guarantor[1]) ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[1]->id)->first()->full_name_affiliate : '',
                       "*garante-->*",
                       isset($loan->guarantor[1]) ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[1]->id)->first()->state_affiliate: '',
                       isset($loan->guarantor[1]) ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[1]->id)->first()->state_type_affiliate: '',
                       isset($loan->guarantor[1]) ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[1]->id)->first()->registration_borrower : '',
                       isset($loan->guarantor[1]) ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[1]->id)->first()->identity_card_borrower : '',
                       isset($loan->guarantor[1]) ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[1]->id)->first()->city_exp_first_shortened_borrower : '',
                       isset($loan->guarantor[1]) ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[1]->id)->first()->first_name_borrower : '',
                       isset($loan->guarantor[1]) ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[1]->id)->first()->second_name_borrower : '',
                       isset($loan->guarantor[1]) ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[1]->id)->first()->last_name_borrower : '',
                       isset($loan->guarantor[1]) ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[1]->id)->first()->mothers_last_name_borrower : '',
                       isset($loan->guarantor[1]) ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[1]->id)->first()->surname_husband_borrower : '',
                       isset($loan->guarantor[1]) ? $loan->getGuarantors()->where('id_affiliate',$loan->guarantor[1]->id)->first()->quota_loan : '',
                       isset($loan->guarantor[1]) ? Util::money_format($loan->get_amount_payment($date_calculate,false,'G')): '',
                    ));
                }                 
        }
        }
         $file_name = $month.'-'.$year;
         $extension = '.xls';
         $export = new FileWithMultipleSheetsDefaulted($command_sheet_dafaulted, $senasir_sheet_defaulted);
         return Excel::download($export, $file_name.$extension);
    }
   //seguimiento de prestamos
    /** @group Reportes de Prestamos
   * Seguimiento de prestamos
   * Lista todos los prestamos con opcion a busquedas
   * @queryParam sortDesc Vector de orden descendente(0) o ascendente(1). Example: 0
   * @queryParam trashed_loan Para filtrar ANULADOS(true) o estados Vigente,Liq,En Proceso(false). Example: true
   * @queryParam per_page Número de datos por página. Example: 8
   * @queryParam page Número de página. Example: 1
   * @queryParam excel Valor booleano para descargar  el docExcel. Example: true
   * @queryParam code_loan  Buscar código del Préstamo. Example: PTMO000012-2021
   * @queryParam identity_card_borrower  Buscar por nro de CI del Prestatario. Example: 10069775
   * @queryParam registration_borrower  Buscar por Matricula del Prestatario. Example: 100697MDF
   * @queryParam last_name_borrower Buscar por primer apellido del Prestatario. Example: RIVERA
   * @queryParam mothers_last_name_borrower Buscar por segundo apellido del Prestatario. Example: ARTEAG
   * @queryParam first_name_borrower Buscar por primer Nombre del Prestatario. Example: ABAD
   * @queryParam second_name_borrower Buscar por segundo Nombre del Prestatario. Example: FAUST
   * @queryParam surname_husband_borrower Buscar por Apellido de casada Nombre del Prestatario. Example: De LA CRUZ
   * @queryParam full_name_borrower Buscar por nombre completo del Prestatario. Example: ANA CRUZ PEREZ
   * @queryParam full_name_affiliate Buscar por nombre completo del Afiliado. Example: ANA CRUZ PEREZ
   * @queryParam sub_modality_loan Buscar por sub modalidad del préstamo. Example: Corto plazo sector activo
   * @queryParam shortened_sub_modality_loan Buscar por nombre corto sub modalidad del préstamo. Example: COR-AFP
   * @queryParam modality_loan Buscar por Modalidad del prestamo. Example: Préstamo a corto plazo
   * @queryParam amount_approved_loan Buscar monto aprobado del afiliado. Example: 25000
   * @queryParam state_type_affiliate Buscar por tipo de estado del afiliado. Example: Activo
   * @queryParam state_affiliate Buscar por estado del affiliado. Example: Servicio
   * @queryParam quota_loan Buscar por la quota del prestamo. Example: 1500
   * @queryParam state_loan Buscar por el estado del prestamo. Example: En proceso
   * @queryParam guarantor_loan_affiliate Buscar los garantes del préstamo. Example: false
   * @queryParam pension_entity_affiliate Buscar por la La pension entidad del afiliado. Example: SENASIR
   * @queryParam disbursement_date_loan Buscar por fecha de desembolso. Example: 2021
   * @authenticated
   * @responseFile responses/loan/list_tracing.200.json
   */

  public function loan_tracking(Request $request)
  {
      // aumenta el tiempo máximo de ejecución de este script a 150 min:
      ini_set('max_execution_time', 9000);
      // aumentar el tamaño de memoria permitido de este script:
      ini_set('memory_limit', '960M');

      if ($request->has('excel')) {
          $excel = $request->boolean('excel');
      } else {
          $excel =false;
      }

      $order = request('sortDesc') ?? '';
      if ($order != '') {
          if ($order) {
              $order_loan = 'Asc';
          }
          if (!$order) {
              $order_loan = 'Desc';
          }
      } else {
          $order_loan = 'Desc';
      }

      if ($request->has('trashed_loan')) {
         $trashed_loan = $request->boolean('trashed_loan');
          if (!$trashed_loan) {
              $trashed_loan = false;
          }
          if ($trashed_loan) {
              $trashed_loan = true;
          }
      } else {
          $trashed_loan = false;
      }
      $pagination_rows = request('per_page') ?? 10;
      $conditions = [];
      $conditions_or = [];
      //filtros
      $id_loan = request('id_loan') ?? '';
      $id_affiliate = request('id_affiliate') ?? '';
      // filtros borrower
      $identity_card_affiliate = request('identity_card_affiliate') ?? '';
      $registration_affiliate = request('registration_affiliate') ?? '';
      $last_name_affiliate = request('last_name_affiliate') ?? '';
      $mothers_last_name_affiliate = request('mothers_last_name_affiliate') ?? '';
      $first_name_affiliate = request('first_name_affiliate') ?? '';
      $second_name_affiliate = request('second_name_affiliate') ?? '';
      $surname_husband_affiliate = request('surname_husband_affiliate') ?? '';

      $pension_entity_affiliate = request('pension_entity_affiliate') ?? '';

      $registration_borrower = request('registration_borrower') ?? '';
      $last_name_borrower = request('last_name_borrower') ?? '';
      $mothers_last_name_borrower = request('mothers_last_name_borrower') ?? '';
      $first_name_borrower = request('first_name_borrower') ?? '';
      $second_name_borrower = request('second_name_borrower') ?? '';
      $surname_husband_borrower = request('surname_husband_borrower') ?? '';

      $identity_card_borrower = request('identity_card_borrower') ?? '';//CI
      $full_name_borrower = request('full_name_borrower') ?? '';//FULL NAME
      //fin filtros borrower

      //loan
      $city_loan = request('city_loan') ?? '';//DTO
      $name_role_loan = request('name_role_loan') ?? '';//AREA
      $user_loan = request('user_loan') ?? '';//USUARIO
      $code_loan = request('code_loan') ?? '';//CODE LOAN
      $sub_modality_loan = request('sub_modality_loan') ?? '';
      $shortened_sub_modality_loan = request('shortened_sub_modality_loan') ?? '';
      $modality_loan = request('modality_loan') ?? '';
      $amount_approved_loan = request('amount_approved_loan') ?? '';


      $state_type_affiliate = request('state_type_affiliate') ?? '';
      $state_affiliate = request('state_affiliate') ?? '';

      $state_loan = request('state_loan') ?? '';

      $quota_loan = request('quota_loan') ?? '';

      $guarantor_loan = request('guarantor_loan') ?? '';

      $disbursement_date_loan = request('disbursement_date_loan') ?? '';

      $amount_approved_loan = request('amount_approved_loan') ?? '';

      $validated_loan = request('validated_loan') ?? '';

              if ($id_loan != '') {
                  array_push($conditions, array('view_loan_borrower.id_loan', 'ilike', "%{$id_loan}%"));
              }

              if ($code_loan != '') {
                  array_push($conditions, array('view_loan_borrower.code_loan', 'ilike', "%{$code_loan}%"));
              }

              if ($id_affiliate != '') {
                  array_push($conditions, array('view_loan_borrower.id_affiliate', 'ilike', "%{$id_affiliate}%"));
              }
              if ($identity_card_affiliate != '') {
                  array_push($conditions, array('view_loan_borrower.identity_card_affiliate', 'ilike', "%{$identity_card_affiliate}%"));
              }
              if ($registration_affiliate != '') {
                  array_push($conditions, array('view_loan_borrower.registration_affiliate', 'ilike', "%{$registration_affiliate}%"));
              }

              if ($last_name_affiliate != '') {
                  array_push($conditions, array('view_loan_borrower.last_name_affiliate', 'ilike', "%{$last_name_affiliate}%"));
              }
              if ($mothers_last_name_affiliate != '') {
                  array_push($conditions, array('view_loan_borrower.mothers_last_name_affiliate', 'ilike', "%{$mothers_last_name_affiliate}%"));
              }

              if ($first_name_affiliate != '') {
                  array_push($conditions, array('view_loan_borrower.first_name_affiliate', 'ilike', "%{$first_name_affiliate}%"));//
              }
              if ($second_name_affiliate != '') {
                  array_push($conditions, array('view_loan_borrower.second_name_affiliate', 'ilike', "%{$second_name_affiliate}%"));
              }
              if ($surname_husband_affiliate != '') {
                  array_push($conditions, array('view_loan_borrower.surname_husband_affiliate', 'ilike', "%{$surname_husband_affiliate}%"));
              }
              if ($identity_card_borrower != '') {
                  array_push($conditions, array('view_loan_borrower.identity_card_borrower', 'ilike', "%{$identity_card_borrower}%"));
              }

              if ($registration_borrower != '') {
                  array_push($conditions, array('view_loan_borrower.registration_borrower', 'ilike', "%{$registration_borrower}%"));
              }

              if ($last_name_borrower != '') {
                  array_push($conditions, array('view_loan_borrower.last_name_borrower', 'ilike', "%{$last_name_borrower}%"));
              }
              if ($mothers_last_name_borrower != '') {
                  array_push($conditions, array('view_loan_borrower.mothers_last_name_borrower', 'ilike', "%{$mothers_last_name_borrower}%"));
              }

              if ($first_name_borrower != '') {
                  array_push($conditions, array('view_loan_borrower.first_name_borrower', 'ilike', "%{$first_name_borrower}%"));//
              }
              if ($second_name_borrower != '') {
                  array_push($conditions, array('view_loan_borrower.second_name_borrower', 'ilike', "%{$second_name_borrower}%"));
              }
              if ($surname_husband_borrower != '') {
                  array_push($conditions, array('view_loan_borrower.surname_husband_borrower', 'ilike', "%{$surname_husband_borrower}%"));
              }
              if ($full_name_borrower != '') {
                array_push($conditions, array('view_loan_borrower.full_name_borrower', 'ilike', "%{$full_name_borrower}%"));
            }

              if ($sub_modality_loan != '') {
                  array_push($conditions, array('view_loan_borrower.sub_modality_loan', 'ilike', "%{$sub_modality_loan}%"));
              }
              if ($shortened_sub_modality_loan != '') {
                  array_push($conditions, array('view_loan_borrower.shortened_sub_modality_loan', 'ilike', "%{$shortened_sub_modality_loan}%"));
              }
              if ($modality_loan != '') {
                  array_push($conditions, array('view_loan_borrower.modality_loan', 'ilike', "%{$modality_loan}%"));
              }

              if ($amount_approved_loan != '') {
                  array_push($conditions, array('view_loan_borrower.amount_approved_loan', 'ilike', "%{$amount_approved_loan}%"));
              }
              if ($state_type_affiliate != '') {
                  array_push($conditions, array('view_loan_borrower.state_type_affiliate', 'ilike', "%{$state_type_affiliate}%"));
              }
              if ($state_affiliate != '') {
                  array_push($conditions, array('view_loan_borrower.state_affiliate', 'ilike', "%{$state_affiliate}%"));
              }

              if ($quota_loan != '') {
                  array_push($conditions, array('view_loan_borrower.quota_loan', 'ilike', "%{$quota_loan}%"));
              }
              if ($state_loan != '') {
                  array_push($conditions, array('view_loan_borrower.state_loan', 'ilike', "%{$state_loan}%"));
              }
              if ($guarantor_loan != '') {
                  array_push($conditions, array('view_loan_borrower.guarantor_loan', 'ilike', "%{$guarantor_loan}%"));
              }
              if ($pension_entity_affiliate != '') {
                  array_push($conditions, array('view_loan_borrower.pension_entity_affiliate', 'ilike', "%{$pension_entity_affiliate}%"));
              }
              if ($disbursement_date_loan != '') {
                  array_push($conditions, array('view_loan_borrower.disbursement_date_loan', 'ilike', "%{$disbursement_date_loan}%"));
              }

              if ($city_loan != '') {
                  array_push($conditions, array('view_loan_borrower.city_loan', 'ilike', "%{$city_loan}%"));
              }
              if ($user_loan != '') {
                  array_push($conditions, array('view_loan_borrower.user_loan', 'ilike', "%{$user_loan}%"));
              }
              if ($name_role_loan != '') {
                  array_push($conditions, array('view_loan_borrower.name_role_loan', 'ilike', "%{$name_role_loan}%"));
              }
              if ($validated_loan != '') {
                  array_push($conditions, array('view_loan_borrower.validated_loan', 'ilike', "%{$validated_loan}%"));
              }

              if ($trashed_loan) {
                  array_push($conditions, array('view_loan_borrower.state_loan', 'like', "Anulado"));
              }else{
                 array_push($conditions, array('view_loan_borrower.state_loan', '<>', "Anulado"));
              }
              if ($excel==true) {
                  $list_loan = DB::table('view_loan_borrower')
                      ->where($conditions)
                      ->select('*')
                      ->orderBy('code_loan', $order_loan)
                      ->get();

                  $File="ListadoPrestamos";
                  $data=array(
                      array("DPTO","ÁREA","USUARIO","ID PRESTAMO", "COD. PRESTAMO", "ID AFILIADO","CI AFILIADO","MATRICULA AFILIADO","NOMBRE COMPLETO AFILIADO","CI PRESTATARIO", "MATRÍCULA PRESTATARIO", "NOMBRE COMPLETO PRESTATARIO","SUB MODALIDAD",
                      "MODALIDAD","MONTO","TIPO ESTADO","ESTADO AFILIADO","CUOTA","ESTADO PRÉSTAMO","ENTE GESTOR AFILIADO",'FECHA DE DESEMBOLSO','TIPO SOLICITUD AFILIADO/ESPOSA' )
             );
                  foreach ($list_loan as $row){
                 array_push($data, array(
                     $row->city_loan,
                     $row->name_role_loan,
                     $row->user_loan,
                     $row->id_loan,
                     $row->code_loan,
                     $row->id_affiliate,
                     $row->identity_card_affiliate,
                     $row->registration_affiliate,
                     $row->full_name_affiliate,
                     $row->identity_card_borrower,
                     $row->registration_borrower,
                     $row->full_name_borrower,
                     $row->sub_modality_loan,
                     $row->modality_loan,
                     $row->amount_approved_loan,
                     $row->state_type_affiliate,
                     $row->state_affiliate,
                     $row->quota_loan,
                     $row->state_loan,
                     $row->pension_entity_affiliate,
                     $row->disbursement_date_loan,
                     $row->type_affiliate_spouse_loan
                 ));
             }
                  $export = new ArchivoPrimarioExport($data);
                  return Excel::download($export, $File.'.xls');
              } else {
                      $list_loan = DB::table('view_loan_borrower')
                      ->where($conditions)
                      ->select('*')
                      ->orderBy('code_loan', $order_loan)
                      ->paginate($pagination_rows);
                  return $list_loan;
              }
          }

   /** @group Reportes de Prestamos
     * PVT y SISMU descuentos simultaneos
     * @bodyParam date date required Fecha para el periodo a consultar. Example: 16-06-2021
     * @responseFile responses/report_loans/loan_desembolsado.200.json
     * @authenticated
     */ 
   public function loan_pvt_sismu_report(request $request){
        $month = Carbon::now()->format('m');
        $year = Carbon::now()->format('Y');
        $first_month = Carbon::parse($request->date);
        $first_month->startOfMonth()->subMonth()->endOfMonth()->format('d-m-Y');
        $second_month = $first_month->startOfMonth()->subMonth()->endOfMonth()->format('d-m-Y');
        $loans_lenders = Loan::where('disbursement_date', '<=', Carbon::parse($request->date))->where('guarantor_amortizing', false)->where('state_id', LoanState::whereName('Vigente')->first()->id)->get();
        //$loans_guarantors = Loan::where('disbursement_date', '!=', null)->where('guarantor_amortizing', true)->where('state_id', LoanState::whereName('Vigente')->first()->id)->get();
        $loan_sheets = array(
            array("Nombres y Apellidos", "Cedula de Identidad", "Matricula", "Matricula DH", "Nro Prestamo", "Fecha de Solicitud", "Fecha de desembolso", "Monto solicitado", "Saldo", "cuota fija mensual", "origen", "Amortizado Por")
        );
        foreach($loans_lenders as $loan)
        {
            foreach($loan->lenders as $lender)
            {
                $loans_sismu = $lender->active_loans_sismu();
                $guarantees_sismu = $lender->active_guarantees_sismu();
                if($loans_sismu != null)
                {
                    array_push($loan_sheets, array(
                        "",
                        "",
                        "",
                        "",
                        "",
                        "",
                        "",
                        "",
                        "",
                    ));
                    array_push($loan_sheets, array(
                        $lender->full_name,
                        $lender->identity_card,
                        $lender->registration,
                        $lender->spouse ? $lender->spouse->registration : "",
                        $loan->code,
                        $loan->request_date,
                        Carbon::parse($loan->disbursement_date)->format('d/m/Y H:i:s'),
                        $loan->amount_approved,
                        $loan->balance,
                        $lender->pivot->quota_treat,
                        "PVT",
                        "TITULAR",
                    ));
                    foreach($loans_sismu as $loan_sismu)
                    {
                        array_push($loan_sheets, array(
                            $lender->full_name,
                            $lender->identity_card,
                            $lender->registration,
                            $lender->spouse ? $lender->spouse->registration : "",
                            $loan_sismu->PresNumero,
                            Carbon::parse($loan_sismu->PresFechaPrestamo)->format('d/m/Y'),
                            Carbon::parse($loan_sismu->PresFechaDesembolso)->format('d/m/Y H:i:s'),
                            $loan_sismu->PresMntDesembolso,
                            $loan_sismu->PresSaldoAct,
                            $loan_sismu->PresCuotaMensual,
                            "SISMU",
                            "TITULAR",
                        ));
                    }
                }
                if($guarantees_sismu != null)
                {
                    $state = false;
                    foreach($guarantees_sismu as $guarantee_sismu)
                    {
                        $query = "SELECT top 4 *
                                    from Amortizacion
                                    where Amortizacion.IdPrestamo = '$guarantee_sismu->IdPrestamo'
                                    and Amortizacion.AmrTipPago = 'GARANTE'
                                    order by Amortizacion.AmrFecPag DESC";
                        $payments = DB::connection('sqlsrv')->select($query);
                        foreach($payments as $payment)
                        {
                            if(Carbon::parse($payment->AmrFecPag)->format('d-m-Y') == $first_month || Carbon::parse($payment->AmrFecPag)->format('d-m-Y') == $second_month){
                                $state = true;
                            }
                        }
                    }
                    if($loans_sismu == null && $state == true)
                    {
                        array_push($loan_sheets, array(
                            "",
                            "",
                            "",
                            "",
                            "",
                            "",
                            "",
                            "",
                            "",
                        ));
                        array_push($loan_sheets, array(
                            $lender->full_name,
                            $lender->identity_card,
                            $lender->registration,
                            $lender->spouse ? $lender->spouse->registration : "",
                            $loan->code,
                            $loan->request_date,
                            Carbon::parse($loan->disbursement_date)->format('d/m/Y H:i:s'),
                            $loan->amount_approved,
                            $loan->balance,
                            $lender->pivot->quota_treat,
                            "PVT",
                            "TITULAR",
                        ));
                    }
                    foreach($guarantees_sismu as $guarantee_sismu)
                    {
                        $state = false;
                        $query = "SELECT top 4 *
                                    from Amortizacion
                                    where Amortizacion.IdPrestamo = '$guarantee_sismu->IdPrestamo'
                                    and Amortizacion.AmrTipPago = 'GARANTE'
                                    order by Amortizacion.AmrFecPag DESC";
                        $payments = DB::connection('sqlsrv')->select($query);
                        foreach($payments as $payment)
                        {
                            if(Carbon::parse($payment->AmrFecPag)->format('d-m-Y') == $first_month || Carbon::parse($payment->AmrFecPag)->format('d-m-Y') == $second_month)
                                $state = true;
                        }
                        if($state == true){
                            array_push($loan_sheets, array(
                                $lender->full_name,
                                $lender->identity_card,
                                $lender->registration,
                                $lender->spouse ? $lender->spouse->registration : "",
                                $guarantee_sismu->PresNumero,
                                Carbon::parse($loan_sismu->PresFechaPrestamo)->format('d/m/Y'),
                                Carbon::parse($guarantee_sismu->PresFechaDesembolso)->format('d/m/Y H:i:s'),
                                $guarantee_sismu->PresMntDesembolso,
                                $guarantee_sismu->PresSaldoAct,
                                $guarantee_sismu->PresCuotaMensual/$guarantee_sismu->quantity_guarantors,
                                "SISMU",
                                "GARANTE"
                            ));
                        }
                    }
                }
            }
        }
        $file_name = $month.'-'.$year;
         $extension = '.xls';
         $export = new SheetExportPayment($loan_sheets, "Prestamos PVT y SISMU");
         return Excel::download($export, $file_name.$extension);
    }


    /** @group Reportes de Prestamos
     * Estado de Solicitudes de Prestamo
     * @bodyParam date date required Fecha para el periodo a consultar. Example: 16-06-2021
     * @responseFile responses/loan/print_request_loans.200.json
     * @authenticated
     */ 
    public function request_state_report(request $request, $standalone = true)
    {
        $loans = Loan::whereStateId(LoanState::whereName('En Proceso')->first()->id)->where('request_date', '<=', $request->date)->orderBy('role_id')->get();
        $loans_array = collect([]);
        $date = "";
        foreach($loans as $loan)
        {
            foreach($loan->records as $record){
                if(strpos($record->action, "derivó") != false){
                    $date = "";
                    $date = $record->created_at;
                    break;
                }
            }
            $loans_array->push([
                "code" => $loan->code,
                "request_date" => Carbon::parse($loan->request_date)->format('d/m/Y H:i:s'),
                "lenders" => $loan->lenders,
                "role" => $loan->role->display_name,
                "update_date" => Carbon::parse($date)->format('d/m/Y H:i:s'),
                "user" => $loan->user ? $loan->user->username : "",
                "amount" => $loan->amount_approved,
                "amount_dirbursement" => $loan->refinancing_balance == 0? $loan->amount_approved:$loan->refinancing_balance,
            ]);
            //$loans_array->push($data);
        }/*foreach ($loans_array as $loan_array)
            return $loan_array->loan_code;*/
            
        $data = [
            'header' => [
                'direction' => 'DIRECCIÓN DE ASUNTOS ADMINISTRATIVOS',
                'unity' => 'UNIDAD DE SISTEMAS',
                'table' => [
                    ['Fecha', Carbon::now()->format('d-m-Y')],
                    ['Hora', Carbon::now()->format('H:m:s')],
                    ['Usuario', Auth::user()->username]
                ]
            ],
            'title' => 'Estado de Solicitudes de Prestamos',
            'loans' => $loans_array,
            'file_title' => 'Estado de Solicitudes de Prestamos',
        ];
        $file_name = 'Solicitudes de Prestamos.pdf';
        $view = view()->make('loan.reports.request_state_report')->with($data)->render();
        if ($standalone) return Util::pdf_to_base64([$view], $file_name,'Reporte Estado de Solicitudes de Prestamos','letter', $request->copies ?? 1);
        return $view;
    }
  /**
   * Listar prestamos generando reportes
   * Lista todos los prestamos con opcion a busquedas
   * @queryParam sortDesc Vector de orden descendente(0) o ascendente(1). Example: 0
   * @queryParam per_page Número de datos por página. Example: 8
   * @queryParam page Número de página. Example: 1
   * @queryParam excel Valor booleano para descargar  el docExcel. Example: true
   * @queryParam id_loan Buscar ID del Préstamo. Example: 1
   * @queryParam code_loan  Buscar código del Préstamo. Example: PTMO000012-2021
   * @queryParam id_affiliate  Buscar por ID del affiliado. Example: 33121
   * @queryParam identity_card_borrower  Buscar por nro de CI del Prestatario. Example: 10069775
   * @queryParam registration_borrower  Buscar por Matricula del Prestatario. Example: 100697MDF
   * @queryParam last_name_borrower Buscar por primer apellido del Prestatario. Example: RIVERA
   * @queryParam mothers_last_name_borrower Buscar por segundo apellido del Prestatario. Example: ARTEAG
   * @queryParam first_name_borrower Buscar por primer Nombre del Prestatario. Example: ABAD
   * @queryParam second_name_borrower Buscar por segundo Nombre del Prestatario. Example: FAUST
   * @queryParam surname_husband_borrower Buscar por Apellido de casada Nombre del Prestatario. Example: De LA CRUZ
   * @queryParam full_name_borrower Buscar por Apellido de casada Nombre del Prestatario. Example: RIVERA ARTEAG ABAD FAUST De LA CRUZ
   * @queryParam sub_modality_loan Buscar por sub modalidad del préstamo. Example: Corto plazo sector activo
   * @queryParam modality_loan Buscar por Modalidad del prestamo. Example: Préstamo a corto plazo
   * @queryParam shortened_sub_modality_loan Buscar por nombre corto de la sub modalidad del prestamo. Example:COR-ACT
   * @queryParam amount_approved_loan Buscar monto aprobado del afiliado. Example: 25000
   * @queryParam state_type_affiliate Buscar por tipo de estado del afiliado. Example: Activo
   * @queryParam state_affiliate Buscar por estado del affiliado. Example: Servicio
   * @queryParam quota_loan Buscar por la quota del prestamo. Example: 1500
   * @queryParam state_loan Buscar por el estado del prestamo. Example: En proceso
   * @queryParam guarantor_loan Buscar los garantes del préstamo. Example: false
   * @queryParam pension_entity_affiliate Buscar por la La pension entidad del afiliado. Example: SENASIR
   * @queryParam disbursement_date_loan Buscar por fecha de desembolso. Example: 2021
   * @authenticated
   * @responseFile responses/loan/list_loans_generate.200.json
   */

  public function list_loan_generate(Request $request){
    // aumenta el tiempo máximo de ejecución de este script a 150 min:
    ini_set('max_execution_time', 9000);
    // aumentar el tamaño de memoria permitido de este script:
    ini_set('memory_limit', '960M');
 
    if($request->has('excel'))
         $excel = $request->boolean('excel');
    else 
         $excel =false;
 
    $order = request('sortDesc') ?? '';
    if($order != ''){
        if($order) $order_loan = 'Asc';
        if(!$order) $order_loan = 'Desc';
 
    }else{
     $order_loan = 'Desc';
    }
    $pagination_rows = request('per_page') ?? 10;
    $conditions = [];
    //filtros
    $id_loan = request('id_loan') ?? '';
    $code_loan = request('code_loan') ?? '';
    $id_affiliate = request('id_affiliate') ?? '';
    $identity_card_borrower = request('identity_card_borrower') ?? '';
    $registration_borrower = request('registration_borrower') ?? '';
 
    $last_name_borrower = request('last_name_borrower') ?? '';
    $mothers_last_name_borrower = request('mothers_last_name_borrower') ?? '';
    $first_name_borrower = request('first_name_borrower') ?? '';
    $second_name_borrower = request('second_name_borrower') ?? '';
    $surname_husband_borrower = request('surname_husband_borrower') ?? '';
    $full_name_borrower = request('full_name_borrower') ?? '';
 
    $sub_modality_loan = request('sub_modality_loan') ?? '';
    $modality_loan = request('modality_loan') ?? '';
    $shortened_sub_modality_loan = request('shortened_sub_modality_loan') ?? '';

    $amount_approved_loan = request('amount_approved_loan') ?? '';
    $state_type_affiliate = request('state_type_affiliate') ?? '';
    $state_affiliate = request('state_affiliate') ?? '';
    $state_loan = request('state_loan') ?? '';
 
    $quota_loan = request('quota_loan') ?? '';
    $guarantor_loan = request('guarantor_loan') ?? '';
    $pension_entity_affiliate = request('pension_entity_affiliate') ?? '';

    $disbursement_date_loan = request('disbursement_date_loan') ?? '';
 
    $amount_approved_loan = request('amount_approved_loan') ?? '';
 
       if ($id_loan != '') {
        array_push($conditions, array('view_loan_borrower.id_loan', 'ilike', "%{$id_loan}%"));
      }
 
      if ($code_loan != '') {
        array_push($conditions, array('view_loan_borrower.code_loan', 'ilike', "%{$code_loan}%"));
      }
  
      if ($id_affiliate != '') {
        array_push($conditions, array('view_loan_borrower.id_affiliate', 'ilike', "%{$id_affiliate}%"));
      }

      if ($identity_card_borrower != '') {
        array_push($conditions, array('view_loan_borrower.identity_card_borrower', 'ilike', "%{$identity_card_borrower}%"));
      }

      if ($registration_borrower != '') {
        array_push($conditions, array('view_loan_borrower.registration_borrower', 'ilike', "%{$registration_borrower}%"));
      }

      if ($last_name_borrower != '') {
        array_push($conditions, array('view_loan_borrower.last_name_borrower', 'ilike', "%{$last_name_borrower}%"));
      }

     if ($mothers_last_name_borrower != '') {
        array_push($conditions, array('view_loan_borrower.mothers_last_name_borrower', 'ilike', "%{$mothers_last_name_borrower}%"));
      }

      if ($first_name_borrower != '') {
        array_push($conditions, array('view_loan_borrower.first_name_borrower', 'ilike', "%{$first_name_borrower}%"));
      }

      if ($second_name_borrower != '') {
        array_push($conditions, array('view_loan_borrower.second_name_borrower', 'ilike', "%{$second_name_borrower}%"));
      }

      if ($surname_husband_borrower != '') {
        array_push($conditions_or, array('view_loan_borrower.surname_husband_borrower', 'ilike', "%{$surname_husband_borrower}%"));
      }
      if ($full_name_borrower != '') {
        array_push($conditions, array('view_loan_borrower.full_name_borrower', 'ilike', "%{$full_name_borrower}%"));
      }
      if ($sub_modality_loan != '') {
        array_push($conditions, array('view_loan_borrower.sub_modality_loan', 'ilike', "%{$sub_modality_loan}%"));
      }

      if ($modality_loan != '') {
        array_push($conditions, array('view_loan_borrower.modality_loan', 'ilike', "%{$modality_loan}%"));
      }
      if ($shortened_sub_modality_loan != '') {
        array_push($conditions, array('view_loan_borrower.shortened_sub_modality_loan', 'ilike', "%{$shortened_sub_modality_loan}%"));
      }
 
      if ($amount_approved_loan != '') {
        array_push($conditions, array('view_loan_borrower.amount_approved_loan', 'ilike', "%{$amount_approved_loan}%"));
      }
      if ($state_type_affiliate != '') {
        array_push($conditions, array('view_loan_borrower.state_type_affiliate', 'ilike', "%{$state_type_affiliate}%"));
      }
      if ($state_affiliate != '') {
        array_push($conditions, array('view_loan_borrower.state_affiliate', 'ilike', "%{$state_affiliate}%"));
      }
  
      if ($quota_loan != '') {
        array_push($conditions, array('view_loan_borrower.quota_loan', 'ilike', "%{$quota_loan}%"));
      }
      if ($state_loan != '') {
        array_push($conditions, array('view_loan_borrower.state_loan', 'ilike', "%{$state_loan}%"));
      }
      if ($guarantor_loan != '') {
        array_push($conditions, array('view_loan_borrower.guarantor_loan', 'ilike', "%{$guarantor_loan}%"));
      }
      if ($pension_entity_affiliate != '') {
        array_push($conditions, array('view_loan_borrower.pension_entity_affiliate', 'ilike', "%{$pension_entity_affiliate}%"));
      }
      if ($disbursement_date_loan != '') {
        array_push($conditions, array('view_loan_borrower.disbursement_date_loan', 'ilike', "%{$disbursement_date_loan}%"));
      }
 
      if($excel==true){
                $list_loan = DB::table('view_loan_borrower')
                ->where($conditions)
                ->select('*')
                ->orderBy('code_loan', $order_loan)
                ->get();
               foreach ($list_loan as $loan) {
                 $padron = Loan::where('id', $loan->id_loan)->first();
                 $loan->balance_loan = $padron->balance;
               }
               $File="ListadoPrestamos";
               $data=array(
                   array("CI AFILIADO","EXP","MATRICULA AFILIADO","NOMBRE COMPLETO AFILIADO","ID PRESTAMO", "COD. PRESTAMO","FECHA DE SOLICITUD","FECHA DE DESEMBOLSO","DPTO","ÍNDICE DE ENDEUDAMIENTO","SUB MODALIDAD",
                   "MODALIDAD","CI PRESTATARIO","EXP",
                   "MATRÍCULA PRESTATARIO","APELLIDO PATERNO ","APELLIDO MATERNO","AP. CASADA","1ER. NOMBRE","2DO. NOMBRE","NRO CPTE CTB","MONTO","ESTADO AFILIADO","TIPO ESTADO","CUOTA","ESTADO PRÉSTAMO","ENTE GESTOR AFILIADO",'SALDO PRÉSTAMO','TIPO SOLICITUD AFILIADO/ESPOSA' )
               );
               foreach ($list_loan as $row){
                   array_push($data, array(
                       $row->identity_card_affiliate,
                       $row->city_exp_first_shortened_affiliate,
                       $row->registration_affiliate,
                       $row->full_name_affiliate,
                       $row->id_loan,
                       $row->code_loan,
                       $row->request_date_loan,
                       $row->disbursement_date_loan,
                       $row->city_loan,
                       $row->indebtedness_borrower,
                       $row->sub_modality_loan,
                       $row->modality_loan,
                       $row->identity_card_borrower,
                       $row->city_exp_first_shortened_borrower,
                       $row->registration_borrower,
                       $row->last_name_borrower,
                       $row->mothers_last_name_borrower,
                       $row->surname_husband_borrower,
                       $row->first_name_borrower,
                       $row->second_name_borrower, 
                       $row->num_accounting_voucher_loan,   
                       $row->amount_approved_loan,
                       $row->state_type_affiliate,
                       $row->state_affiliate,
                       $row->quota_loan,
                       $row->state_loan,
                       $row->pension_entity_affiliate,
                       $row->balance_loan,       
                       $row->type_affiliate_spouse_loan
                   ));
               }
               $export = new ArchivoPrimarioExport($data);
               return Excel::download($export, $File.'.xls');
      }else{    
        $list_loan = DB::table('view_loan_borrower')
       ->where($conditions)
       ->select('*')
       ->orderBy('code_loan', $order_loan)   
       ->paginate($pagination_rows);
            $list_loan->getCollection()->transform(function ($list_loan) {
            $padron = Loan::findOrFail($list_loan->id_loan);
            $list_loan->balance_loan=$padron->balance;
            return $list_loan;
               });
           return $list_loan;
      }
   }

   /** @group Reportes de Prestamos
   * Reporte de solicitudes de prestamos
   * @queryParam initial_date Fecha inicial. Example: 2021-01-01
   * @queryParam final_date Fecha Final. Example: 2021-05-01
   * @authenticated
   * @responseFile responses/loan/list_tracing.200.json
   */
  public function loan_application_status(request $request, $standalone = true)
  {
       if($request->initial_date == null)
           $initial_date = Carbon::parse('1900-01-01');
       else
           $initial_date = $request->initial_date;
       if($request->final_date == null)
           $final_date = Carbon::now();
       else
           $final_date = $request->final_date;
       $initial_date = Carbon::parse($initial_date)->startOfDay();
       $final_date = Carbon::parse($final_date)->endOfDay();

       $loans = Loan::where('request_date', '>=', $initial_date)
               ->where('request_date', '<=', $final_date)
               ->where('deleted_at', null)
               ->orderBy('role_id')->get();
       $loans_collect = collect([]);
       $query = "SELECT role_id, count(*)
               from loans l
               where l.request_date >= '$initial_date'
               and l.request_date <= '$final_date'
               and l.deleted_at is null 
               group by role_id
               order by role_id";
       $roles = DB::select($query);
       foreach($loans as $loan)
       {
           $ubication = $loan->role->display_name;
           $query_derivation = "SELECT *
                               from records r 
                               where r.recordable_type = 'loans'
                               and r.record_type_id = 3
                               and r.recordable_id = $loan->id
                               and r.action like '%$ubication'
                               order by r.created_at";
           $derivation = DB::select($query_derivation);
           $loans_collect->push([
               'code' => $loan->code,
               'request_date' => $loan->request_date,
               'modality' => $loan->modality->procedure_type->name,
               'sub_modality' => $loan->modality->name,
               'type' => $loan->borrower->first()->state->affiliate_state_type->name,
               'borrower' => $loan->borrower->first()->full_name,
               'ci_borrower' => $loan->borrower->first()->identity_card,
               'user' => $loan->user ? $loan->user->username : '',
               'role' => $loan->role->display_name,
               'city' => $loan->city->name,
               'derivation_date' => sizeof($derivation) == 0 ? '' : Carbon::parse($derivation[0]->created_at)->format('d-m-Y H:m:s'),
               'request_amount' => $loan->amount_approved,
               'ref' => $loan->parent_reason == "REFINANCIAMIENTO" ? "S" : "N",
               'disbursed_amount' => $loan->refinancing_balance == 0 ? $loan->amount_approved : $loan->refinancing_balance
           ]);
       }
      
      $data = [
       'header' => [
           'direction' => 'DIRECCIÓN DE ESTRATEGIAS SOCIALES E INVERSIONES',
           'unity' => 'UNIDAD DE JEFATURA DE PRESTAMOS',
           'table' => [
               ['Fecha', Carbon::now()->format('d-m-Y')],
               ['Hora', Carbon::now()->format('H:m:s')],
               ['Usuario', Auth::user()->username]
           ]
       ],
       'title' => 'ESTADOS DE SOLICITUDES DE PRESTAMOS',
       'initial_date' => $request->initial_date,
       'final_date' => $request->final_date,
       'loans' => $loans_collect,
       'roles' => $roles,
       'file_title' => 'Estado de Solicitud de Prestamos',
   ];
   $file_name = 'Ingresos Depositados en Tesoreria.pdf';
   $view = view()->make('loan.reports.loan_state_request_report')->with($data)->render();
   if ($standalone) return Util::pdf_to_base64([$view], $file_name, 'Depositos en Tesoreria' ,'letter', $request->copies ?? 1, false);
   return $view;
  }
}
