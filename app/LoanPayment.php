<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon;

class LoanPayment extends Model
{
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = true;
    public $fillable = [
        'loan_id',
        'affiliate_id',
        'pay_date',
        'estimated_date',
        'quota_number',
        'quota_estimated',
        'capital_payment',
        'interest_payment',
        'penal_payment',
        'accumulation_interest',
        'voucher_number',
        'payment_type',
        'receipt_number',
        'description'
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
    public static function days_interest($loan_id, $estimated_date)
    {
        $loanPayment=LoanPayment::where('loan_id', '=', $loan_id)->orderBy('quota_number', 'asc')->get()->toArray();
        $quota=count($loanPayment);
        if($quota==0)
        {
            $loan=Loan::findOrFail($loan_id);
            $date_disbursement=$loan->disbursement_date;
            $diferencia = Carbon::parse($date_disbursement)->diffInDays(Carbon::parse($estimated_date));
            $diferencia=$diferencia+1;
            if($diferencia>91)
            {
                $interest_corriente=Carbon::parse($estimated_date)->endOfDay()->day;
                $interest_acumulado=$diferencia;
                $interest_penal=$diferencia-31;
            }
            else
            {
                if($diferencia > 31)
                {   $interest_corriente=Carbon::parse($estimated_date)->endOfDay()->day;
                    $interest_acumulado=$diferencia-Carbon::parse($estimated_date)->endOfDay()->day;
                    $interest_penal=0;
                }
                else
                {   $interest_corriente=Carbon::parse($estimated_date)->endOfDay()->day;
                    $interest_penal=0;
                    $interest_acumulado=0;
                }
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
                'dias_acumulado'=>$interest_acumulado];
    }
    // Unión de pagos con el mismo número de cuota
    public function merge($payments)
    {
        $merged = new LoanPayment();
        foreach ($payments as $key => $payment) {
            if ($key == 0) {
                $merged = $payment;
            } else {
                $merged->capital_payment += $payment->capital_payment;
                $merged->interest_payment += $payment->interest_payment;
                $merged->penal_payment += $payment->penal_payment;
                $merged->accumulation_interest += $payment->accumulation_interest;
            }
            if (!next($payments)) {
                $merged->pay_date = $payment->pay_date;
                $merged->estimated_date = $payment->estimated_date;
            }
        }
        unset($merged->affiliate_id, $merged->payment_type, $merged->voucher_number, $merged->receipt_number, $merged->description, $merged->created_at, $merged->updated_at);
        return $merged;
    }
    public static function quota_date($loan_id)
    {   $c=1;
        $quota_date=[];
        $loan=Loan::find($loan_id);
        $date_disbursement=$loan->disbursement_date;
        $num_quota=count($loan->plan());
        $loanGlobalParameter=(LoanGlobalParameter::all())->last();
        $offsetday=$loanGlobalParameter->offset_day;
        $last_day=Carbon::parse($date_disbursement)->endOfMonth();
        $number_day=Carbon::parse($date_disbursement)->diffInDays(Carbon::parse($last_day));
        $next_quota=$last_day;
        if($number_day>=$offsetday){
            $quota_date[$c]=$next_quota->toDateString();
        }else{$c=0;
        }
        while($c<$num_quota){
            $c=$c+1;
            $next_quota=Carbon::parse($next_quota)->addWeek()->endOfMonth()->toDateString();
            $quota_date[$c]=$next_quota;
        } 
        return $quota_date;
    }

    public static function current_interest($loan_id, $estimated_date, $estimated_date_last, $quota)
    {
        $loanPayment=LoanPayment::where('loan_id', '=', $loan_id)->get()->toArray();
        if($quota==1)
        {
            $loan=Loan::find($loan_id);
            $date_disbursement=$loan->disbursement_date;
            $diferencia = Carbon::parse($date_disbursement)->diffInDays(Carbon::parse($estimated_date_last));
            $diferencia=$diferencia+1;
        }
        else{
            $i=$quota-1;
            $diferencia = Carbon::parse($estimated_date_last)->diffInDays(Carbon::parse($estimated_date));
        }
        return $diferencia;
    }


}
