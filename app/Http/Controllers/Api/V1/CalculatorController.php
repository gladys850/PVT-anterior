<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Affiliate;
use Util;
use App\ProcedureModality;
use App\Loan;
use App\Http\Requests\CalculatorForm;
use App\Http\Requests\SimulatorForm;



/** @group Calculadora
* Simulador de la calculadora
*/
class CalculatorController extends Controller
{
    /**
    * Liquido para calificación
    * @bodyParam liquid_calification[0].affiliate_id integer required ID del afiliado. Example: 9389
    * @bodyParam liquid_calification[0].parent_loan_id integer ID de Préstamo Padre. Example: 6
    * @bodyParam liquid_calification[0].contributions[0].payable_liquid integer required Líquido pagable. Example: 2000
    * @bodyParam liquid_calification[0].contributions[0].seniority_bonus integer required Bono Cargo . Example: 0
    * @bodyParam liquid_calification[0].contributions[0].border_bonus integer required Bono Frontera . Example: 0
    * @bodyParam liquid_calification[0].contributions[0].public_security_bonus integer required Bono Seguridad Ciudadana . Example: 300
    * @bodyParam liquid_calification[0].contributions[0].east_bonus integer required Bono Oriente. Example: 0
    * @bodyParam liquid_calification[0].contributions[1].payable_liquid integer Líquido pagable. Example: 3000
    * @bodyParam liquid_calification[0].contributions[1].seniority_bonus integer Bono Cargo . Example: 0
    * @bodyParam liquid_calification[0].contributions[1].border_bonus integer Bono Frontera . Example: 0
    * @bodyParam liquid_calification[0].contributions[1].public_security_bonus integer Bono Seguridad Ciudadana . Example: 300
    * @bodyParam liquid_calification[0].contributions[1].east_bonus integer Bono Oriente. Example: 0
    * @bodyParam liquid_calification[0].contributions[2].payable_liquid integer Líquido pagable. Example: 3500
    * @bodyParam liquid_calification[0].contributions[2].seniority_bonus integer Bono Cargo . Example: 0
    * @bodyParam liquid_calification[0].contributions[2].border_bonus integer Bono Frontera . Example: 0
    * @bodyParam liquid_calification[0].contributions[2].public_security_bonus integer Bono Seguridad Ciudadana . Example: 300
    * @bodyParam liquid_calification[0].contributions[2].east_bonus integer Bono Oriente. Example: 0
    * @bodyParam liquid_calification[1].affiliate_id integer required ID del afiliado. Example: 1
    * @bodyParam liquid_calification[1].parent_loan_id integer ID de Préstamo Padre. Example: 6
    * @bodyParam liquid_calification[1].contributions[0].payable_liquid integer required Líquido pagable. Example: 2000
    * @bodyParam liquid_calification[1].contributions[0].seniority_bonus integer required Bono Cargo . Example: 0
    * @bodyParam liquid_calification[1].contributions[0].border_bonus integer required Bono Frontera . Example: 0
    * @bodyParam liquid_calification[1].contributions[0].public_security_bonus integer required Bono Seguridad Ciudadana . Example: 300
    * @bodyParam liquid_calification[1].contributions[0].east_bonus integer required Bono Oriente. Example: 0
    * @authenticated
    * @responseFile responses/calculator/store.200.json
    */
    public function store(CalculatorForm $request)
    {
        $liquid_calification = $request->liquid_calification;
        $liquid_calificated = collect([]);
        foreach($liquid_calification as $liq){
            $affiliate = Affiliate::findOrFail($liq['affiliate_id']);
            if($request->has('parent_loan_id')){
                $parent_loan = Loan::findOrFail($liq['parent_loan_id']);
                if (!$parent_loan) abort(404);
                $parent_quota = $parent_loan->next_payment()->estimated_quota * $parent_loan->lenders->find($liq['affiliate_id'])->pivot->payment_percentage/100;
            }else{
                $parent_quota = 0;
            }
            $contributions = $liq['contributions'];
            $contributions = collect($contributions);
            $payable_liquid_average = $contributions->avg('payable_liquid');
            $contribution_first = $contributions->first();// se obtiene los bonos de la ultima boleta
            $total_bonuses = $contribution_first['seniority_bonus']+$contribution_first['border_bonus']+$contribution_first['public_security_bonus']+$contribution_first['east_bonus'];
            $liquid_qualification_calculated = $this->liquid_qualification($payable_liquid_average, $total_bonuses, $affiliate, $parent_quota);
            $liquid_calificated->push([
                'affiliate_id' => $affiliate->id,
                'payable_liquid_calculated' => round($payable_liquid_average,2),
                'bonus_calculated' => round($total_bonuses,2),
                'liquid_qualification_calculated' => round($liquid_qualification_calculated,2),
                'quota_refinance' => round($parent_quota,2)
            ]);
        }
        return $liquid_calificated;
    }
    /**
    * Simulador
    * @bodyParam procedure_modality_id integer required ID de modalidad. Example: 41
    * @bodyParam amount_requested integer required monto solicitado. Example: 39000
    * @bodyParam months_term integer required plazo. Example: 30
    * @bodyParam liquid_qualification_calculated_lender float liquido para calificacion del titular en caso de evaluación como garantes. Example: 3500
    * @bodyParam guarantor boolean Afiliados evaluados como garantes. Example: true
    * @bodyParam liquid_calculated[0].affiliate_id integer required ID del afiliado. Example: 9389
    * @bodyParam liquid_calculated[0].liquid_qualification_calculated float required liquido para calificación calculada Example: 2200.5
    * @bodyParam liquid_calculated[1].affiliate_id integer required ID del afiliado. Example: 1
    * @bodyParam liquid_calculated[1].liquid_qualification_calculated float required liquido para calificación calculada Example: 2700.6
    * @authenticated
    * @responseFile responses/calculator/simulator.200.json
    */
    public function simulator(SimulatorForm $request){
        $modality = ProcedureModality::findOrFail($request->procedure_modality_id);
        $amount_requested = $request->amount_requested;
        $liquid_calculated = collect($request->liquid_calculated);
        $calculated_data = collect([]);
        if($request->guarantor)
        {
            if(count($liquid_calculated) != $modality->loan_modality_parameter->guarantors)abort(403, 'La cantidad de garantes no corresponde a la modalidad');
            //calculo de totales para la cabecera
            $debt_index = $modality->loan_modality_parameter->debt_index;
            $liquid_qualification_calculated_lender = $request->liquid_qualification_calculated_lender;
            $months_term = $request->months_term;
            $quota_calculated_total = $this->quota_calculator($modality, $request->months_term, $amount_requested);
            $amount_maximum_suggested = $this->maximum_amount($modality,$request->months_term,$liquid_qualification_calculated_lender);
            if($amount_requested>$amount_maximum_suggested){
                $quota_calculated_total = $this->quota_calculator($modality, $request->months_term, $amount_maximum_suggested);
                $amount_requested = $amount_maximum_suggested;
            }
            $maximum_suggested_valid = false;
            if($modality->procedure_type->interval->minimum_amount<=$amount_maximum_suggested && $amount_maximum_suggested<=$modality->procedure_type->interval->maximum_amount) $maximum_suggested_valid = true;
            $indebtedness_calculated_total=round((($quota_calculated_total/$liquid_qualification_calculated_lender)*100),2);
            $evaluate = false;
            if ($indebtedness_calculated_total<=$debt_index) $evaluate=true;
            //calculo de garantes
            $quantity_guarantors = count($liquid_calculated);
            $quota_calculated = $quota_calculated_total/$quantity_guarantors;
            $c=1;$percentage = 0;
            foreach($liquid_calculated as $liquid){
                if($quantity_guarantors && $request->liquid_qualification_calculated_lender >0)
                $indebtedness_calculated = $quota_calculated/$liquid['liquid_qualification_calculated']*100;
                if($quantity_guarantors%2==0){
                    $percentage_payment = intval($quota_calculated*100/$quota_calculated_total);
                }else{
                    if($c<$quantity_guarantors){
                        $percentage_payment = intval($quota_calculated*100/$quota_calculated_total);
                        $c++;$percentage = $percentage + $percentage_payment;
                    }else{
                        $percentage_payment = 100-$percentage;
                    }
                }
                $calculated_data->push([
                    'affiliate_id' => $liquid['affiliate_id'],
                    'quota_calculated' => Util::money_format($quota_calculated),
                    'indebtedness_calculated' => round($indebtedness_calculated,2),
                    'payment_percentage' => $percentage_payment,
                    'liquid_qualification_calculated' => $liquid['liquid_qualification_calculated'],
                    'is_valid' => ($indebtedness_calculated) <= ($modality->loan_modality_parameter->decimal_index)*100
                ]);
            }
            $response = $this->header($quota_calculated_total,$indebtedness_calculated_total,$request->amount_requested,$months_term,$evaluate,$liquid_qualification_calculated_lender,$amount_maximum_suggested,$maximum_suggested_valid,$calculated_data);
        }
        else{
            $modality = ProcedureModality::find($request->procedure_modality_id);
            if($modality->procedure_type->name == 'Préstamo Anticipo' || $modality->procedure_type->name == 'Préstamo a corto plazo' || $modality->procedure_type->name == 'Préstamo a largo plazo'){
                if(count($liquid_calculated)>$modality->loan_modality_parameter->max_lenders)abort(403, 'La cantidad de titulares no corresponde a la modalidad');
                foreach($liquid_calculated as $liquid){
                    $quota_calculated = $this->quota_calculator($modality, $request->months_term, $amount_requested);
                    $amount_maximum_suggested = $this->maximum_amount($modality,$request->months_term,$liquid['liquid_qualification_calculated']);
                    if($amount_requested>$amount_maximum_suggested){
                        $quota_calculated = $this->quota_calculator($modality, $request->months_term, $amount_maximum_suggested);
                        $amount_requested = $amount_maximum_suggested;
                    }
                    $maximum_suggested_valid = false;
                    if($modality->procedure_type->interval->minimum_amount<=$amount_maximum_suggested && $amount_maximum_suggested<=$modality->procedure_type->interval->maximum_amount) $maximum_suggested_valid = true;
                    $indebtedness_calculated = $quota_calculated/$liquid['liquid_qualification_calculated']*100;$valuate = false;
                    if(($indebtedness_calculated) <= ($modality->loan_modality_parameter->decimal_index)*100) $valuate = true;
                    $calculated_data->push([
                        'affiliate_id' => $liquid['affiliate_id'],
                        'quota_calculated' => Util::money_format($quota_calculated),
                        'indebtedness_calculated' => round($indebtedness_calculated,2),
                        'payment_percentage' => 100,
                        'liquid_qualification_calculated' => $liquid['liquid_qualification_calculated'],
                        'is_valid' =>$valuate
                    ]);
                }
                $response = $this->header($quota_calculated,$indebtedness_calculated,$request->amount_requested,$request->months_term,$valuate,$liquid['liquid_qualification_calculated'],$amount_maximum_suggested,$maximum_suggested_valid,$calculated_data);


            }else{
                if(count($liquid_calculated)>$modality->loan_modality_parameter->max_lenders)abort(403, 'La cantidad de titulares no corresponde a la modalidad');
                $response = $this->loan_percent($request);
            }
        }
        return $response;
    }
    // funcion para sacar la cuota estimada con la calculadora
    private function quota_calculator($procedure_modality, $months_term, $amount_requested){
        $interest_rate = $procedure_modality->current_interest->monthly_current_interest;
        return ((($interest_rate)/(1-(1/pow((1+$interest_rate),$months_term))))*$amount_requested);
    }
    // liquido para calificacion
    private function liquid_qualification($payable_liquid_average, $total_bonuses, $affiliate, $parent_quota){
        $active_guarantees = $affiliate->active_guarantees(); $sum_quota_guarantor = 0;
        foreach($active_guarantees as $res){
            $sum_quota_guarantor+= ($res->estimated_quota*$res->pivot->payment_percentage)/100; // descuento en caso de tener garantias activas
        }
        $liquid_qualification_calculated = $payable_liquid_average - $total_bonuses - $sum_quota_guarantor + $parent_quota;
        return $liquid_qualification_calculated;
    }
    // monto maximo
    private function maximum_amount($procedure_modality,$months_term,$liquid_qualification_calculated){
        $interest_rate = $procedure_modality->current_interest->monthly_current_interest;
        $loan_interval = $procedure_modality->procedure_type->interval;
        $debt_index = $procedure_modality->loan_modality_parameter->decimal_index;
        $maximum_qualified_amount = intval((1-(1/pow((1+$interest_rate),$months_term)))*($debt_index*$liquid_qualification_calculated)/$interest_rate);
        if ($maximum_qualified_amount > ($loan_interval->maximum_amount)){
            $maximum_qualified_amount = $loan_interval->maximum_amount;
        } else {
            $maximum_qualified_amount = $maximum_qualified_amount;
        }
        return $maximum_qualified_amount;
        //return intval(round(floor($maximum_qualified_amount))/100)*100;
    }
    //toda la información de abajo se debe borrar hasta que el front arregle
    /**
    * Calculadora
    * @bodyParam procedure_modality_id integer required ID de modalidad. Example: 34
    * @bodyParam amount_requested integer required monto solicitado. Example: 5000
    * @bodyParam months_term integer required plazo. Example: 5
    * @bodyParam affiliate_id integer required ID del afiliado. Example: 1
    * @bodyParam parent_loan_id integer ID de Préstamo Padre.. Example: 1
    * @bodyParam guarantor boolean Afiliado evaluado como garante. Example: false
    * @bodyParam contributions[0].payable_liquid integer required Líquido pagable. Example: 2000
    * @bodyParam contributions[0].seniority_bonus integer required Bono Cargo . Example: 0.00
    * @bodyParam contributions[0].border_bonus integer required Bono Frontera . Example: 0.00
    * @bodyParam contributions[0].public_security_bonus integer required Bono Seguridad Ciudadana . Example: 0.00
    * @bodyParam contributions[0].east_bonus integer required Bono Oriente. Example: 0.00
    * @bodyParam contributions[1].payable_liquid integer Líquido pagable. Example: 2270
    * @bodyParam contributions[1].seniority_bonus integer Bono Cargo . Example: 0.00
    * @bodyParam contributions[1].border_bonus integer Bono Frontera . Example: 0.00
    * @bodyParam contributions[1].public_security_bonus integer Bono Seguridad Ciudadana . Example: 0.00
    * @bodyParam contributions[1].east_bonus integer Bono Oriente. Example: 0.00
    * @bodyParam contributions[2].payable_liquid integer Líquido pagable. Example: 1563
    * @bodyParam contributions[2].seniority_bonus integer Bono Cargo . Example: 0.00
    * @bodyParam contributions[2].border_bonus integer Bono Frontera . Example: 0.00
    * @bodyParam contributions[2].public_security_bonus integer Bono Seguridad Ciudadana . Example: 0.00
    * @bodyParam contributions[2].east_bonus integer Bono Oriente. Example: 0.00
    * @authenticated
    * @responseFile responses/calculator/store.200.json
    */
    public function calculator(Request $request)
    {
        $procedure_modality = ProcedureModality::findOrFail($request->procedure_modality_id);
        $amount_requested = $request->amount_requested;
        $affiliate = Affiliate::findOrFail($request->affiliate_id);
        if ($request->has('parent_loan_id')) {
            $parent_loan = Loan::with(['lenders'=> function($q) use ($affiliate) {
                $q->where('affiliate_id', $affiliate->id);
            }])->whereId($request->parent_loan_id)->first();
            if (!$parent_loan) abort(404);
            $parent_quota = $parent_loan->next_payment()->estimated_quota * $parent_loan->lenders[0]->pivot->payment_percentage/100;
        } else {
            $parent_quota = 0;
        }
        $contributions = collect($request->contributions);
        $payable_liquid_average = $contributions->avg('payable_liquid');
        $contribution_first = $contributions->first();// se obtiene los bonos de la ultima boleta
        $total_bonuses = $contribution_first['seniority_bonus']+$contribution_first['border_bonus']+$contribution_first['public_security_bonus']+$contribution_first['east_bonus'];
        $liquid_qualification_calculated = $this->liquid_qualification_borrar($payable_liquid_average, $total_bonuses, $affiliate, $parent_quota, $request->guarantor);
        $quota_calculated = $this->quota_calculator_borrar($procedure_modality, $request->months_term, $amount_requested);

        $amount_maximum_suggested = $this->maximum_amount_borrar($procedure_modality,$request->months_term,$liquid_qualification_calculated);
       
       
        //return $amount_maximum_suggested;
        if($amount_requested>$amount_maximum_suggested){
            $quota_calculated = $this->quota_calculator_borrar($procedure_modality, $request->months_term, $amount_maximum_suggested);
            $amount_requested = $amount_maximum_suggested;
        }
        if($payable_liquid_average!=0){
            $indebtedness_calculated =$quota_calculated/($liquid_qualification_calculated)*100 ;
        }else{
            $indebtedness_calculated = 0;
        }
        return response()->json([
            'payable_liquid_calculated' => round($payable_liquid_average),
            'bonus_calculated' => $total_bonuses,
            'liquid_qualification_calculated' => round($liquid_qualification_calculated),
            'quota_calculated' => Util::money_format($quota_calculated),
            'indebtedness_calculated' => intval($indebtedness_calculated),
            'amount_requested' => $amount_requested,
            'amount_maximum_suggested' => $amount_maximum_suggested,
            'is_valid' => ($indebtedness_calculated) <= ($procedure_modality->loan_modality_parameter->decimal_index)*100
        ]);
    }
     // funcion para sacar la cuota estimada con la calculadora
    private function quota_calculator_borrar($procedure_modality, $months_term, $amount_requested){
        $interest_rate = $procedure_modality->current_interest->monthly_current_interest;
        return ((($interest_rate)/(1-(1/pow((1+$interest_rate),$months_term))))*$amount_requested);
    }
    // funcion para sacar el liquido para calificacion
    private function liquid_qualification_borrar($payable_liquid_average, $total_bonuses, $affiliate, $parent_quota, $guarantor){
        $active_guarantees = $affiliate->active_guarantees(); $sum_quota_guarantor = 0;
        foreach($active_guarantees as $res){
            $sum_quota_guarantor+= ($res->estimated_quota*$res->pivot->payment_percentage)/100; // en caso de tener garantias
        }
        if($guarantor){
            $active_loans = $affiliate->active_loans(); $sum_quota_lender = 0;
            foreach($active_loans as $res){
                $sum_quota_lender+= ($res->estimated_quota*$res->pivot->payment_percentage)/100; // en caso de ser garante y tener prestamos propios
            }
            $liquid_qualification_calculated = $payable_liquid_average - $total_bonuses - $sum_quota_guarantor - $sum_quota_lender;
        }else{
            // arreglar cuando hay padre y los demas que son tramites ajenos
            $liquid_qualification_calculated  = $payable_liquid_average - $total_bonuses - $sum_quota_guarantor + $parent_quota;
        }
        return $liquid_qualification_calculated;
    }
    // monto maximo
    private function maximum_amount_borrar($procedure_modality,$months_term,$liquid_qualification_calculated){
        $interest_rate = $procedure_modality->current_interest->monthly_current_interest;
        $loan_interval = $procedure_modality->procedure_type->interval;
        $debt_index = $procedure_modality->loan_modality_parameter->decimal_index;
        $maximum_qualified_amount = intval((1-(1/pow((1+$interest_rate),$months_term)))*($debt_index*$liquid_qualification_calculated)/$interest_rate);
        if ($maximum_qualified_amount > ($loan_interval->maximum_amount)){
            $maximum_qualified_amount = $loan_interval->maximum_amount;
        } else {
            $maximum_qualified_amount = $maximum_qualified_amount;
        }
        return $maximum_qualified_amount;
        //return intval(round(floor($maximum_qualified_amount))/100)*100;
    }

