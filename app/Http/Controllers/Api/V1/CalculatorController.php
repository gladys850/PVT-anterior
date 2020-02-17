<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Affiliate;
use Util;
use App\ProcedureModality;
use App\Loan;

/** @group Préstamos
*/
class CalculatorController extends Controller
{
    /**
    * Calculadora
    */
    public function store(Request $request)
    {
        $procedure_modality = ProcedureModality::findOrFail($request->procedure_modality_id);
        $limit = $procedure_modality->procedure_type->loan_interval;
        $amount_request = $request->amount_request;$months_term = $request->months_term;
        if($limit->minimum_amount<=$amount_request && $amount_request<=$limit->maximum_amount && $limit->minimum_term<=$months_term && $months_term<=$limit->maximum_term){
            $affiliate = Affiliate::findOrFail($request->affiliate_id);
            if ($request->has('parent_loan_id')) {
                $parent_loan = Loan::with(['lenders'=> function($q) use ($affiliate) {
                    $q->where('affiliate_id', $affiliate->id);
                }])->whereId($request->parent_loan_id)->first();
                if (!$parent_loan) abort(404);
                $parent_quota = $parent_loan->next_payment->quota_estimated * $parent_loan->lenders[0]->pivot->payment_percentage/100;
                return $parent_quota;
            } else {
                $parent_quota = 0;
            }
            $contributions = collect($request->contributions);
            $payable_liquid_average = $contributions->avg('payable_liquid');
            $contribution_first = $contributions->first();
            $total_bonuses = $contribution_first['seniority_bonus']+$contribution_first['border_bonus']+$contribution_first['public_security_bonus']+$contribution_first['east_bonus'];
            $liquid_qualification=$this->liquid_qualification($payable_liquid_average,$total_bonuses,$affiliate,$parent_quota);
            $quota_calculation = $this->quota_calculator($procedure_modality, $request->months_term, $amount_request,$liquid_qualification);
            $maximum_suggested_amount = $this->maximum_amount($procedure_modality,$request->months_term,$liquid_qualification);
            if($payable_liquid_average!=0){
                $index_calculated =$quota_calculation/($liquid_qualification)*100 ;
            }else{
                $index_calculated = 0;
            }
            return response()->json([
                'promedio_liquido_pagable'=>$payable_liquid_average,
                'total_bonos'=>$total_bonuses,
                'liquido_para_calificacion'=>round($liquid_qualification),
                'calculo_de_cuota'=>Util::money_format($quota_calculation),
                'indice_endeudamiento'=>intval($index_calculated),
                'monto_maximo_sugerido'=>round($maximum_suggested_amount),
                'valido' => intval($index_calculated) <= ($procedure_modality->loan_modality_parameter->decimal_index)*100
            ]);
        }else{ abort (404);} 
    }
    // funcion para sacar el liquido para calificacion
    public function liquid_qualification($payable_liquid_average,$total_bonuses,$affiliate,$parent_quota=0){
        $active_guarantees = $affiliate->active_guarantees();$sum_quota_guarantor=0;
        foreach($active_guarantees as $res){ 
            $sum_quota_guarantor+= ($res->estimated_quota*$res->pivot->payment_percentage)/100;
        }
        return ($payable_liquid_average-$total_bonuses-$sum_quota_guarantor+$parent_quota);
    }
    //funcion para sacar la cuota estimada con la calculadora
    public function quota_calculator($procedure_modality,$months_term,$amount_request,$liquid_qualification){
        $loan_interval = $procedure_modality->procedure_type->loan_interval;
        $interest_rate = $procedure_modality->current_interest->monthly_current_interest; 
        if($amount_request>0 && $months_term ==null){
            return ((($interest_rate)/(1-(1/pow((1+$interest_rate),$loan_interval->maximum_term))))*$amount_request);   
        }if($amount_request ==null && $months_term>0){
            $maximum_qualified_amount = $this->maximum_amount($modality_id,$months_term,$liquid_qualification);
            return ((($interest_rate)/(1-(1/pow((1+$interest_rate),$months_term))))*$maximum_qualified_amount);   
        }if ($months_term>0 && $amount_request>0){
            return ((($interest_rate)/(1-(1/pow((1+$interest_rate),$months_term))))*$amount_request);   
        }
    }
    // monto maximo
    public function maximum_amount($procedure_modality,$months_term,$liquid_qualification){ 
        $interest_rate = $procedure_modality->current_interest->monthly_current_interest;
        $loan_interval = $procedure_modality->procedure_type->loan_interval;
        $debt_index = $procedure_modality->loan_modality_parameter->decimal_index;
        if($months_term ==null){
            $months_term = $loan_interval->maximum_term;
        }
        $maximum_qualified_amount = intval((1-(1/pow((1+$interest_rate),$months_term)))*($debt_index*$liquid_qualification)/$interest_rate);
        if ($maximum_qualified_amount > ($loan_interval->maximum_amount)){
            $maximum_qualified_amount = $loan_interval->maximum_amount;
        } else {
            $maximum_qualified_amount = $maximum_qualified_amount; 
        }
        return $maximum_qualified_amount;
    }
}