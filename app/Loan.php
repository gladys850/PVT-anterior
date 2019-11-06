<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
        return $this->penal_interest > 0 ? true : false;
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

    public function interest()
    {
        return $this->belongsTo(LoanInterest::class);
    }

    // Saldo capital
    public function balance()
    {
        return $this->amount_approved - $this->payments()->sum('capital_amortization');
    }

    // Unión de pagos con el mismo número de cuota
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
        return $this->payments()->latest()->first();
    }

    public function last_merged_payment()
    {
        $latest_quota = $this->last_payment;
        if ($latest_quota) {
            $payments = LoanPayment::whereLoanId($this->id)->whereQuotaNumber($latest_quota->quota_number)->get();
            return $this->merge_payments($payments);
        }
        return $latest_quota;
    }

    public function quota()
    {
        $interest = $this->interest;
        $monthly_interest = $interest->annual_interest_decimal / 12;
        return $monthly_interest * $this->amount_approved / (1 - 1 / (1 + pow($monthly_interest, $this->loan_term)));
    }

    public function next_payment()
    {
        $interest = $this->interest;
        $quota = new LoanPayment();
        $year_days = 365;
        // Establecer número de cuota
        if (!$this->last_payment) {
            $quota->quota_number = 1;
        } else {
            $quota->quota_number = $this->last_payment->quota_number + 1;
        }
        // Calcular intereses
        $balance_interest = $quota->balance * $interest->annual_interest_decimal * $this->current_interest / $year_days;
        $quota->interest_amortization = $balance_interest * $this->current_interest;
        $quota->penal_amortization = $balance_interest * $this->accumulated_interest;
        $quota->other_charges = $interest->penal_interest_decimal * $this->penal_interest;
        // Calcular amortización de capital
        $total_interests = $quota->interest_amortization + $quota->penal_amortization + $quota->other_charges;
        if (($quota->balance + $total_interests) > $this->quota) {
            $quota->capital_amortization = $quota->balance - $total_interests;
        } else {
            $quota->capital_amortization = $quota->balance;
        }
        // Calcular monto total de la cuota
        $quota->estimated_fee = $quota->capital_amortization + $total_interests;
        return $quota;
    }
}