    //division porcentual de las cuotas de los codeudores
    private function loan_percent(request $request){
        $procedure_modality = ProcedureModality::findOrFail($request->procedure_modality_id);
        $debt_index = $procedure_modality->loan_modality_parameter->debt_index;
        $lc = $request->liquid_calculated;
        $ms = $request->amount_requested;
        $plm = $request->months_term;
        $ticm = $procedure_modality->current_interest->monthly_current_interest;
        $ce = $this->quota_calculator($procedure_modality, $plm, $ms);
        $liquid_qualification_calculated=0;
        foreach($lc as $obj){
        $liquid_qualification_calculated = $liquid_qualification_calculated + $obj["liquid_qualification_calculated"];
        }
        //***m */
        $amount_maximum_suggested = $this->maximum_amount($procedure_modality,$plm,$liquid_qualification_calculated);
        if($ms>$amount_maximum_suggested){
            $ce = $this->quota_calculator($procedure_modality, $plm, $amount_maximum_suggested);
            $ms = $amount_maximum_suggested;
        }
        $maximum_suggested_valid = false;
        if($procedure_modality->procedure_type->interval->minimum_amount<=$amount_maximum_suggested && $amount_maximum_suggested<=$procedure_modality->procedure_type->interval->maximum_amount) $maximum_suggested_valid = true;
        //***end m */
        $ie = ($ce/$liquid_qualification_calculated)*100;
        if($ie<=$debt_index){
            $evaluate = true;
        }else{
            $evaluate = false;
        }
        //$ams = $this->maximum_amount($procedure_modality,$plm,$liquid_qualification_calculated);
        $cosigners=array();
        foreach($lc as $obj2){
            $plc = (int)$obj2["liquid_qualification_calculated"];
            $plc = round(($plc/$liquid_qualification_calculated)*100);
            $ce_c = round(($ce*$plc)/100,2);
            $cosigner=array(
                "affiliate_id"=>$obj2["affiliate_id"],
                "quota_calculated_estimated"=>$ce_c,
                'payment_percentage'=>$plc,
                'liquid_qualification_calculated' => $obj2["liquid_qualification_calculated"],
        );
        array_push($cosigners,$cosigner);
        }
        $response = $this->header($ce,$ie,$ms,$plm,$evaluate,$liquid_qualification_calculated,$amount_maximum_suggested,$maximum_suggested_valid,$cosigners);
        return $response;
    }

