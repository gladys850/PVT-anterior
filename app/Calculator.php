<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Util;


class Calculator extends Model
{
    // metodos para la calculadora
    // suma de numeros, la suma del liquido pagable, bonos
    public function add_numbers($amounts)
    {
        if(count($amounts)>0){
            $add_result=0;
            foreach($amounts as $data){ 
                $add_result+=$data; 
            }
            return $add_result;
        }
        return 0;
    }
    // funcion para sacar el liquido para calificacion
    public function liquid_qualification($ticket,$bonuses,$affiliate_id){
        //$ticket=[2000,2000,2000];$bonuses=[24]; $affiliate_id=2;
        $affiliate = Affiliate::findOrFail($affiliate_id);
        $active_guarantees = $affiliate->active_guarantees();$sum_quota_guarantor=0;
        foreach($active_guarantees as $res){ 
            $sum_quota_guarantor+= ($res->estimated_quota*$res->pivot->payment_percentage)/100;
        }
        $active_loans = $affiliate->active_loans();$sum_actives_loans=0;
        foreach($active_loans as $res){ 
            $sum_actives_loans+= ($res->estimated_quota*$res->pivot->payment_percentage)/100;
        }
        $average_ticket = $this->average_ticket($ticket);
        $total_bonuses = $this->add_numbers($bonuses);
        return ($average_ticket-$total_bonuses+$sum_actives_loans-$sum_quota_guarantor);
    }
    public function average_ticket($ticket){
        if(count($ticket)>0){
           return ($this->add_numbers($ticket))/count($ticket);
        }
        return 0;
    }
    //funcion para sacar la cuota estimada con la calculadora
    public function quota_calculator($ticket,$bonuses,$modality_id,$months_term,$amount_request,$affiliate_id){
        if($modality_id !=null){
            $loan_interval= (ProcedureModality::find($modality_id))->procedure_type->loan_interval;
            $interest_rate=(LoanInterest::where('procedure_modality_id', '=',$modality_id)->latest()->first())->monthly_current_interest; 
            if($amount_request>0 && $months_term ==null){
                return ((($interest_rate)/(1-(1/pow((1+$interest_rate),$loan_interval->maximum_term))))*$amount_request);   
            }if($amount_request ==null && $months_term>0){
                    $maximum_qualified_amount = $this->maximum_amount($modality_id,$ticket,$bonuses,$months_term);
                    return ((($interest_rate)/(1-(1/pow((1+$interest_rate),$months_term))))*$maximum_qualified_amount);   
                }if ($months_term>0 && $amount_request>0){
                        return ((($interest_rate)/(1-(1/pow((1+$interest_rate),$months_term))))*$amount_request);   
                    }else{
                        if(count($ticket)>0){
                            $maximum_qualified_amount = $this->maximum_amount($modality_id,$ticket,$bonuses,$months_term);
                            $estimated_quota=((($interest_rate)/(1-(1/pow((1+$interest_rate),$loan_interval->maximum_term))))*$maximum_qualified_amount);
                            return $estimated_quota;
                        }else{
                            return 0;
                        }
                    }
        }else{
             return 0;
        } 
    }
    // monto maximo
    public function maximum_amount($modality_id,$ticket,$bonuses,$months_term,$affiliate_id){ 
        if($modality_id!=null){
            $interest_rate=(LoanInterest::where('procedure_modality_id', '=',$modality_id)->latest()->first())->monthly_current_interest;
            $loan_interval= (ProcedureModality::find($modality_id))->procedure_type->loan_interval;
            $debt_index=(LoanModalityParameter::where('procedure_modality_id', '=',$modality_id)->latest()->first())->decimal_index;
            $liquid_qualification = $this->liquid_qualification($ticket,$bonuses,$affiliate_id);
            if($months_term ==null){
                $months_term = $loan_interval->maximum_term;
            }
            $maximum_qualified_amount = (1-(1/pow((1+$interest_rate),$months_term)))*($debt_index*$liquid_qualification)/$interest_rate;
            //$maximum_qualified_amount = floor(round(floor($maximum_qualified_amount))/1000)*1000;
            if ($maximum_qualified_amount > ($loan_interval->maximum_amount)){
                $maximum_qualified_amount = $loan_interval->maximum_amount;
            } else {
                $maximum_qualified_amount = $maximum_qualified_amount; 
            }
            return $maximum_qualified_amount;
            
        }else{
            return 0;
        }   
    }
    public function calculator($ticket,$bonuses,$modality_id,$months_term,$amount_request,$affiliate_id)
    {
        $payable_liquid_average= $this->average_ticket($ticket);
        $total_bonuses=$this->add_numbers($bonuses);
        $liquid_qualification=$this->liquid_qualification($ticket,$bonuses,$affiliate_id);
        $quota_calculation = $this->quota_calculator($ticket,$bonuses,$modality_id, $months_term, $amount_request, $affiliate_id);
        $maximum_suggested_amount = $this->maximum_amount($modality_id,$ticket,$bonuses,$months_term,$affiliate_id);
        if($payable_liquid_average!=0){
            $index_calculated =$quota_calculation/(($this->average_ticket($ticket))-($this->add_numbers($bonuses)))*100 ;
            //$debt_index=(LoanModalityParameter::where('procedure_modality_id', '=',$modality_id)->latest()->first())->debt_index;
        }else{
            $index_calculated = 0;
        }
        return response()->json([
            'promedio_liquido_pagable'=>$payable_liquid_average,
            'total_bonos'=>$total_bonuses,
            'liquido_para_calificacion'=>round($liquid_qualification),
            'calculo_de_cuota'=>round($quota_calculation),
            'indice_endeudamiento'=>round($index_calculated),
            'monto_maximo_sugerido'=>round($maximum_suggested_amount)
        ]);
    }
}
