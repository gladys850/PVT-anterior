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
                   array( "NRO DE PRÉSTAMO", "FECHA DE SOLICITUD", "FECHA DESEMBOLSO",
                   "REGIONAL","TIPO","MODALIDAD","SUB MODALIDAD",
                   "CEDULA DE IDENTIDAD","EXP","MATRICULA","MATRICULA CÓNYUGUE",
                   "PRIMER NOMBRE","SEGUNDO NOMBRE","PATERNO","MATERNO","APELLIDO CASADA","***",
                   "ESP CEDULA DE IDENTIDAD","ESP EXP","ESP MATRICULA","ESP MATRICULA CÓNYUGUE",
                   "ESP PRIMER NOMBRE","ESP SEGUNDO NOMBRE","ESP PATERNO","ESP MATERNO","ESP APELLIDO CASADA","***",
                   "NRO CBTE CONTABLE","SALDO ACTUAL","AMPLIACIÓN","MONTO DESEMBOLSADO","MONTO REFINANCIADO","LIQUIDO DESEMBOLSADO",
                   "PLAZO","ESTÁDO PRÉSTAMO","DESTINO CREDITO" )
               );
               foreach ($list_loan as $loan){
                   array_push($data, array(
                       $loan->code,
                       Carbon::parse($loan->request_date)->format('d/m/Y'),
                       Carbon::parse($loan->disbursement_date)->format('d/m/Y H:i:s'),

                       $loan->city->name,
                       $loan->lenders[0]->affiliate_state->affiliate_state_type->name,
                       $loan->modality->procedure_type->name,
                       $loan->modality->shortened,

                       $loan->lenders[0]->identity_card,
                       $loan->lenders[0]->city_identity_card->first_shortened,
                       $loan->lenders[0]->registration,
                       $loan->lenders[0]->spouse ? $loan->lenders[0]->spouse->registration : 0,
                       $loan->lenders[0]->first_name,
                       $loan->lenders[0]->second_name,
                       $loan->lenders[0]->last_name,
                       $loan->lenders[0]->mothers_last_name,
                       $loan->lenders[0]->surname_husband,
                       //
                       $loan->lenders[0]->spouse ? '***':'***',
                       $loan->lenders[0]->spouse ? $loan->lenders[0]->spouse->identity_card :'',
                       $loan->lenders[0]->spouse ? $loan->lenders[0]->spouse->city_identity_card->first_shortened:'',
                       $loan->lenders[0]->spouse ? $loan->lenders[0]->spouse->registration:'',
                       $loan->lenders[0]->spouse ? $loan->lenders[0]->registration :'',
                       $loan->lenders[0]->spouse ? $loan->lenders[0]->spouse->first_name:'',
                       $loan->lenders[0]->spouse ? $loan->lenders[0]->spouse->second_name:'',
                       $loan->lenders[0]->spouse ? $loan->lenders[0]->spouse->last_name:'',
                       $loan->lenders[0]->spouse ? $loan->lenders[0]->spouse->mothers_last_name:'',
                       $loan->lenders[0]->spouse ? $loan->lenders[0]->spouse->surname_husband:'',
                       $loan->lenders[0]->spouse ? '***':'***',
                       //
                       $loan->num_accounting_voucher,
                       Util::money_format($loan->balance),
                       $loan->parent_reason,
                       Util::money_format($loan->amount_approved),

                       $loan->parent_reason? Util::money_format($loan->amount_approved - $loan->refinancing_balance) : '0,00',//liquido desembolsado
                       $loan->parent_reason? Util::money_format($loan->refinancing_balance):Util::money_format($loan->amount_approved),//MONTO REFINANCIADO//MONTO REFINANCIADO
                       $loan->loan_term,//plazo
                       $loan->state->name,//estado del prestamo

                       $loan->destiny->name
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

    $initial_date = request('initial_date') ?? '';
    $final_date = request('final_date') ?? '';

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
               //return $list_loan;
               $File="ListadoPrestamosDesembolsados";
               $data=array(
                   array( "NRO DE PRÉSTAMO", "FECHA DE SOLICITUD", "FECHA DESEMBOLSO",
                   "REGIONAL","TIPO","MODALIDAD","SUB MODALIDAD",
                   "CEDULA DE IDENTIDAD","EXP","MATRICULA","MATRICULA CÓNYUGUE",
                   "PRIMER NOMBRE","SEGUNDO NOMBRE","PATERNO","MATERNO","APELLIDO CASADA","***",
                   "ESP CEDULA DE IDENTIDAD","ESP EXP","ESP MATRICULA","ESP MATRICULA CÓNYUGUE",
                   "ESP PRIMER NOMBRE","ESP SEGUNDO NOMBRE","ESP PATERNO","ESP MATERNO","ESP APELLIDO CASADA","***",
                   "NRO CBTE CONTABLE","SALDO ACTUAL","MONTO DESEMBOLSADO","LIQUIDO DESEMBOLSADO",
                   "PLAZO","ESTÁDO PRÉSTAMO","DESTINO CREDITO","CAPITAL PAGADO FECHA DE CORTE","SALDO A FECHA DE CORTE",
                   "MONTO APROBADO","MONTO DESEMBOLSADO DE REFINANCIAMIENTO" ,"MONTO REFINANCIADO","AMPLIACIÓN?","INDICE DE ENDEUDAMIENTO")
               );
               foreach ($list_loan as $loan){
                   array_push($data, array(
                    $loan->code,
                    Carbon::parse($loan->request_date)->format('d/m/Y'),
                    Carbon::parse($loan->disbursement_date)->format('d/m/Y H:i:s'),

                    $loan->city->name,
                    $loan->lenders[0]->affiliate_state->affiliate_state_type->name,
                    $loan->modality->procedure_type->name,
                    $loan->modality->shortened,

                    $loan->lenders[0]->identity_card,
                    $loan->lenders[0]->city_identity_card->first_shortened,
                    $loan->lenders[0]->registration,
                    $loan->lenders[0]->spouse ? $loan->lenders[0]->spouse->registration : 0,
                    $loan->lenders[0]->first_name,
                    $loan->lenders[0]->second_name,
                    $loan->lenders[0]->last_name,
                    $loan->lenders[0]->mothers_last_name,
                    $loan->lenders[0]->surname_husband,
                    $loan->lenders[0]->spouse ? '***':'***',
                    $loan->lenders[0]->spouse ? $loan->lenders[0]->spouse->identity_card :'',
                    $loan->lenders[0]->spouse ? $loan->lenders[0]->spouse->city_identity_card->first_shortened:'',
                    $loan->lenders[0]->spouse ? $loan->lenders[0]->spouse->registration:'',
                    $loan->lenders[0]->spouse ? $loan->lenders[0]->registration :'',
                    $loan->lenders[0]->spouse ? $loan->lenders[0]->spouse->first_name:'',
                    $loan->lenders[0]->spouse ? $loan->lenders[0]->spouse->second_name:'',
                    $loan->lenders[0]->spouse ? $loan->lenders[0]->spouse->last_name:'',
                    $loan->lenders[0]->spouse ? $loan->lenders[0]->spouse->mothers_last_name:'',
                    $loan->lenders[0]->spouse ? $loan->lenders[0]->spouse->surname_husband:'',
                    $loan->lenders[0]->spouse ? '***':'***',

                    $loan->num_accounting_voucher,
                    Util::money_format($loan->balance),
                    Util::money_format($loan->amount_approved),

                    $loan->parent_reason? Util::money_format($loan->amount_approved - $loan->refinancing_balance) : Util::money_format($loan->amount_approved),//liquido desembolsado
                    $loan->loan_term,//plazo
                    $loan->state->name,//estado del prestamo
                    $loan->destiny->name,

                    $loan->last_payment? Util::money_format($loan->amount_approved - $loan->last_payment->previous_balance+$loan->last_payment->capital_payment):' sin registro',//capital pagado 
                    $loan->last_payment? Util::money_format($loan->last_payment->previous_balance-$loan->last_payment->capital_payment):' sin registro',//Saldo a fecha de corte
                    Util::money_format($loan->amount_approved),

                    $loan->parent_reason? Util::money_format($loan->refinancing_balance):'0,00',//MONTO REFINANCIADO//MONTO REFINANCIADO
                    $loan->parent_reason? Util::money_format($loan->amount_approved - $loan->refinancing_balance) : '0,00',//
                    $loan->parent_reason? $loan->parent_reason:'',//SI ES AMPLIACION
                    Util::money_format($loan->lenders[0]->pivot->indebtedness_calculated)//indice de endeudamineto
                   ));
               }

               //liquidacion
               if ($initial_date != '' && $final_date != '') {
                $date_ini = $request->initial_date.' 00:00:00';
                $date_fin = $request->final_date.' 23:59:59';

                $list_loan_liq = Loan::where('state_id', LoanState::where('name', 'Liquidado')->first()->id)->whereBetween('disbursement_date', [$date_ini, $date_fin])->get();
            }else{
                if ($final_date != '') {
                    $date_fin = $request->final_date.' 23:59:59';
                    $list_loan_liq = Loan::where('state_id', LoanState::where('name', 'Liquidado')->first()->id)->where('disbursement_date', '<=', $date_fin)->get();

                }else{
                    if ($initial_date != '') {
                        $date_ini = $request->initial_date.' 00:00:00';
                        $list_loan_liq = Loan::where('state_id', LoanState::where('name', 'Liquidado')->first()->id)->where('disbursement_date', '>=', $date_ini)->get();
                    }else{
                        $list_loan_liq = Loan::where('state_id', LoanState::where('name', 'Liquidado')->first()->id)->get();
                    }
                } 
            }

               //return $list_loan_liq;
               $File="ListadoPrestamosVigenteLiquidado";
               $data_liq=array(
                array( "NRO DE PRÉSTAMO", "FECHA DE SOLICITUD", "FECHA DESEMBOLSO",
                "REGIONAL","TIPO","MODALIDAD","SUB MODALIDAD",
                "CEDULA DE IDENTIDAD","EXP","MATRICULA","MATRICULA CÓNYUGUE",
                "PRIMER NOMBRE","SEGUNDO NOMBRE","PATERNO","MATERNO","APELLIDO CASADA","***",
                "ESP CEDULA DE IDENTIDAD","ESP EXP","ESP MATRICULA","ESP MATRICULA CÓNYUGUE",
                "ESP PRIMER NOMBRE","ESP SEGUNDO NOMBRE","ESP PATERNO","ESP MATERNO","ESP APELLIDO CASADA","***",
                "NRO CBTE CONTABLE","SALDO ACTUAL","MONTO DESEMBOLSADO","LIQUIDO DESEMBOLSADO",
                "PLAZO","ESTÁDO PRÉSTAMO","DESTINO CREDITO","CAPITAL PAGADO FECHA DE CORTE","SALDO A FECHA DE CORTE",
                "MONTO APROBADO","MONTO DESEMBOLSADO DE REFINANCIAMIENTO" ,"MONTO REFINANCIADO","AMPLIACIÓN?","INDICE DE ENDEUDAMIENTO")
            );
               foreach ($list_loan_liq as $row){
                   array_push($data_liq, array(
                    $loan->code,
                    Carbon::parse($loan->request_date)->format('d/m/Y'),
                    Carbon::parse($loan->disbursement_date)->format('d/m/Y H:i:s'),

                    $loan->city->name,
                    $loan->lenders[0]->affiliate_state->affiliate_state_type->name,
                    $loan->modality->procedure_type->name,
                    $loan->modality->shortened,

                    $loan->lenders[0]->identity_card,
                    $loan->lenders[0]->city_identity_card->first_shortened,
                    $loan->lenders[0]->registration,
                    $loan->lenders[0]->spouse ? $loan->lenders[0]->spouse->registration : 0,
                    $loan->lenders[0]->first_name,
                    $loan->lenders[0]->second_name,
                    $loan->lenders[0]->last_name,
                    $loan->lenders[0]->mothers_last_name,
                    $loan->lenders[0]->surname_husband,
                    $loan->lenders[0]->spouse ? '***':'***',
                    $loan->lenders[0]->spouse ? $loan->lenders[0]->spouse->identity_card :'',
                    $loan->lenders[0]->spouse ? $loan->lenders[0]->spouse->city_identity_card->first_shortened:'',
                    $loan->lenders[0]->spouse ? $loan->lenders[0]->spouse->registration:'',
                    $loan->lenders[0]->spouse ? $loan->lenders[0]->registration :'',
                    $loan->lenders[0]->spouse ? $loan->lenders[0]->spouse->first_name:'',
                    $loan->lenders[0]->spouse ? $loan->lenders[0]->spouse->second_name:'',
                    $loan->lenders[0]->spouse ? $loan->lenders[0]->spouse->last_name:'',
                    $loan->lenders[0]->spouse ? $loan->lenders[0]->spouse->mothers_last_name:'',
                    $loan->lenders[0]->spouse ? $loan->lenders[0]->spouse->surname_husband:'',
                    $loan->lenders[0]->spouse ? '***':'***',

                    $loan->num_accounting_voucher,
                    Util::money_format($loan->balance),
                    Util::money_format($loan->amount_approved),

                    $loan->parent_reason? Util::money_format($loan->amount_approved - $loan->refinancing_balance) : Util::money_format($loan->amount_approved),//liquido desembolsado
                    $loan->loan_term,//plazo
                    $loan->state->name,//estado del prestamo
                    $loan->destiny->name,

                    //$loan->last_payment? Util::money_format($loan->amount_approved - $loan->last_payment->previous_balance + $loan->last_payment->capital_payment):' sin registro',//capital pagado
                    $loan->last_payment? Util::money_format($loan->amount_approved - $loan->last_payment->previous_balance+$loan->last_payment->capital_payment):' sin registro',//capital pagado
                    $loan->last_payment? Util::money_format($loan->last_payment->previous_balance-$loan->last_payment->capital_payment):' sin registro',//Saldo a fecha de corte
                    Util::money_format($loan->amount_approved),

                    $loan->parent_reason? Util::money_format($loan->refinancing_balance):'0,00',//MONTO REFINANCIADO//MONTO REFINANCIADO
                    $loan->parent_reason? Util::money_format($loan->amount_approved - $loan->refinancing_balance) : Util::money_format($loan->amount_approved),//liquido desembolsado
                    $loan->parent_reason? $loan->parent_reason:'',//SI ES AMPLIACION
                    Util::money_format($loan->lenders[0]->pivot->indebtedness_calculated)//indice de endeudamineto
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
        //$date_pay = $date_ini->startOfMonth()->addMonth()->endOfMonth()->format('Y-m-d');

        $date_previous = Carbon::parse($request->date)->startOfMonth()->subMonth()->endOfMonth()->format('Y-m-d');

        $date_calculate = Carbon::parse($request->date)->endOfMonth()->format('Y-m-d');

        $date_limit = Carbon::create(Carbon::parse($date_previous)->format('Y'), Carbon::parse($date_previous)->format('m'), 15);
        $date_limit = Carbon::parse($date_limit)->format('Y-m-d');

        $loans_request = Loan::where('state_id', LoanState::where('name', 'Vigente')->first()->id)->where('disbursement_date','<=', $date_limit)->get();

        $id_senasir = array();
        foreach(ProcedureModality::where('name', 'like', '%SENASIR')->get() as $procedure)
             array_push($id_senasir, $procedure->id);
        $id_comando = array();
        foreach(ProcedureModality::where('name', 'like', '%Activo')->orWhere('name', 'like', '%Activo%')->get() as $procedure)
             array_push($id_comando, $procedure->id);
 
             $command_sheet_before=array(
                array("CI AFILIADO","MATRICULA AFILIADO","NOMBRE COMPLETO AFILIADO","***",
                "COD PRESTAMO", "FECHA DE DESEMBOLSO", "DPTO", "TIPO ESTADO","ESTADO AFILIADO",
                "MATRICULA PRESTATARIO", "CI PRESTATARIO", "1ER NOMBRE", "2DO NOMBRE", "APELLIDO PATERNO", "APELLIDO MATERNO","APELLIDO DE CASADA", "SALDO ACTUAL", "CUOTA FIJA MENSUAL", "DESCUENTO PROGRAMADO", "INTERES","Amort. TIT o GAR?","***",
                "GAR Estado","GAR Tipo de estado","Matricula garante","GAR CI", "GAR Exp","GAR Primer Nombre","GAR Segundo Nombre",
                "GAR 1er Apellido","GAR 2do Apellido","GAR Apellido de Casada","GAR Cuota fija","GAR descuento","***","GAR2 Estado","GAR2 Tipo de estado","Matricula garante","GAR2 CI", "GAR2 Exp","GAR2 Primer Nombre","GAR2 Segundo Nombre",
                "GAR2 1er Apellido","GAR2 2do Apellido","GAR2 Apellido de Casada","GAR2 Cuota fija","GAR2 descuento")
            );
         $command_sheet_later=array(
            array("CI AFILIADO","MATRICULA AFILIADO","NOMBRE COMPLETO AFILIADO","***",
            "COD PRESTAMO", "FECHA DE DESEMBOLSO", "DPTO", "TIPO ESTADO","ESTADO AFILIADO",
            "MATRICULA PRESTATARIO", "CI PRESTATARIO", "1ER NOMBRE", "2DO NOMBRE", "APELLIDO PATERNO", "APELLIDO MATERNO","APELLIDO DE CASADA", "SALDO ACTUAL", "CUOTA FIJA MENSUAL", "DESCUENTO PROGRAMADO", "INTERES","Amort. TIT o GAR?","***",
            "GAR Estado","GAR Tipo de estado","Matricula garante","GAR CI", "GAR Exp","GAR Primer Nombre","GAR Segundo Nombre",
            "GAR 1er Apellido","GAR 2do Apellido","GAR Apellido de Casada","GAR Cuota fija","GAR descuento","***","GAR2 Estado","GAR2 Tipo de estado","Matricula garante","GAR2 CI", "GAR2 Exp","GAR2 Primer Nombre","GAR2 Segundo Nombre",
            "GAR2 1er Apellido","GAR2 2do Apellido","GAR2 Apellido de Casada","GAR2 Cuota fija","GAR2 descuento")
        );
         $senasir_sheet_before=array(
            array("CI AFILIADO","MATRICULA AFILIADO","NOMBRE COMPLETO AFILIADO","***",
                "COD PRESTAMO", "FECHA DE DESEMBOLSO", "DPTO", "TIPO ESTADO","ESTADO AFILIADO",
                "MATRICULA PRESTATARIO", "CI PRESTATARIO", "1ER NOMBRE", "2DO NOMBRE", "APELLIDO PATERNO", "APELLIDO MATERNO","APELLIDO DE CASADA", "SALDO ACTUAL", "CUOTA FIJA MENSUAL", "DESCUENTO PROGRAMADO", "INTERES","Amort. TIT o GAR?","***",
                "GAR Estado","GAR Tipo de estado","Matricula garante","GAR CI", "GAR Exp","GAR Primer Nombre","GAR Segundo Nombre",
                "GAR 1er Apellido","GAR 2do Apellido","GAR Apellido de Casada","GAR Cuota fija","GAR descuento","***","GAR2 Estado","GAR2 Tipo de estado","Matricula garante","GAR2 CI", "GAR2 Exp","GAR2 Primer Nombre","GAR2 Segundo Nombre",
                "GAR2 1er Apellido","GAR2 2do Apellido","GAR2 Apellido de Casada","GAR2 Cuota fija","GAR2 descuento")
            );
         $senasir_sheet_later=array(
            array("CI AFILIADO","MATRICULA AFILIADO","NOMBRE COMPLETO AFILIADO","***",
            "COD PRESTAMO", "FECHA DE DESEMBOLSO", "DPTO", "TIPO ESTADO","ESTADO AFILIADO",
            "MATRICULA PRESTATARIO", "CI PRESTATARIO", "1ER NOMBRE", "2DO NOMBRE", "APELLIDO PATERNO", "APELLIDO MATERNO","APELLIDO DE CASADA", "SALDO ACTUAL", "CUOTA FIJA MENSUAL", "DESCUENTO PROGRAMADO", "INTERES","Amort. TIT o GAR?","***",
            "GAR Estado","GAR Tipo de estado","Matricula garante","GAR CI", "GAR Exp","GAR Primer Nombre","GAR Segundo Nombre",
            "GAR 1er Apellido","GAR 2do Apellido","GAR Apellido de Casada","GAR Cuota fija","GAR descuento","***","GAR2 Estado","GAR2 Tipo de estado","Matricula garante","GAR2 CI", "GAR2 Exp","GAR2 Primer Nombre","GAR2 Segundo Nombre",
            "GAR2 1er Apellido","GAR2 2do Apellido","GAR2 Apellido de Casada","GAR2 Cuota fija","GAR2 descuento")
        );

         $command_ancient=array(
            array("CI AFILIADO","MATRICULA AFILIADO","NOMBRE COMPLETO AFILIADO","***",
            "COD PRESTAMO", "FECHA DE DESEMBOLSO", "DPTO", "TIPO ESTADO","ESTADO AFILIADO",
            "MATRICULA PRESTATARIO", "CI PRESTATARIO", "1ER NOMBRE", "2DO NOMBRE", "APELLIDO PATERNO", "APELLIDO MATERNO","APELLIDO DE CASADA", "SALDO ACTUAL", "CUOTA FIJA MENSUAL", "DESCUENTO PROGRAMADO", "INTERES","Amort. TIT o GAR?","***",
            "GAR Estado","GAR Tipo de estado","Matricula garante","GAR CI", "GAR Exp","GAR Primer Nombre","GAR Segundo Nombre",
            "GAR 1er Apellido","GAR 2do Apellido","GAR Apellido de Casada","GAR Cuota fija","GAR descuento","***","GAR2 Estado","GAR2 Tipo de estado","Matricula garante","GAR2 CI", "GAR2 Exp","GAR2 Primer Nombre","GAR2 Segundo Nombre",
            "GAR2 1er Apellido","GAR2 2do Apellido","GAR2 Apellido de Casada","GAR2 Cuota fija","GAR2 descuento")
        );
          $senasir_ancient=array(
            array("CI AFILIADO","MATRICULA AFILIADO","NOMBRE COMPLETO AFILIADO","***",
            "COD PRESTAMO", "FECHA DE DESEMBOLSO", "DPTO", "TIPO ESTADO","ESTADO AFILIADO",
            "MATRICULA PRESTATARIO", "CI PRESTATARIO", "1ER NOMBRE", "2DO NOMBRE", "APELLIDO PATERNO", "APELLIDO MATERNO","APELLIDO DE CASADA", "SALDO ACTUAL", "CUOTA FIJA MENSUAL", "DESCUENTO PROGRAMADO", "INTERES","Amort. TIT o GAR?","***",
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
                            $lender->registration_affiliate,
                            $lender->full_name_affiliate,
                            $loan->guarantor_amortizing? '***' : '***',
                             $lender->code_loan,
                             Carbon::parse($lender->disbursement_date_loan)->format('d/m/Y H:i:s'),
                             $lender->city_loan,
                             $lender->state_type_affiliate,
                             $lender->state_affiliate,
                             $lender->registration_borrower,
                             $lender->identity_card_borrower,
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
                                $lender->registration_affiliate,
                                $lender->full_name_affiliate,
                                $loan->guarantor_amortizing? '***' : '***',
                                 $lender->code_loan,
                                 Carbon::parse($lender->disbursement_date_loan)->format('d/m/Y H:i:s'),
                                 $lender->city_loan,
                                 $lender->state_type_affiliate,
                                 $lender->state_affiliate,
                                 $lender->registration_borrower,
                                 $lender->identity_card_borrower,
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
         $loans_before = Loan::whereMonth('disbursement_date', $sub_month)->whereYear('disbursement_date', $year)->get();//considerar caso fin de año
         foreach($loans_before as $loan){
             if(Carbon::parse($loan->disbursement_date)->day > LoanGlobalParameter::first()->offset_interest_day){
                 if(in_array($loan->procedure_modality_id, $id_comando))
                 {
                     foreach($loan->getBorrowers() as $lender)
                     {
                        array_push($command_sheet_later, array(
                            $lender->identity_card_affiliate,
                            $lender->registration_affiliate,
                            $lender->full_name_affiliate,
                            $loan->guarantor_amortizing? '***' : '***',
                             $lender->code_loan,
                             Carbon::parse($lender->disbursement_date_loan)->format('d/m/Y H:i:s'),
                             $lender->city_loan,
                             $lender->state_type_affiliate,
                             $lender->state_affiliate,
                             $lender->registration_borrower,
                             $lender->identity_card_borrower,
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
                                $lender->registration_affiliate,
                                $lender->full_name_affiliate,
                                $loan->guarantor_amortizing? '***' : '***',
                                 $lender->code_loan,
                                 Carbon::parse($lender->disbursement_date_loan)->format('d/m/Y H:i:s'),
                                 $lender->city_loan,
                                 $lender->state_type_affiliate,
                                 $lender->state_affiliate,
                                 $lender->registration_borrower,
                                 $lender->identity_card_borrower,
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
                        $lender->registration_affiliate,
                        $lender->full_name_affiliate,
                        $loan->guarantor_amortizing? '***' : '***',
                         $lender->code_loan,
                         Carbon::parse($lender->disbursement_date_loan)->format('d/m/Y H:i:s'),
                         $lender->city_loan,
                         $lender->state_type_affiliate,
                         $lender->state_affiliate,
                         $lender->registration_borrower,
                         $lender->identity_card_borrower,
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
                            $lender->registration_affiliate,
                            $lender->full_name_affiliate,
                            $loan->guarantor_amortizing? '***' : '***',
                             $lender->code_loan,
                             Carbon::parse($lender->disbursement_date_loan)->format('d/m/Y H:i:s'),
                             $lender->city_loan,
                             $lender->state_type_affiliate,
                             $lender->state_affiliate,
                             $lender->registration_borrower,
                             $lender->identity_card_borrower,
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
        foreach($loans as $loan)
        {
            $loans_array->push([
                "code" => $loan->code,
                "request_date" => Carbon::parse($loan->request_date)->format('d-m-Y'),
                "lenders" => $loan->lenders,
                "role" => $loan->role->display_name,
                "update_date" => "",
                "user" => $loan->user ? $loan->user->username : "",
                "amount" => $loan->amount_approved,
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
        if ($standalone) return Util::pdf_to_base64([$view], $file_name, $request->copies ?? 1);
        return $view;
    }
}
