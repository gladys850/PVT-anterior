<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\CarbonImmutable;

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
        'accumulated_interest',
        'voucher_number',
        'payment_type',
        'receipt_number',
        'description'
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public static function days_interest(Loan $loan, $estimated_date = null)
    {
        $interest = [
            'current' => 0,
            'penal' => 0,
            'accumulated' => 0
        ];
        if ($loan->balance == 0) return $interest;
        $estimated_date = CarbonImmutable::parse($estimated_date ?? CarbonImmutable::now()->toDateString());
        $latest_quota = $loan->payments()->first();
        if (!$latest_quota) {
            $payment_date = $loan->disbursement_date;
            if (!$payment_date) return $interest;
        } else {
            $payment_date = $latest_quota->pay_date;
        }
        $payment_date = CarbonImmutable::parse($payment_date);
        if ($estimated_date->lessThan($payment_date)) return $interest;
        $diff_days = $estimated_date->diffInDays($payment_date) + 1;
        if ($estimated_date->diffInMonths($payment_date) == 0) {
            $interest['current'] = $diff_days;
        } else {
            $interest['current'] = $estimated_date->day;
            if ($payment_date->day >= LoanGlobalParameter::latest()->first()->offset_interest_day && $estimated_date->diffInMonths($payment_date) == 1) {
                $interest['current'] += $payment_date->endOfMonth()->diffInDays($payment_date) + 1;
            }
        }
        $interest['accumulated'] = $diff_days - $interest['current'];
        if ($interest['accumulated'] >= 90) {
            $interest['penal'] = $interest['accumulated'] - $interest['current'];
        }
        return $interest;
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
                $merged->accumulated += $payment->accumulated;
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
        $offsetday=$loanGlobalParameter->offset_interest_day;
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