    //colocado de la cabecera al array
    private function header($ce,$ie,$ms,$plm,$evaluate,$liquid_qualification_calculated,$amount_maximum_suggested,$maximum_suggested_valid,$cosigners){
        $response=array(
            "quota_calculated_estimated_total"=>Util::money_format($ce),
            "indebtedness_calculated_total"=>round($ie,2),
            "amount_requested"=>$ms,
            "months_term"=>$plm,
            "is_valid"=>$evaluate,
            'liquid_qualification_calculated_total' => $liquid_qualification_calculated,
            'amount_maximum_suggested' => $amount_maximum_suggested,
            'maximum_suggested_valid' => $maximum_suggested_valid,
            "affiliates"=>$cosigners
        );
        return $response;
    }

    /**
    * Evaluacion individual de garantes
    * @bodyParam procedure_modality_id integer required ID de modalidad. Example: 34
    * @bodyParam affiliate_id integer required ID del afiliado. Example: 1
    * @bodyParam quota_calculated_total_lender cuota calculada del titular. Example: 900
    * @bodyParam contributions[0].payable_liquid integer required Líquido pagable. Example: 2000
    * @bodyParam contributions[0].seniority_bonus integer required Bono Cargo . Example: 0.00
    * @bodyParam contributions[0].border_bonus integer required Bono Frontera . Example: 0.00
    * @bodyParam contributions[0].public_security_bonus integer required Bono Seguridad Ciudadana . Example: 0.00
    * @bodyParam contributions[0].east_bonus integer required Bono Oriente. Example: 0.00
    * @authenticated
    * @responseFile responses/calculator/evaluate_guarantor.200.json
    */
    public function evaluate_guarantor(Request $request){
        $procedure_modality = ProcedureModality::findOrFail($request->procedure_modality_id);
        $quantity_guarantors = $procedure_modality->loan_modality_parameter->guarantors;
        if($quantity_guarantors > 0){
            $debt_index = $procedure_modality->loan_modality_parameter->debt_index;
            $amount_requested =$request->mount_requested;
            $months_term = $request->montjs_term;
            $affiliate_id = $request->affiliate_id;
            $affiliate = Affiliate::findOrFail($request->affiliate_id);
            $contributions = collect($request->contributions);
            $payable_liquid_average = $contributions->avg('payable_liquid');
            if ($request->has('parent_loan_id')) {
                $parent_loan = Loan::with(['lenders'=> function($q) use ($affiliate) {
                    $q->where('affiliate_id', $affiliate->id);
                }])->whereId($request->parent_loan_id)->first();
                if (!$parent_loan) abort(404);
                $parent_quota = $parent_loan->next_payment()->estimated_quota * $parent_loan->lenders[0]->pivot->payment_percentage/100;
            } else {
                $parent_quota = 0;
            }
            //$parent_quota = 0;
            $quota_calculated = $request->quota_calculated_total_lender/$quantity_guarantors;
            $contribution_first = $contributions->first();
            $total_bonuses = $contribution_first['seniority_bonus']+$contribution_first['border_bonus']+$contribution_first['public_security_bonus']+$contribution_first['east_bonus'];
            $liquid_qualification_calculated = $this->liquid_qualification($payable_liquid_average, $total_bonuses, $affiliate, $parent_quota);
            $indebtedness_calculated = $quota_calculated/$liquid_qualification_calculated*100;
            if ($indebtedness_calculated <= $debt_index)
                $evaluate = true;
            else
                $evaluate = false;
            $response = array(
                "is_valid" => $evaluate,
                "indebtnes_calculated" => round($indebtedness_calculated,2),
                "payable_liquid" => round($payable_liquid_average,2),
                "bonus_calculated" => round($total_bonuses,2),
                "payable_liquid_calculated" => round($liquid_qualification_calculated,2),
            );
            return $response;
        }
        else{
            return abort(403, 'no corresponde a esta modalidad');
        }
    }
}