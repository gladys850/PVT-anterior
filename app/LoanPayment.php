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
    public function current_interest($loan_id, $quota)
    {
        $loanPayment=LoanPayment::where('loan_id', '=', $loan_id)->get()->toArray();
        if($quota==1)
        {
            $loan=Loan::find($loan_id);
            $date_disbursement=$loan->disbursement_date;
            $date_first_quota=$loanPayment[0]['estimated_date'];
            $diferencia = Carbon::parse($date_disbursement)->diffInDays(Carbon::parse($date_first_quota));
        }
        else{
            $i=$quota-1;
            $j=$i-1;
            $date_quota=$loanPayment[$i]['estimated_date'];
            $date_next_quota=$loanPayment[$j]['estimated_date'];
            $diferencia = Carbon::parse($date_quota)->diffInDays(Carbon::parse($date_next_quota));
        }
        return $diferencia;

    // Unión de pagos con el mismo número de cuota
    }
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
    public function quota_date($loan_id)
    {   $c=1;
        $quota_date=[];
        $loan=Loan::find($loan_id);
        $date_disbursement=$loan->disbursement_date;
        $num_quota=$loan->loan_term;
        $loanParameter=(LoanParameter::all())->last();
        $offsetday=$loanParameter->offset_day;
        $last_day=Carbon::parse($date_disbursement)->endOfMonth();
        $number_day=Carbon::parse($date_disbursement)->diffInDays(Carbon::parse($last_day));
        $next_quota=$last_day;
        if($number_day>=$offsetday){
            $quota_date[$c]=$next_quota->format('d-m-Y');
        }else{$c=0;
        }
        while($c<$num_quota){
            $c=$c+1;     
            $next_quota=Carbon::parse($next_quota)->addWeek()->endOfMonth();
            $quota_date[$c]=$next_quota->format('d-m-Y');
        } 
        return $quota_date;
    }
}
