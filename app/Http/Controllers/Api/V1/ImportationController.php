<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ArchivoPrimarioExport;
use Carbon;
use Illuminate\Support\Facades\Log;

/** @group Importacion de datos C o S
* Importacion de datos Comando  o Senasir
*/

class ImportationController extends Controller
{

   /**
    * Agrupar montos de afiliados
    * Agrupar montos de afiliadoso
    * @queryParam origin required Tipo de importacion C (Comando general) o S (Senasir). Example: C
    * @queryParam period required id_del periodo . Example: 1
    * @authenticated
    * @responseFile responses/importation/importation_agroup.200.json
     */

 public function agruped_payments(Request $request){

    DB::beginTransaction();
    try {

        $request->validate([
            'origin'=>'required|string',
            'period'=>'required|exists:periods,id'
        ]);
    $data = array();
    $count_affiliate = 0;
    $count_no_exist = 0;
    $validado = false;
    $origin = $request->origin; $period = $request->period;//entradas
        if($origin == 'C'){
            $query = "  SELECT import_command_payments.identity_card as identity_card, sum(amount) as amount
                        FROM import_command_payments
                        where import_command_payments.period_id = '$period'
                        group by import_command_payments.identity_card";

            $payment_agroups = DB::select($query);

            foreach($payment_agroups as $payment_agroup){
                $affiliate_id = $this->serch_id($payment_agroup->identity_card);
                if($affiliate_id != 0){
                    DB::table("command_payment_groups")
                    ->insert([
                      "affiliate_id" => $affiliate_id,
                      "period_id" => 1,
                      "identity_card" =>$payment_agroup->identity_card,
                      "amount" => $payment_agroup->amount,
                      "amount_balance" => $payment_agroup->amount,
                    ]);
                   Log::info('Registro agrupado de affiliado con Id: '.$affiliate_id);
                  $count_affiliate++;
                }else{

                    $data_loan =  $payment_agroup;
                    array_push($data, $data_loan);
                    $count_no_exist++;
                }
             }

        }else{
            if($origin == 'S'){

            $query = "  SELECT import_senasir_payments.registration as registration, import_senasir_payments.registration_dh as registration_dh, sum(amount) as amount
                        FROM import_senasir_payments
                        where import_senasir_payments.period_id = '$period'
                        group by import_senasir_payments.registration, import_senasir_payments.registration_dh";

            $payment_agroups = DB::select($query);

            foreach($payment_agroups as $payment_agroup){
                $affiliate_id = $this->serch_id($payment_agroup->registration);
                if($affiliate_id != 0){
                    DB::table("senasir_payment_groups")
                    ->insert([
                      "affiliate_id" => $affiliate_id,
                      "period_id" => 1,
                      "registration" =>$payment_agroup->registration,
                      "registration_dh" =>$payment_agroup->registration_dh,
                      "amount" => $payment_agroup->amount,
                      "amount_balance" => $payment_agroup->amount,
                      "created_at" =>Carbon::now(),
                      "updated_at" =>Carbon::now(),
                    ]);
                  $count_affiliate++;

                }else{
                    $data_loan =  $payment_agroup;
                    array_push($data, $data_loan);
                    $count_no_exist++;
                }
            }

            }else{
                abort(409, 'Incorrecto! Debe enviar C(Comando General) ó S(Senasir)');
            }
        }

        //verificar exixtencia de afiliados
            if($count_no_exist > 0){
                DB::rollback();
                //DB::commit();//eliminar
                Log::info('Cantidad de registros no existentes: '.$count_no_exist);

               $FileC="DatosNoEncontradosComando"; $FileS="DatosNoEncontradosSenasir";

               $data_cabeceraC=array(array("NRO de CARNET", "MONTO TOTAL"));
               $data_cabeceraS=array(array("MATRÍCULA", "MATRÍCULA D_H", "MONTO TOTAL"));

               foreach ($data as $row){
                   if($origin == 'C'){
                        array_push($data_cabeceraC, array($row->identity_card, $row->amount));
                   }else{
                        array_push($data_cabeceraS, array($row->registration, $row->registration_dh, $row->amount));
                   }
               }
               if($origin == 'C'){
                $export = new ArchivoPrimarioExport($data_cabeceraC);
                return Excel::download($export, $FileC.'.xls');
               }else{
                $export = new ArchivoPrimarioExport($data_cabeceraS);
                return Excel::download($export, $FileS.'.xls');
               }
               // return  response()->json(['count_affiliates_not_exist'=>$count_no_exist, 'Affiliates_regularize'=>$data]);
            }else{
                if($count_affiliate > 0){
                    $validado =true;
                    DB::commit();
                return  response()->json(['message' =>'Validación de datos realizado con exito!','validated_agroup'=>$validado,'count_affilites'=>$count_affiliate]);

                }else{
                    DB::commit();
                return  response()->json(['message' =>'Validación de datos incorrecto! no se encontraron datos por agrupar','validated_agroup'=>$validado,'count_affilites'=>$count_affiliate]);
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
           //throw $e;
           return ['message' => $e->getMessage()];
        }
   }

   //buscar id y ci
   public function serch_id($ci){
       //$ci= '1700723';
       $ci=$ci;

        $query = "  SELECT affiliates.id as id
                    FROM affiliates
                    where affiliates.identity_card = '$ci'
                    or affiliates.registration = '$ci'";

        $query_spouse = "  SELECT spouses.affiliate_id as affiliate_id
                    FROM spouses
                    where spouses.identity_card = '$ci'
                    or spouses.registration = '$ci'";
        $affiliate =DB::select($query);
        if($affiliate){
            return $affiliate[0]->id;
        }
        else{
            $spouse = DB::select($query_spouse);
            if($spouse){
                return $spouse[0]->affiliate_id;
            }else
                return 0; //no existe el ci
        }
    }

     //importacion de cobros 
     public function importation_payment(Request $request){
        $origin = 'C';$period =1;
        $query = "  SELECT *
                    FROM payment_groupeds
                    where payment_groupeds.origin = '$origin'
                    and payment_groupeds.period_id = '$period'";

       $payment_agroups = DB::select($query);
       foreach($payment_agroups as $payment_agroup){
        return $payment_agroup->id;
       }
        return $payment_agroups;
    }

    //prestamos por  afiliado
    public function loan_lenders($id_affiliate){

        $query = " SELECT loans.*
                    FROM loans
                    join loan_affiliates ON loan_affiliates.loan_id = loans.id
                    join affiliates ON affiliates.id = loan_affiliates.affiliate_id
                    join loan_states ON loan_states.id = loans.state_id
                    where loan_affiliates.affiliate_id = '$id_affiliate'
                    and  loan_affiliates.guarantor = false
                    and  loan_states.name = 'Vigente'
                    and  loans.guarantor_amortizing = false
                    order by loans.disbursement_date desc";

        $loan_lenders = DB::select($query);
        return $loan_lenders;
    }
    //prestamos por  guarantor
    public function loan_guarantors($id_affiliate){
        $query = " SELECT loans.*
                    FROM loans
                    join loan_affiliates ON loan_affiliates.loan_id = loans.id
                    join affiliates ON affiliates.id = loan_affiliates.affiliate_id
                    join loan_states ON loan_states.id = loans.state_id
                    where loan_affiliates.affiliate_id = '$id_affiliate'
                    and  loan_affiliates.guarantor = true
                    and  loan_states.name = 'Vigente'
                    and  loans.guarantor_amortizing = true
                    order by loans.disbursement_date desc";

       $loan_guarantors= DB::select($query);
        return $loan_guarantors;
    }
}
