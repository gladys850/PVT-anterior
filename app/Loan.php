<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon;

class Loan extends Model
{
    public $timestamps = true;
    public $guarded = ['id'];

    public function state()
    {
        return $this->belongsTo(LoanState::class);
    }

    public function guarantors()
    {
        return $this->belongsToMany(Affiliate::class, 'loan_guarantors');
    }

    public function defaulted()
    {
        return $this->penal_interest() > 0 ? true : false;
    }

    // Cálculo de días de interés penal
    public function penal_interest_days()
    {
        return rand(0, 3);
    }

    public function payments()
    {
        return $this->hasMany(LoanPayment::class)->orderBy('quota_number')->orderBy('created_at');
    }

    public function merge_payments($payments)
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
    }

    public function last_payment()
    {
        $latest_quota = $this->payments()->latest()->first();
        if ($latest_quota) {
            $payments = LoanPayment::whereLoanId($this->id)->whereQuotaNumber($latest_quota->quota_number)->get();
            return $this->merge_payments($payments);
        }
        return $latest_quota;
    }

    public function first_quota()
    {
        $quota = new LoanPayment();
        $year_days = 365;
        if (Carbon::parse($this->disbursement_date)->day < LoanParameter::latest()->first()->offset_day) {
            $quota->estimated_fee = $this->amount_aproved;
            // TODO NEEDED CURRENT INTEREST
        }
    }

    public function next_quota()
    {
        if (!$this->last_payment) {
            return $this->first_quota();
        } else {
            return 'TODO NEXT CUOTA CALCULUS';
        }
    }
}