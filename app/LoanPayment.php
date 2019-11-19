<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon;

class LoanPayment extends Model
{
    public $timestamps = true;

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
    public function days_interest($loan_id, $estimated_date)
    {
        $loanPayment=LoanPayment::where('loan_id', '=', $loan_id)->orderBy('quota_number', 'asc')->get()->toArray();
        $quota=count($loanPayment);
        if($quota==0)
        {
            $loan=Loan::find($loan_id);
            $date_disbursement=$loan->disbursement_date;
            $diferencia = Carbon::parse($date_disbursement)->diffInDays(Carbon::parse($estimated_date));
            $diferencia=$diferencia+1;
            if($diferencia>31)
            {   $interest_corriente=Carbon::parse($estimated_date)->endOfDay()->day;
                $interest_penal=0;
                $interest_acumulado=$diferencia-Carbon::parse($estimated_date)->endOfDay()->day;

            }
            else
            {   $interest_corriente=$diferencia;
                $interest_penal=0;
                $interest_acumulado=0;
            }
        }
        else
        {
            $i=$quota-1;
            $date_quota=$loanPayment[$i]['pay_date'];
            $diferencia = Carbon::parse($date_quota)->diffInDays(Carbon::parse($estimated_date));
            if($diferencia>91)
            {   $interest_corriente=Carbon::parse($estimated_date)->endOfDay()->day;
                $interest_penal=$diferencia-31;
                $interest_acumulado=$diferencia;
            }
            else
            {
                if($diferencia>31)
                {   $interest_corriente=Carbon::parse($estimated_date)->endOfDay()->day;
                    $interest_penal=0;
                    $interest_acumulado=$diferencia-Carbon::parse($estimated_date)->endOfDay()->day;
                }
                else
                {   $interest_corriente=Carbon::parse($estimated_date)->endOfDay()->day;
                    $interest_penal=0;
                    $interest_acumulado=0;
                }
            }
        }
        return  ['dias_corriente'=>$interest_corriente,
                'dias_penal'=>$interest_penal,
                'dias_cumulado'=>$interest_acumulado];
    }
    // Unión de pagos con el mismo número de cuota
    public function merge($payments)
    {
        $merged = new LoanPayment();
        foreach ($payments as $key => $payment) {
            if ($key == 0) {
                $merged = $payment;
                unset($merged->payment_type);
                unset($merged->voucher_number);
                unset($merged->receipt_number);
                unset($merged->description);
                unset($merged->created_at);
                unset($merged->updated_at);
            } else {
                $merged->estimated_fee += $payment->estimated_fee;
                $merged->capital_amortization += $payment->capital_amortization;
                $merged->interest_amortization += $payment->interest_amortization;
                $merged->penal_amortization += $payment->penal_amortization;
                $merged->other_charges += $payment->other_charges;
            }
            if (!next($payments)) {
                $merged->pay_date = $payment->pay_date;
                $merged->estimated_date = $payment->estimated_date;
            }
        }
        return $merged;
    }
}