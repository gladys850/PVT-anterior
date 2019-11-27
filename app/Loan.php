<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon;
use Util;

class Loan extends Model
{
    protected $appends = ['balance', 'estimated_quota', 'defaulted'];
    public $timestamps = true;
    protected $hidden = ['pivot'];
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
        'loan_interest_id',
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

    public function guarantors()
    {
        return $this->belongsToMany(Affiliate::class, 'loan_affiliates')->whereGuarantor(true);
    }

    public function lenders()
    {
        return $this->belongsToMany(Affiliate::class, 'loan_affiliates')->whereGuarantor(false);
    }

    public function modality()
    {
      return $this->belongsTo(ProcedureModality::class,'procedure_modality_id', 'id');
    }
    //$loan=Loan::first() ; $loan->modality->procedure_documents// listar requisitos de acuerdo a una modalidad
    public function submitted_documents()
    {
      return $this->hasMany(LoanSubmittedDocument::class);
    }

    public function getDefaultedAttribute()
    {
        return LoanPayment::days_interest($this->id, Carbon::now()->toDateString())['dias_penal'] > 0 ? true : false;
    }

    public function payments()
    {
        return $this->hasMany(LoanPayment::class)->orderBy('quota_number')->orderBy('created_at');
    }
    public function interest()
    {
        return $this->belongsTo(LoanInterest::class, 'loan_interest_id', 'id');
    }

    public function observations()
    {
        return $this->morphMany(Observable::class, 'observable');
    }
    //desembolso --> afiliado, esposa, beneficiario
    public function disbursable()
    {
        return $this->morphTo();
    }

    // Saldo capital
    public function getBalanceAttribute()
    {
        $balance = $this->amount_disbursement;
        if ($this->payments()->count() > 0) {
            $balance -= $this->payments()->sum('capital_payment');
        }
        return Util::round($balance);
    }

    public function last_payment()
    {
        return $this->payments()->latest()->first();
    }

    public function last_quota()
    {
        $latest_quota = $this->last_payment();
        if ($latest_quota) {
            $payments = LoanPayment::whereLoanId($this->id)->whereQuotaNumber($latest_quota->quota_number)->get();
            $latest_quota = new LoanPayment();
            $latest_quota = $latest_quota->merge($payments);
        }
        return $latest_quota;
    }

    public function getEstimatedQuotaAttribute()
    {
        $monthly_interest = $this->interest->monthly_current_interest;
        unset($this->interest);
        return Util::round($monthly_interest * $this->amount_disbursement / (1 - 1 / pow((1 + $monthly_interest), $this->loan_term)));
    }
 

    public function next_payment()
    {
        $quota = $this->last_quota();
        if (!$quota) {
            $quota = new LoanPayment();
            $quota->estimated_date = LoanPayment::quota_date($this->id)[1];
            $current_date = Carbon::now();
            $last_date = Carbon::parse($quota->estimated_date);
            if ($last_date->month <= $current_date->month) {
                $quota->estimated_date = $current_date;
            } else {
                $quota->estimated_date = $current_date->addMonth();
            }
            $quota->quota_number = 1;
        } else {
            $quota->estimated_date = Carbon::now();
            $quota->quota_number = $quota->quota_number + 1;
        }
        $quota->estimated_date = $quota->estimated_date->endOfMonth()->toDateString();
        unset($quota->pay_date);
        $interest = $this->interest;
        $interest_days = LoanPayment::days_interest($this->id, $quota->estimated_date);

        // Calcular intereses
        $quota->balance = $this->balance;
        $quota->interest_payment = Util::round($quota->balance * $interest->daily_current_interest * $interest_days['dias_corriente']);
        $quota->penal_payment = Util::round($quota->balance * $interest->daily_penal_interest * $interest_days['dias_penal']);
        $quota->accumulation_interest = Util::round($quota->balance * $interest->daily_current_interest * $interest_days['dias_acumulado']);
        // Calcular amortización de capital
        $total_interests = $quota->interest_payment + $quota->penal_payment + $quota->accumulation_interest;
        if (($quota->balance + $total_interests) > $this->estimated_quota) {
            $quota->capital_payment = $this->estimated_quota - $total_interests;
        } else {
            $quota->capital_payment = $quota->balance;
        }
        // Calcular monto total de la cuota
        $quota->quota_estimated = $quota->capital_payment + $total_interests;
        $quota->next_balance = $quota->balance - $quota->capital_payment;
        return $quota;
    }
    public function  plan(){
        $saldo_capital=$this->amount_disbursement;
        $interes_diario=$this->interest->daily_current_interest;
        $cuota_estimada=$this->estimated_quota;
        $plan=[];
        $fechas = [];
        $fechas[] = $this->disbursement_date;
        $n=1;
        while($saldo_capital>0) {
            $fechas[] = Carbon::parse($fechas[$n-1])->startOfMonth()->addMonth()->endOfMonth()->toDateString();
            $dias_interes=LoanPayment::current_interest($this->id,$fechas[$n-1],$fechas[$n],$n);
            $amortizacion_interes=$dias_interes*$saldo_capital*$interes_diario;
            if($cuota_estimada>=$saldo_capital){
                $amortizacion_capital=$saldo_capital;
            } else {
                $amortizacion_capital=$cuota_estimada-$amortizacion_interes;
            }
            $total_pagar=$amortizacion_interes+$amortizacion_capital;
            $saldo_capital=$saldo_capital-$amortizacion_capital;

            $plan[$n][] = [
                'cuota'=>$n,
                'fecha'=>$fechas[$n],
                'Dias'=>$dias_interes,
                'Capital'=>Util::round($amortizacion_capital),
                'Interes'=>Util::round($amortizacion_interes),
                'Saldo Capital'=>Util::round($saldo_capital)
            ];
            $n+=1;
        }
        return $plan;

    }
}