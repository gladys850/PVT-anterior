<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Util;

use DB;
use App\LoanPayment;
use App\Loan;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ArchivoPrimarioExport;
use App\Exports\SheetExportPayment;
use App\Exports\MultipleSheetExportPayment;
use Illuminate\Support\Facades\Storage;
//use App\Exports\SheetExportPayment;
use Carbon;

class LoanPaymentReportController extends Controller
{
   /** @group Reportes de amortizaciones 
   * Reporte de amortizaciones de descuentos Titular - Garante 
   * Reporte de amortizaciones de descuentos Titular - Garante 
   * @queryParam initial_date Fecha inicial. Example: 2021-01-01
   * @queryParam final_date Fecha Final. Example: 2021-05-01
   * @authenticated
   * @responseFile responses/reports_amortizations/dincuent_months_t_g.200.json
   */
  public function report_amortization_discount_months(Request $request){
    // aumenta el tiempo máximo de ejecución de este script a 150 min:
    ini_set('max_execution_time', 9000);
    // aumentar el tamaño de memoria permitido de este script:
    ini_set('memory_limit', '960M');

    $order_loan = 'Desc';
    
    $conditions = [];
    //filtros
    $initial_date = request('initial_date') ?? '';
    $final_date = request('final_date') ?? '';
    $state_pagado = 'Pagado';
    $procedure_loan_payment = "Amortización Automática";

    if ($initial_date != '') {
      array_push($conditions, array('view_loan_amortizations.estimated_date_loan_payment', '>=', "%{$initial_date}%"));
    }
    if ($final_date != '') {
      array_push($conditions, array('view_loan_amortizations.estimated_date_loan_payment', '<=', "%{$final_date}%"));
    }
    array_push($conditions, array('view_loan_amortizations.states_loan_payment', 'like', "%{$state_pagado}%"));
    array_push($conditions, array('view_loan_amortizations.procedure_loan_payment', 'like', "%{$procedure_loan_payment}%"));
    $list_loan = DB::table('view_loan_amortizations')
      ->where($conditions)
      ->select('*')
      ->orderBy('code_loan', $order_loan)
      ->get();
        foreach ($list_loan as $loan) {
          $padron = Loan::where('id', $loan->id_loan)->first();
          $loan->modality=$padron->modality->procedure_type->second_name;
          $loan->sub_modality=$padron->modality->shortened;
          $loan->separation='***';
          }      
          $File="ListadoAmortizaciones";
          $data=array(
              array("CI AFILIADO","MATRICULA AFILIADO","NOMBRE COMPLETO AFILIADO","***","COD PRÉSTAMO", "FECHA DE DESEMBOLSO", "TIPO","FECHA DE CALCULO","FECHA DE TRANSACCIÓN",
              "MODALIDAD PRÉSTAMO","SUB MODALIDAD PRÉSTAMO",
              "MATRICULA", "CI", "PRIMER NOMBRE","SEGUNDO NOMBRE","APELLIDO PATERNO","APELLIDO MATERNO","APELLIDO CASADA",
              "CAPITAL","INTERÉS CORRIENTE","INTERÉS PENAL","INTERÉS CORRIENTE PENDIENTE",
              "INTERÉS PENAL PENDIENTE","TOTAL PAGADO","SALDO ANTERIOR","SALDO ACTUAL","PAGADO POR","TIPO DESCUENTO","NRO DE COBRO","ESTADO AMORTIZACIÓN")
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
                   ));
               }
               $export = new ArchivoPrimarioExport($data);
               return Excel::download($export, $File.'.xls');
  }

  /** @group Reportes de amortizaciones 
   * Reporte de amortizaciones en efectivo y deposito en cuenta  
   * Reporte de amortizaciones en efectivo y deposito en cuenta modalidad amortizacion directa  
   * @queryParam initial_date Fecha inicial. Example: 2021-01-01
   * @queryParam final_date Fecha Final. Example: 2021-05-01
   * @authenticated
   * @responseFile responses/reports_amortizations/dincuent_cash_deposit.200.json
   */
 public function report_amortization_cash_deposit(Request $request){
    // aumenta el tiempo máximo de ejecución de este script a 150 min:
    ini_set('max_execution_time', 9000);
    // aumentar el tamaño de memoria permitido de este script:
    ini_set('memory_limit', '960M');

    $order_loan = 'Desc';

    $conditions = [];
    //filtros
    $initial_date = request('initial_date') ?? '';
    $final_date = request('final_date') ?? '';
    $state_pagado='Pagado';
    $procedure_type='Directo';

    if ($initial_date != '') {
      array_push($conditions, array('view_loan_amortizations.estimated_date_loan_payment', '>=', "%{$initial_date}%"));
    }
    if ($final_date != '') {
      array_push($conditions, array('view_loan_amortizations.estimated_date_loan_payment', '<=', "%{$final_date}%"));
    }
    array_push($conditions, array('view_loan_amortizations.states_loan_payment', 'ilike', "%{$state_pagado}%"));
    array_push($conditions, array('view_loan_amortizations.modality_loan_payment', 'ilike', "%{$procedure_type}%"));
    
    $list_loan = DB::table('view_loan_amortizations')
                ->where($conditions)
                ->select('*')
                ->orderBy('code_loan', $order_loan)
                ->get();

      foreach ($list_loan as $loan) {
      $padron = Loan::where('id', $loan->id_loan)->first();
      $loan->modality=$padron->modality->procedure_type->second_name;
      $loan->sub_modality=$padron->modality->shortened;
      $loan->separation='***';
      }

      $File="ListadoAmortizaciones";
      $data=array(
      array("CI AFILIADO","MATRICULA AFILIADO","NOMBRE COMPLETO AFILIADO","***","COD PRÉSTAMO", "FECHA DE DESEMBOLSO", "TIPO","FECHA DE CALCULO","FECHA DE TRANSACCIÓN",
      "MODALIDAD PRÉSTAMO","SUB MODALIDAD PRÉSTAMO",
      "MATRICULA", "CI", "PRIMER NOMBRE","SEGUNDO NOMBRE","APELLIDO PATERNO","APELLIDO MATERNO","APELLIDO CASADA",
      "CAPITAL","INTERÉS CORRIENTE","INTERÉS PENAL","INTERÉS CORRIENTE PENDIENTE",
      "INTERÉS PENAL PENDIENTE","TOTAL PAGADO","SALDO ANTERIOR", "SALDO ACTUAL","PAGADO POR","TIPO DESCUENTO","TIPO DE PAGO","CBTE","NRO DE COBRO","ESTADO AMORTIZACIÓN")
      );
               foreach ($list_loan as $row){
                   array_push($data, array(

                    $row->identity_card_affiliate,
                      $row->registration_affiliate,
                      $row->full_name_affiliate,
                      $row->separation,
                      $row->code_loan,
                      Carbon::parse($row->disbursement_date_loan)->format('d/m/Y H:i:s'),//fecha de desembolso
                      $row->state_affiliate_loan_payment,

                      Carbon::parse($row->estimated_date_loan_payment)->format('d/m/Y'),//fecha de pago/calculo
                      Carbon::parse($row->date_loan_payment)->format('d/m/Y H:i:s'),//fecha de transacción
                      $row->modality, //MOdalidad
                      $row->sub_modality, //Sub modalidad
                      $row->registration_borrower,
                      $row->identity_card_borrower,
                      $row->first_name_borrower,
                      $row->second_name_borrower,
                      $row->last_name_borrower,
                      $row->mothers_last_name_borrower,
                      $row->surname_husband_borrower,

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
                      $row->voucher_type_loan_payment, //TIPO DE DESCUENTO EFECTIVO O DEP EN CUENTA
                      $row->voucher_loan_payment, //comprobante

                      $row->code_loan_payment, //Nro de cobro*/
                      $row->states_loan_payment,//estado del cobro
                   ));
               }
    $export = new ArchivoPrimarioExport($data);
    return Excel::download($export, $File.'.xls');
}

  /** @group Reportes de amortizaciones 
   * Reporte de amortizaciones por ajustes
   * Reporte de amortizaciones por ajustes modalidad amortizacion Ajuste
   * @queryParam initial_date Fecha inicial. Example: 2021-01-01
   * @queryParam final_date Fecha Final. Example: 2021-05-01
   * @authenticated
   * @responseFile responses/reports_amortizations/report_amortization_ajust.200.json
   */
 public function report_amortization_ajust(Request $request){
  // aumenta el tiempo máximo de ejecución de este script a 150 min:
  ini_set('max_execution_time', 9000);
  // aumentar el tamaño de memoria permitido de este script:
  ini_set('memory_limit', '960M');

  $order_loan = 'Desc';
  
  $conditions = [];
  //filtros
  $initial_date = request('initial_date') ?? '';
  $final_date = request('final_date') ?? '';
  $state_pagado='Pagado';
  $procedure_type='Amortización por Ajuste';

  if ($initial_date != '') {
    array_push($conditions, array('view_loan_amortizations.estimated_date_loan_payment', '>=', "%{$initial_date}%"));
  }
  if ($final_date != '') {
    array_push($conditions, array('view_loan_amortizations.estimated_date_loan_payment', '<=', "%{$final_date}%"));
  }
  array_push($conditions, array('view_loan_amortizations.states_loan_payment', 'ilike', "%{$state_pagado}%"));
  array_push($conditions, array('view_loan_amortizations.procedure_loan_payment', 'ilike', "%{$procedure_type}%"));
  
  $list_loan = DB::table('view_loan_amortizations')
              ->where($conditions)
              ->select('*')
              ->orderBy('code_loan', $order_loan)
              ->get();

  foreach ($list_loan as $loan) {
    $padron = Loan::where('id', $loan->id_loan)->first();
    $loan->modality=$padron->modality->procedure_type->second_name;
    $loan->sub_modality=$padron->modality->shortened;
  }

  $File="ListadoAmortizaciones";
  $data=array(
        array("NRO DE PRÉSTAMO", "FECHA DE DESEMBOLSO", "TIPO","FECHA DE PAGO","FECHA DE TRANSACCIÓN","MODALIDAD","SUB MODALIDAD",
              "MATRICULA AFILIADO", "CI AFILIADO", "NOMBRE COMPLETO AFILIADO", "***","MATRICULA TITULAR", "CI TITULAR","APELLIDO CASADA", "APELLIDO PATERNO","APELLIDO MATERNO",
              "PRIMER NOMBRE","SEGUNDO NOMBRE","CAPITAL","INTERÉS CORRIENTE","INTERÉS PENAL","INTERÉS CORRIENTE PENDIENTE", 
              "INTERÉS PENAL PENDIENTE","TOTAL PAGADO","SALDO ANTERIOR", "SALDO ACTUAL","PAGADO POR","TIPO DESCUENTO","CBTE","NRO DE COBRO")
             );
  foreach ($list_loan as $row){
    array_push($data, array(
                $row->code_loan,//nro de prestamo
                Carbon::parse($row->disbursement_date_loan)->format('d/m/Y H:i:s'),//fecha de desembolso
                $row->state_type_affiliate,//tipo (pasivo o activo)
                           
                Carbon::parse($row->estimated_date_loan_payment)->format('d/m/Y'),//fecha de pago
                Carbon::parse($row->date_loan_payment)->format('d/m/Y H:i:s'),//fecha de transacción
                $row->modality, //MOdalidad
                $row->sub_modality, //Sub modalidad

                $row->registration_affiliate,
                $row->identity_card_affiliate,
                $row->full_name_affiliate,
                "***",
                $row->registration_borrower,//matricula afiliado
                $row->identity_card_borrower,//matricula esposa

                $row->surname_husband_borrower,//ap casada
                $row->last_name_borrower,//ap paterno
                $row->mothers_last_name_borrower,//ap materno
                
                $row->first_name_borrower,//primer nombre
                $row->second_name_borrower,//segundo nombre
                Util::money_format($row->capital_payment),//capital .. pagado
                Util::money_format($row->interest_payment), //interes corriente 
                Util::money_format($row->penal_payment),// interes penal 
                Util::money_format($row->interest_remaining),//interes corriente pendiente
                Util::money_format($row->penal_remaining),//interes penal pendiente 
                Util::money_format($row->quota_loan_payment),//total pagado 
                Util::money_format($row->previous_balance),// saldo anterior 
                Util::money_format($row->current_balance),//saldo actual 

                $row->paid_by_loan_payment,//pagado por 
                $row->modality_shortened_loan_payment,// tipo de descuento 

                $row->voucher_type_loan_payment, //comprobante

                $row->code_loan_payment, //Nro de cobro
             ));
    }
    $export = new ArchivoPrimarioExport($data);
    return Excel::download($export, $File.'.xls');
  }
 /** @group Reportes de amortizaciones 
   * Reporte de amortizaciones por complemento y fondo de retiro  
   * Reporte de amortizaciones amortizaciones por complemento y fondo de retiro 
   * @queryParam initial_date Fecha inicial. Example: 2021-01-01
   * @queryParam final_date Fecha Final. Example: 2021-05-01
   * @authenticated
   * @responseFile responses/reports_amortizations/report_complement_fondo.200.json
   */
  public function report_amortization_fondo_complement(Request $request)
  {
      // aumenta el tiempo máximo de ejecución de este script a 150 min:
      ini_set('max_execution_time', 9000);
      // aumentar el tamaño de memoria permitido de este script:
      ini_set('memory_limit', '960M');

      $order_loan = 'Desc';

      $conditions = [];
      $conditions_fondo = [];
      //filtros
      $initial_date = request('initial_date') ?? '';
      $final_date = request('final_date') ?? '';
      $state_pagado='Pagado';
      $procedure_type='Complemento Económico';

      $procedure_type_fr='Amortización Fondo de Retiro';
      if ($initial_date != '') {
          array_push($conditions, array('view_loan_amortizations.estimated_date_loan_payment', '>=', "%{$initial_date}%"));
          array_push($conditions_fondo, array('view_loan_amortizations.estimated_date_loan_payment', '>=', "%{$initial_date}%"));
      }
      if ($final_date != '') {
          array_push($conditions, array('view_loan_amortizations.estimated_date_loan_payment', '<=', "%{$final_date}%"));
          array_push($conditions_fondo, array('view_loan_amortizations.estimated_date_loan_payment', '<=', "%{$final_date}%"));
      }
      array_push($conditions, array('view_loan_amortizations.procedure_loan_payment', 'ilike', "%{$procedure_type}%"));
      array_push($conditions_fondo, array('view_loan_amortizations.procedure_loan_payment', 'ilike', "%{$procedure_type_fr}%"));


      $list_loan = DB::table('view_loan_amortizations')
                    ->where($conditions)
                    ->select('*')
                    ->orderBy('code_loan', $order_loan)
                    ->get();

      foreach ($list_loan as $loan) {
          $padron = Loan::where('id', $loan->id_loan)->first();
          $loan->modality=$padron->modality->procedure_type->second_name;
          $loan->sub_modality=$padron->modality->shortened;
          $loan->separation='***';
      }
      $File="ListadoAmortizaciones";
      $data=array(
                 array("CI AFILIADO","MATRICULA AFILIADO","NOMBRE COMPLETO AFILIADO","***","COD PRÉSTAMO", "FECHA DE DESEMBOLSO", "TIPO","FECHA DE CALCULO","FECHA DE TRANSACCIÓN",
                 "MODALIDAD PRÉSTAMO","SUB MODALIDAD PRÉSTAMO",
                 "MATRICULA", "CI", "PRIMER NOMBRE","SEGUNDO NOMBRE","APELLIDO PATERNO","APELLIDO MATERNO","APELLIDO CASADA",
                 "CAPITAL","INTERÉS CORRIENTE","INTERÉS PENAL","INTERÉS CORRIENTE PENDIENTE",
                 "INTERÉS PENAL PENDIENTE","TOTAL PAGADO","SALDO ANTERIOR", "SALDO ACTUAL","PAGADO POR","TIPO DESCUENTO","CBTE","NRO DE COBRO","ESTADO AMORTIZACIÓN")
             );
      foreach ($list_loan as $row) {
          array_push($data, array(

                  $row->identity_card_affiliate,
                  $row->registration_affiliate,
                  $row->full_name_affiliate,
                  $row->separation,
                  $row->code_loan,
                  Carbon::parse($row->disbursement_date_loan)->format('d/m/Y H:i:s'),//fecha de desembolso
                  $row->state_affiliate_loan_payment,

                  Carbon::parse($row->estimated_date_loan_payment)->format('d/m/Y'),//fecha de pago/calculo
                  Carbon::parse($row->date_loan_payment)->format('d/m/Y H:i:s'),//fecha de transacción
                  $row->modality, //MOdalidad
                  $row->sub_modality, //Sub modalidad
                  $row->registration_borrower,
                  $row->identity_card_borrower,
                  $row->first_name_borrower,
                  $row->second_name_borrower,
                  $row->last_name_borrower,
                  $row->mothers_last_name_borrower,
                  $row->surname_husband_borrower,

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
                  $row->voucher_loan_payment, //comprobante

                  $row->code_loan_payment, //Nro de cobro*/
                  $row->states_loan_payment//estado del cobro
                 ));
      }

      //fondo de retiro
      $list_loan_fondo = DB::table('view_loan_amortizations')
             ->where($conditions_fondo)
             ->select('*')
             ->orderBy('code_loan', $order_loan)
             ->get();

      foreach ($list_loan_fondo as $loan) {
          $padron = Loan::where('id', $loan->id_loan)->first();
          $loan->modality=$padron->modality->procedure_type->second_name;
          $loan->sub_modality=$padron->modality->shortened;
          $loan->separation='***';
      }

      $File="ListadoAmortizaciones";
      $data_fondo=array(
              array("CI AFILIADO","MATRICULA AFILIADO","NOMBRE COMPLETO AFILIADO","***","COD PRÉSTAMO", "FECHA DE DESEMBOLSO", "TIPO","FECHA DE CALCULO","FECHA DE TRANSACCIÓN",
              "MODALIDAD PRÉSTAMO","SUB MODALIDAD PRÉSTAMO",
              "MATRICULA", "CI", "PRIMER NOMBRE","SEGUNDO NOMBRE","APELLIDO PATERNO","APELLIDO MATERNO","APELLIDO CASADA",
              "CAPITAL","INTERÉS CORRIENTE","INTERÉS PENAL","INTERÉS CORRIENTE PENDIENTE",
              "INTERÉS PENAL PENDIENTE","TOTAL PAGADO","SALDO ANTERIOR", "SALDO ACTUAL","PAGADO POR","TIPO DESCUENTO","CBTE","NRO DE COBRO","ESTADO AMORTIZACIÓN")
          );
      foreach ($list_loan_fondo as $row) {
          array_push($data_fondo, array(

                  $row->identity_card_affiliate,
                  $row->registration_affiliate,
                  $row->full_name_affiliate,
                  $row->separation,
                  $row->code_loan,
                  Carbon::parse($row->disbursement_date_loan)->format('d/m/Y H:i:s'),//fecha de desembolso
                  $row->state_affiliate_loan_payment,

                  Carbon::parse($row->estimated_date_loan_payment)->format('d/m/Y'),//fecha de pago/calculo
                  Carbon::parse($row->date_loan_payment)->format('d/m/Y H:i:s'),//fecha de transacción
                  $row->modality, //MOdalidad
                  $row->sub_modality, //Sub modalidad
                  $row->registration_borrower,
                  $row->identity_card_borrower,
                  $row->first_name_borrower,
                  $row->second_name_borrower,
                  $row->last_name_borrower,
                  $row->mothers_last_name_borrower,
                  $row->surname_husband_borrower,

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
                  $row->voucher_loan_payment, //comprobante

                  $row->code_loan_payment, //Nro de cobro*/
                  $row->states_loan_payment//estado del cobro
                 ));
      }

      $export = new MultipleSheetExportPayment($data, $data_fondo, 'COM-ECO', 'FRP');
      return Excel::download($export, $File.'.xls');
  }
    /** @group Reportes de amortizaciones 
   * Reporte de amortizaciones pendientes de confirmacion deacuerdo al comprobante de generacion
   * Reporte de amortizaciones por ajustes modalidad amortizacion Ajuste
   * @queryParam initial_date Fecha inicial. Example: 2021-01-01
   * @queryParam final_date Fecha Final. Example: 2021-05-01
   * @authenticated
   * @responseFile responses/reports_amortizations/report_amortization_ajust.200.json
   */
 public function report_amortization_pending_confirmation(Request $request){
  // aumenta el tiempo máximo de ejecución de este script a 150 min:
  ini_set('max_execution_time', 9000);
  // aumentar el tamaño de memoria permitido de este script:
  ini_set('memory_limit', '960M');

  $order_loan = 'Desc';
  
  $conditions = [];
  //filtros
  $initial_date = request('initial_date') ?? '';
  $final_date = request('final_date') ?? '';
  $state_pagado='Pendiente por confirmar';

  if ($initial_date != '') {
    array_push($conditions, array('view_loan_amortizations.estimated_date_loan_payment', '>=', "%{$initial_date}%"));
  }
  if ($final_date != '') {
    array_push($conditions, array('view_loan_amortizations.estimated_date_loan_payment', '<=', "%{$final_date}%"));
  }
  array_push($conditions, array('view_loan_amortizations.states_loan_payment', 'like', "%{$state_pagado}%"));
  //

        $list_loan = DB::table('view_loan_amortizations')
                          ->where($conditions)
                          ->select('*')
                          ->orderBy('code_loan', $order_loan)
                          ->get();

            foreach ($list_loan as $loan) {
                $padron = Loan::where('id', $loan->id_loan)->first();
                $loan->modality=$padron->modality->procedure_type->second_name;
                $loan->sub_modality=$padron->modality->shortened;
                $loan->separation='***';
            }
             $File="ListadoAmortizaciones";
             $data=array(
              array("CI AFILIADO","MATRICULA AFILIADO","NOMBRE COMPLETO AFILIADO","***","COD PRÉSTAMO", "FECHA DE DESEMBOLSO", "TIPO","FECHA DE CALCULO","FECHA DE TRANSACCIÓN",
              "MODALIDAD PRÉSTAMO","SUB MODALIDAD PRÉSTAMO",
              "MATRICULA", "CI", "PRIMER NOMBRE","SEGUNDO NOMBRE","APELLIDO PATERNO","APELLIDO MATERNO","APELLIDO CASADA",
              "CAPITAL","INTERÉS CORRIENTE","INTERÉS PENAL","INTERÉS CORRIENTE PENDIENTE",
              "INTERÉS PENAL PENDIENTE","TOTAL PAGADO","SALDO ANTERIOR", "SALDO ACTUAL","PAGADO POR","TIPO DESCUENTO","CBTE","NRO DE COBRO","ESTADO AMORTIZACIÓN")
          );
             foreach ($list_loan as $row){
                 array_push($data, array(
                  $row->identity_card_affiliate,
                  $row->registration_affiliate,
                  $row->full_name_affiliate,
                  $row->separation,
                  $row->code_loan,
                  Carbon::parse($row->disbursement_date_loan)->format('d/m/Y H:i:s'),//fecha de desembolso
                  $row->state_affiliate_loan_payment,

                  Carbon::parse($row->estimated_date_loan_payment)->format('d/m/Y'),//fecha de pago/calculo
                  Carbon::parse($row->date_loan_payment)->format('d/m/Y H:i:s'),//fecha de transacción
                  $row->modality, //MOdalidad
                  $row->sub_modality, //Sub modalidad
                  $row->registration_borrower,
                  $row->identity_card_borrower,
                  $row->first_name_borrower,
                  $row->second_name_borrower,
                  $row->last_name_borrower,
                  $row->mothers_last_name_borrower,
                  $row->surname_husband_borrower,

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
                  $row->voucher_loan_payment, //comprobante

                  $row->code_loan_payment, //Nro de cobro*/
                  $row->states_loan_payment//estado del cobro
                 ));
             }
             $export = new ArchivoPrimarioExport($data);
             return Excel::download($export, $File.'.xls');
  }
  


}
