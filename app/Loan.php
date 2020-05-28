<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;
use Carbon;
use Util;

class Loan extends Model
{
    use Traits\EloquentGetTableNameTrait;
    // use Traits\RelationshipsTrait;
    use PivotEventTrait;
    use SoftDeletes;

    protected $dates = [
        'disbursement_date',
        'request_date'
    ];
    // protected $appends = ['balance', 'estimated_quota', 'defaulted'];
    public $timestamps = true;
    // protected $hidden = ['pivot'];
    public $guarded = ['id'];
    public $fillable = [
        'code',
        'disbursable_id',
        'disbursable_type',
        'procedure_modality_id',
        'disbursement_date',
        'parent_loan_id',
        'parent_reason',
        'request_date',
        'amount_requested',
        'city_id',
        'interest_id',
        'state_id',
        'amount_approved',
        'payable_liquid_calculated',
        'bonus_calculated',
        'indebtedness_calculated',
        'liquid_qualification_calculated',
        'loan_term',
        'payment_type_id',
        'personal_reference_id',
        'account_number',
        'destiny_id',
        'role_id',
        'validated'
    ];

    function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        if (!$this->request_date) {
            $this->request_date = Carbon::now();
        }
        if (!$this->state_id) {
            $state = LoanState::whereName('En Proceso')->first();
            if ($state) {
                $this->state_id = $state->id;
            }
        }
        if (!$this->code) {
            $latest_loan = DB::table('loans')->orderBy('created_at', 'desc')->limit(1)->first();
            if (!$latest_loan) $latest_loan = (object)['id' => 0];
            $this->code = implode(['PTMO', str_pad($latest_loan->id + 1, 6, '0', STR_PAD_LEFT), '-', Carbon::now()->year]);
        }
    }

    public function setProcedureModalityIdAttribute($id)
    {
        $this->attributes['procedure_modality_id'] = $id;
        $this->attributes['interest_id'] = $this->modality->current_interest->id;
    }

    public function personal_reference()
    {
        return $this->belongsTo(PersonalReference::class);
    }

    public function notes()
    {
        return $this->morphMany(Note::class, 'annotable');
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable')->withPivot('user_id', 'date')->withTimestamps();
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function parent_loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function state()
    {
      return $this->belongsTo(LoanState::class, 'state_id','id');
    }
    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function payment_type()
    {
        return $this->belongsTo(PaymentType::class,'payment_type_id','id');
    }

    public function submitted_documents()
    {
        return $this->belongsToMany(ProcedureDocument::class, 'loan_submitted_documents', 'loan_id')->withPivot('reception_date', 'comment', 'is_valid');
    }

    public function getSubmittedDocumentsListAttribute()
    {
        return  [
            'required' => ($this->submitted_documents)->intersect($this->modality->required_documents),
            'optional' => ($this->submitted_documents)->intersect($this->modality->optional_documents)
        ];
    }

    public function guarantors()
    {
        return $this->loan_affiliates()->withPivot('payment_percentage')->whereGuarantor(true);
    }

    public function lenders()
    {
        return $this->loan_affiliates()->withPivot('payment_percentage')->whereGuarantor(false);
    }

    public function loan_affiliates()
    {
        return $this->belongsToMany(Affiliate::class, 'loan_affiliates');
    }

    public function modality()
    {
        return $this->belongsTo(ProcedureModality::class,'procedure_modality_id', 'id');
    }

    public function getDefaultedAttribute()
    {
        return LoanPayment::days_interest($this)->penal > 0 ? true : false;
    }

    public function payments()
    {
        return $this->hasMany(LoanPayment::class)->orderBy('quota_number', 'desc')->orderBy('created_at');
    }
    public function interest()
    {
        return $this->belongsTo(LoanInterest::class, 'interest_id', 'id');
    }

    public function observations()
    {
        return $this->morphMany(Observation::class, 'observable')->latest('updated_at');
    }
    //desembolso --> afiliado, esposa
    public function disbursable()
    {
        return $this->morphTo();
    }

    public function destiny()
    {
        return $this->belongsTo(LoanDestiny::class, 'destiny_id', 'id');
    }
     // add records
    public function records()
    {
        return $this->morphMany(Record::class, 'recordable')->latest('updated_at');
    }
    // Saldo capital
    public function getBalanceAttribute()
    {
        $balance = $this->amount_approved;
        if ($this->payments()->count() > 0) {
            $balance -= $this->payments()->sum('capital_payment');
        }
        return Util::round($balance);
    }

    public function getLastPaymentAttribute()
    {
        return $this->payments()->latest()->first();
    }

    public function getObservedAttribute()
    {
        return ($this->observations()->count() > 0) ? true : false;
    }

    public static function get_percentage($dato)
    {
        if(count($dato)>0){
            return Util::round(1/count($dato)*100);
        }
    }

    public function last_quota()
    {
        $latest_quota = $this->last_payment;
        if ($latest_quota) {
            $payments = $this->payments()->whereQuotaNumber($latest_quota->quota_number)->get();
            $latest_quota = new LoanPayment();
            $latest_quota = $latest_quota->merge($payments);
        }
        return $latest_quota;
    }

    public function getEstimatedQuotaAttribute()
    {
        $monthly_interest = $this->interest->monthly_current_interest;
        unset($this->interest);
        return Util::round($monthly_interest * $this->amount_approved / (1 - 1 / pow((1 + $monthly_interest), $this->loan_term)));
    }

    public function next_payment($estimated_date = null, $amount = null, $liquidate = false)
    {
        do {
            if ($liquidate) {
                $amount = $this->amount_requested * $this->amount_requested;
            } else {
                if (!$amount) $amount = $this->estimated_quota;
            }
            $quota = new LoanPayment();
            $next_payment = LoanPayment::quota_date($this);
            if (!$estimated_date) {
                $quota->estimated_date = $next_payment->date;
            } else {
                $quota->estimated_date = Carbon::parse($estimated_date)->toDateString();
            }
            $quota->quota_number = $this->balance > 0 ? $next_payment->quota : null;
            $interest = $this->interest;
            $quota->estimated_days = LoanPayment::days_interest($this, $quota->estimated_date);
            $quota->paid_days = clone($quota->estimated_days);
            $quota->balance = $this->balance;
            $quota->penal_payment = $quota->accumulated_payment = $quota->interest_payment = $quota->capital_payment = $total_interests = 0;
            // Calcular intereses
            // Interés penal
            do {
                $total_interests -= $quota->penal_payment;
                $quota->penal_payment = Util::round($quota->balance * $interest->daily_penal_interest * $quota->paid_days->penal);
                $total_interests += $quota->penal_payment;
                if ($total_interests > $amount) {
                    $quota->paid_days->penal = intval($amount * $quota->paid_days->penal / $quota->penal_payment);
                    $quota->paid_days->accumulated = $quota->paid_days->current = 0;
                }
            } while ($total_interests > $amount);
            // Interés acumulado
            do {
                $total_interests -= $quota->accumulated_payment;
                $quota->accumulated_payment = Util::round($quota->balance * $interest->daily_current_interest * $quota->paid_days->accumulated);
                $total_interests += $quota->accumulated_payment;
                if ($total_interests > $amount) {
                    $quota->paid_days->accumulated = intval(($amount - $quota->penal_payment) * $quota->paid_days->accumulated / $quota->accumulated_payment);
                    $quota->paid_days->current = 0;
                }
            } while ($total_interests > $amount);
            // Interés corriente
            do {
                $total_interests -= $quota->interest_payment;
                $quota->interest_payment = Util::round($quota->balance * $interest->daily_current_interest * $quota->paid_days->current);
                $total_interests += $quota->interest_payment;
                if ($total_interests > $amount) {
                    $quota->paid_days->current = intval(($amount - $quota->penal_payment - $quota->accumulated_payment) * $quota->paid_days->current / $quota->interest_payment);
                }
            } while ($total_interests > $amount);
            // Calcular amortización de capital
            if ($total_interests > 0) {
                if (($quota->balance + $total_interests) > $amount) {
                    $quota->capital_payment = Util::round($amount - $total_interests);
                } else {
                    $quota->capital_payment = $quota->balance;
                }
            }
            // Calcular monto total de la cuota
            $quota->estimated_quota = $quota->capital_payment + $total_interests;
            $quota->next_balance = Util::round($quota->balance - $quota->capital_payment);
            $quota->penal_remaining = $quota->estimated_days->penal - $quota->paid_days->penal;
            $quota->accumulated_remaining = $quota->estimated_days->accumulated - $quota->paid_days->accumulated + $quota->estimated_days->current - $quota->paid_days->current;
            if ($liquidate) {
                if ($quota->next_balance > 0) {
                    $amount *= $this->amount_requested;
                } else {
                    $liquidate = false;
                }
            }
        } while ($liquidate);
        return $quota;
    }

    public function getPlanAttribute() {
        $plan = [];
        $daily_interest = $this->interest->daily_current_interest;
        $balance = $this->amount_approved;
        $estimated_quota = $this->estimated_quota;
        $i = 0;
        while ($balance > 0) {
            if (count($plan) == 0) {
                $next_payment = LoanPayment::quota_date($this, true);
            } else {
                $next_payment = (object)[
                    'quota' => $plan[$i-1]->quota + 1,
                    'date' => Carbon::parse($plan[$i-1]->date)->startOfMonth()->addMonth()->endOfMonth()->toDateString()
                ];
            }
            $interest = LoanPayment::days_interest($this, $next_payment->date);
            $next_interest = Util::round($balance * $interest->current * $daily_interest);
            if (($balance + $next_interest) > $estimated_quota) {
                $next_balance = $estimated_quota - $next_interest;
            } else {
                $next_balance = $balance;
            }
            $next_balance = Util::round($next_balance);
            $balance = Util::round($balance - $next_balance);
            $total = $next_balance + $next_interest;
            array_push($plan, (object)[
                'quota' => $next_payment->quota,
                'date' => $next_payment->date,
                'days' => $interest->current,
                'estimated_quota' => ($estimated_quota >= $total) ? $total : $estimated_quota,
                'capital' => $next_balance,
                'interest' => $next_interest,
                'next_balance' => $balance
            ]);
            $i++;
        }
        return $plan;
    }

    //obtener modalidad teniendo el tipo y el afiliado
    public static function get_modality($modality_name, $affiliate){
        $modality = null;
        if ($affiliate->affiliate_state){
            $affiliate_state = $affiliate->affiliate_state->name;
            $affiliate_state_type = $affiliate->affiliate_state->affiliate_state_type->name;
        switch($modality_name){
            case 'Préstamo Anticipo':
                if($affiliate_state_type == "Activo")
                {
                    $modality=ProcedureModality::whereShortened("ANT-SA")->first();
                }
                else{
                    if($affiliate_state_type == "Pasivo"){
                        $modality=ProcedureModality::whereShortened("ANT-SP")->first();
                    }
                }
            break;
            case 'Préstamo a corto plazo':
                if($affiliate_state_type == "Activo"){
                    if($affiliate->active_loans()){
                        foreach($affiliate->active_loans() as $loan){
                            if($loan->modality->shortened == 'PCP-SA')
                            $modality = ProcedureModality::whereShortened("PCP-R-SA")->first();//Refinanciamiento corto plazo activo
                            break;
                        }
                    }
                    if(!$modality){
                        if($affiliate_state == "Servicio" || $affiliate_state == "Comisión" )
                        {
                            $modality=ProcedureModality::whereShortened("PCP-SA")->first(); //corto plazo activo
                        }else{
                            $modality=ProcedureModality::whereShortened("PCP-DLA")->first(); // corto plazo activo letra A
                        }
                    }
                }else{
                    if($affiliate_state_type == "Pasivo"){
                        if($affiliate->afp){
                            if($affiliate->active_loans()){
                                foreach($affiliate->active_loans() as $loan){
                                    if($loan->modality->shortened == 'PCP-SP-AFP')
                                    $modality=ProcedureModality::whereShortened("PCP-R-SP-AFP")->first();// refi afp pasivo
                                    break;
                                }
                            }
                            if(!$modality) $modality=ProcedureModality::whereShortened("PCP-SP-AFP")->first(); // Prestamo a corto plazo sector pasivo afp;
                        }else{
                            if($affiliate->active_loans()){
                                foreach($affiliate->active_loans() as $loan){
                                    if($loan->modality->shortened == 'PCP-SP-SEN')
                                    $modality=ProcedureModality::whereShortened("PCP-R-SP-SEN")->first();// refi senasir pasivo
                                    break;
                                }
                            }
                            if(!$modality) $modality=ProcedureModality::whereShortened("PCP-SP-SEN")->first(); // Prestamo a corto plazo senarir
                        }
                    }
                }
                break;
            case 'Préstamo a largo plazo':
                if($affiliate_state_type == "Activo")
                {
                    if($affiliate_state !== "Disponibilidad" ) //cpop no pueden estar en disponibilidad letra A o C
                    {
                        if($affiliate->cpop){
                            if($affiliate->active_loans()){
                                foreach($affiliate->active_loans() as $loan){
                                    if($loan->modality->shortened == 'PLP-CPOP')
                                    $modality=ProcedureModality::whereShortened("PLP-R-SA-CPOP")->first();// Refi largo plazo activo 1 solo garante
                                    break;
                                }
                            }
                            if(!$modality) $modality=ProcedureModality::whereShortened("PLP-CPOP")->first(); // Largo plazo activo cpop
                        }
                    }
                    if(!$modality) $modality=ProcedureModality::whereShortened("PLP-GP-SAYADM")->first(); //Largo plazo activo  y adm con garantia personal 
                }
                else{
                    if($affiliate_state_type == "Pasivo"){
                        if($affiliate->active_loans()){
                            if($affiliate->cpop){
                                foreach($affiliate->active_loans() as $loan){
                                    if($loan->modality->shortened == 'PLP-GP-SP')
                                    $modality=ProcedureModality::whereShortened("PLP-R-SP-CPOP")->first(); // Refi largo plazo pasivo 1 solo garante
                                    break;
                                }
                            }
                        }
                        if(!$modality) $modality=ProcedureModality::whereShortened("PLP-GP-SP")->first(); // largo plazo pasivo con 1 solo garante
                    }
                }
                break;
            case 'Préstamo hipotecario':
                if($affiliate_state_type == "Activo")
                {
                    if($affiliate->cpop){
                        $modality=ProcedureModality::whereShortened("PLP-GH-CPOP")->first(); //hipotecario CPOP
                    }else{
                        $modality=ProcedureModality::whereShortened("PLP-GH-SA")->first();
                    }
                }
                break;
            }
        }
        if ($modality) {
            $modality->loan_modality_parameter;
            return response()->json($modality);
        }
    }
}


