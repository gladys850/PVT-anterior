<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    public $timestamps = true;
    public $guarded = ['id'];
    public $fillable = [
        'disbursable_id',
        'disbursable_type',
		'procedure_modality_id',
		'amount_disbursement',
		'parent_loand_id',
        'parent_reason',
        'request_date',
        'amount_request',
        'city_id',
        'insterest_loan_id',
        'loan_state_id',
        'amount_aproved',
        'loan_term',
        'disbursement_date',
        'disbursement_type_id',
        'modification_date',
        
	];
    public function state()
    {
      return $this->belongsTo(LoanState::class, 'loan_state_id','id');
    }
    public function city()
    {
      return $this->belongsTo(City::class);
    }
    public function payment_type()
    {
      return $this->belongsTo(PaymentType::class,'disbursement_type_id','id');
    }
    public function loan_interest()
    {
      return $this->belongsTo(LoanInterest::class,'interest_loan_id','id');
    }
    public function guarantors()
    {
        return $this->belongsToMany(Affiliate::class, 'loan_guarantors');
    }
    public function loan_affiliates()
    {
        return $this->belongsToMany(Affiliate::class, 'loan_affiliates');
    }
 
    /*public function submitted_documents()
    {
      return $this->hasMany(LoanSubmitedDocument::class);
    }*/
    public function modality()
    {
      return $this->belongsTo(ProcedureModality::class,'procedure_modality_id', 'id');
    }
    //$loan=Loan::first() ; $loan->modality->procedure_documents// listar requisitos de acuerdo a una modalidad
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

    public function last_payment()
    {
        return $this->payments()->latest()->first();
    }

    public function last_quota()
    {
        $latest_quota = $this->last_payment;
        if ($latest_quota) {
            $payments = LoanPayment::whereLoanId($this->id)->whereQuotaNumber($latest_quota->quota_number)->get();
            if (count($payments) == 1) return $payments->first();
            $latest_quota = new LoanPayment();
            $latest_quota = $latest_quota->merge($payments);
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