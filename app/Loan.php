<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;
use Carbon\CarbonImmutable;
use App\LoanGlobalParameter;
use App\Rules\LoanIntervalTerm;
use App\Http\Controllers\Api\V1\CalculatorController;
use Carbon;
use Util;
use App\Affiliate;

class Loan extends Model
{
    use Traits\EloquentGetTableNameTrait;
    //use Traits\RelationshipsTrait;
    use PivotEventTrait;
    use SoftDeletes;

    protected $dates = [
        //'disbursement_date',
        'request_date'
    ];
    // protected $appends = ['balance', 'estimated_quota', 'defaulted'];
    public $timestamps = true;
    // protected $hidden = ['pivot'];
    public $guarded = ['id'];
    public $fillable = [
        'code',
        //'disbursable_id',
       // 'disbursable_type',
        'procedure_modality_id',
        'disbursement_date',
        'disbursement_time',
        //'num_budget_certification',
        'num_accounting_voucher',
        'parent_loan_id',
        'parent_reason',
        'request_date',
        'amount_requested',
        'city_id',
        'interest_id',
        'state_id',
        'amount_approved',
        'indebtedness_calculated',
        'indebtedness_calculated_previous',
        'liquid_qualification_calculated',
        'loan_term',
        'refinancing_balance',
        'guarantor_amortizing',
        'payment_type_id',
        'number_payment_type',
        'property_id',
        'destiny_id',
        'financial_entity_id',
        'role_id',
        'validated',
        'user_id',
        'delivery_contract_date',
        'return_contract_date',
        'regional_delivery_contract_date',
        'regional_return_contract_date',
        'payment_plan_compliance',
        'affiliate_id'
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
            if($this->parent_reason == 'REPROGRAMACIÓN' && $this->parent_loan)
            {
                    if(substr($this->parent_loan->code, -3) != substr($this->parent_reason,0,3))
                        $this->code = Loan::find($this->parent_loan_id)->code." - ".substr($this->parent_reason,0,3);
                    else
                        $this->code = $this->parent_loan->code;
            }else{
                $latest_loan = DB::table('loans')->find(DB::table('loans')->max('id'));
                if (!$latest_loan) $latest_loan = (object)['id' => 0];
                $this->code = implode(['PTMO', str_pad($latest_loan->id + 1, 6, '0', STR_PAD_LEFT), '-', Carbon::now()->year]);
            }
        }
    }
    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class,'affiliate_id','id');
    }

    public function loan_plan()
    {
        return $this->hasMany(LoanPlanPayment::class)->orderBy('quota_number');
    }

    public function setProcedureModalityIdAttribute($id)
    {
        $this->attributes['procedure_modality_id'] = $id;
        $this->attributes['interest_id'] = $this->modality->current_interest->id;
    }

    public function loan_property()
    {
        return $this->belongsTo(LoanProperty::class, 'property_id','id');
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

    public function financial_entity()
    {
        return $this->belongsTo(FinancialEntity::class,'financial_entity_id','id');
    }

    public function submitted_documents()
    {
        return $this->belongsToMany(ProcedureDocument::class, 'loan_submitted_documents', 'loan_id')->withPivot('reception_date', 'comment', 'is_valid');
    }
    public function documents_modality()
    {     
        $ids= $this->submitted_documents->pluck('id');
        $documents=collect();
        foreach($ids as $id){
           $documents->push(ProcedureDocument::where('procedure_documents.id',$id)->leftJoin('procedure_requirements as req','req.procedure_document_id','=','procedure_documents.id')->select('procedure_documents.name','req.number')->first());
        }  
         return (object)$documents->sortBy('number');
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
        return $this->loan_affiliates()->withPivot('payment_percentage','payable_liquid_calculated', 'bonus_calculated', 'quota_previous','quota_treat','indebtedness_calculated','indebtedness_calculated_previous','liquid_qualification_calculated','contributionable_ids','contributionable_type','type')->whereGuarantor(true);
    }

    public function lenders()
    {
        return $this->loan_affiliates()->withPivot('payment_percentage','payable_liquid_calculated', 'bonus_calculated', 'quota_previous','quota_treat', 'indebtedness_calculated','indebtedness_calculated_previous','liquid_qualification_calculated','contributionable_ids','contributionable_type','type')->whereGuarantor(false);
    }

    public function loan_affiliates()
    {
        return $this->belongsToMany(Affiliate::class, 'loan_affiliates')->withPivot('payment_percentage','guarantor','payable_liquid_calculated', 'bonus_calculated', 'quota_previous','quota_treat', 'indebtedness_calculated','indebtedness_calculated_previous','liquid_qualification_calculated','contributionable_ids','contributionable_type','type');
    }
    
    public function loan_affiliates_ballot()
    {
        return $this->belongsToMany(Affiliate::class, 'loan_affiliates')->withPivot('contributionable_ids','contributionable_type');
    }

    public function personal_references()
    {
        return $this->loan_persons()->withPivot('cosigner')->whereCosigner(false);
    }

    public function cosigners()
    {
        return $this->loan_persons()->withPivot('cosigner')->whereCosigner(true);
    }

    public function loan_persons()
    {
        return $this->belongsToMany(PersonalReference::class, 'loan_persons');
    }

    public function modality()
    {
        return $this->belongsTo(ProcedureModality::class,'procedure_modality_id', 'id');
    }

    public function getDefaultedAttribute()
    {
        return LoanPayment::days_interest2($this)->penal > 0 ? true : false;
    }
    public function getdelay()
    {
        return LoanPayment::days_interest2($this);
    }
    public function getdelay_parcial()
    {
        return LoanPayment::days_interest2($this)->interest_accumulated > 0 ? true : false;
    }

    public function payments()
    {
        return $this->hasMany(LoanPayment::class)->orderBy('quota_number', 'desc')->orderBy('created_at');
    }
    public function payments_pendings_confirmations()
    {
        $state_id = LoanPaymentState::whereName('Pendiente por confirmar')->first()->id;
        return $this->hasMany(LoanPayment::class)->where('state_id', $state_id)->orderBy('quota_number', 'desc')->orderBy('created_at');
    }
    public function payment_pending_confirmation()//pago de pendiente por confirmacion para refin
    {
        $state_id = LoanPaymentState::whereName('Pendiente por confirmar')->first()->id;
        return $this->hasMany(LoanPayment::class)->where('state_id', $state_id)->orderBy('quota_number', 'desc')->orderBy('created_at')->first();
    }
    public function paymentsKardex()
    {
        $id_pagado = LoanPaymentState::where('name','Pagado')->first();
        $id_pendiente = LoanPaymentState::where('name', 'Pendiente por confirmar')->first();
        return $this->hasMany(LoanPayment::class)->whereIn('state_id', [$id_pagado->id, $id_pendiente->id])->orderBy('quota_number', 'asc');
    }
    //relacion uno a muchos
    public function loan_contribution_adjusts()
    {
        return $this->hasMany(LoanContributionAdjust::class);
    }
    public function interest()
    {
        return $this->belongsTo(LoanInterest::class, 'interest_id', 'id');
    }
    public function data_loan()
    {
        return $this->hasOne(Sismu::class,'loan_id','id');
    }
    
    public function getRecordsUserAttribute()
    {
        return $this->records()->first()->user();
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
        $loan_states = LoanPaymentState::where('name', 'Pagado')->orWhere('name', 'Pendiente por confirmar')->get();
        if ($this->payments()->count() > 0) {
            $balance -= $this->payments()->where('state_id', $loan_states->first()->id)->sum('capital_payment');
            $balance -= $this->payments()->where('state_id', $loan_states->last()->id)->sum('capital_payment');
        }
        return Util::round($balance);
    }

    public function getLastPaymentAttribute()
    {
        return $this->payments()->latest()->first();
    }

    public function getLastPaymentValidatedAttribute()
    {
        $loan_states = LoanPaymentState::where('name', 'Pagado')->orWhere('name', 'Pendiente por confirmar')->get();
        $payment = $this->payments()->whereLoanId($this->id)->where('state_id', $loan_states->first()->id)->orWhere('state_id',$loan_states->last()->id)->whereLoanId($this->id)->latest()->first();
        return $payment;
    }

    public function last_payment_date($date_final)
    {
        $loan_states = LoanPaymentState::where('name', 'Pagado')->first();
        return $this->payments()->whereLoanId($this->id)->where('state_id', $loan_states->id)->Where('estimated_date','<=', $date_final)->orderBy('estimated_date', 'asc')->limit(1)->first();
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
        return Util::round2($monthly_interest * $this->amount_approved / (1 - 1 / pow((1 + $monthly_interest), $this->loan_term)));
    }

    public function next_payment2($affiliate_id, $estimated_date, $paid_by, $procedure_modality_id, $estimated_quota, $liquidate = false)
    {
        $grace_period = LoanGlobalParameter::latest()->first()->grace_period;

        // nuevos calculos
        $quota = new LoanPayment();
        $quota->transaction_date = Carbon::now()->format('Y-m-d H:i:s');
        $sw = false;
        $latest_quota = $this->last_payment_validated;
        $quota->estimated_date = $estimated_date;
        $quota->previous_balance = $this->balance;
        $quota->previous_payment_date = $latest_quota ? Carbon::parse($latest_quota->estimated_date)->endOfDay() : Carbon::parse($this->disbursement_date)->endOfDay();
        $quota->quota_number = $this->paymentsKardex->count() + 1;
        $date_ini = CarbonImmutable::parse($this->disbursement_date);
        $penal_days = 0;
        if($date_ini->day <= LoanGlobalParameter::latest()->first()->offset_interest_day){
            $date_pay = $date_ini->endOfMonth()->endOfDay()->format('Y-m-d');
        }
        else{
            $date_pay = $date_ini->startOfMonth()->addMonth()->endOfMonth()->endOfDay()->format('Y-m-d');
            $sw = true;
        }
        $date_pay = Carbon::parse($date_pay)->endOfDay();
        $estimated_date = Carbon::parse($estimated_date)->endOfDay();
        if($quota->quota_number == 1 && $estimated_date <= $date_pay){
            $penal_days = 0;
            $current_days = (Carbon::parse($quota->previous_payment_date)->diffInDays(Carbon::parse($estimated_date)));
            if($estimated_date != $date_pay){
                $interest_generated = LoanPayment::interest_by_days($current_days, $this->interest->annual_interest, $this->balance);
            }
            else{
                if($sw)
                    $interest_generated = LoanPayment::interest_by_days(Carbon::parse($this->disbursement_date)->endOfMonth()->format('d') - Carbon::parse($this->disbursement_date)->format('d'), $this->interest->annual_interest, $this->balance) + LoanPayment::interest_by_days(Carbon::parse($this->disbursement_date)->startOfMonth()->addMonth()->endOfMonth()->format('d'),$this->interest->annual_interest,$this->balance);
                else
                    $interest_generated = LoanPayment::interest_by_days($current_days, $this->interest->annual_interest, $this->balance);
            }
        }
        else{
            $current_days = (Carbon::parse($quota->previous_payment_date)->diffInDays(Carbon::parse($estimated_date)));
            $interest_generated = LoanPayment::interest_by_days($current_days, $this->interest->annual_interest, $this->balance);
            if($current_days > 31)
                $penal_days = (Carbon::parse($quota->previous_payment_date)->diffInDays(Carbon::parse($estimated_date)) - 31);
        }

        //dias y montos estimados
        $estimated_days = [
            'current' => $current_days,
            'current_generated' => $interest_generated,
            //'current_generated' => LoanPayment::interest_by_days($current_days, $this->interest->annual_interest, $this->balance),
            'interest_accumulated' => $latest_quota ? $latest_quota->interest_accumulated : 0,
            'penal' => $penal_days,
            'penal_generated' => LoanPayment::interest_by_days($penal_days, $this->interest->penal_interest, $this->balance),
            'penal_accumulated' => $latest_quota ? $latest_quota->penal_accumulated : 0,
        ];
        //$quota->estimated_days = $estimated_days;
        $quota->estimated_days = $estimated_days;
        $quota->paid_days = $quota->estimated_days;
        $quota->balance = $this->balance;
        $quota->penal_remaining = $quota->paid_days['penal_accumulated'];
        $quota->penal_payment  = 0;
        $quota->interest_remaining = $quota->paid_days['interest_accumulated'];
        $quota->capital_payment = $total_interests = $quota->interest_payment = 0;
        $quota->penal_accumulated = $quota->interest_accumulated = 0;
        //return $quota;
        $total_interests = 0;
        $partial_amount = 0;
        $interest = $this->interest;
        $amount = $estimated_quota;
        

        // Calcular intereses

        // Interes acumulado penal

        if($quota->penal_remaining > 0){
            if($amount >= $quota->penal_remaining){
                $amount = $amount - $quota->penal_remaining;
                //$quota->penal_remaining = 0;
            }
            else{
                $quota->penal_remaining = $amount;
                $amount = 0;
            }
        }
        else{
            $quota->penal_remaining = 0;
        }
        $total_interests += $quota->penal_remaining;

        // Interes acumulado corriente
        
        if($quota->interest_remaining > 0){
            if($amount >= $quota->interest_remaining){
                $amount = $amount - $quota->interest_remaining;
                //$quota->interest_remaining = 0;
            }
            else{
                $quota->interest_remaining = $amount;
                $amount = 0;
            }
        }
        else{
            $quota->interest_remaining = 0;
        }
        $total_interests += $quota->interest_remaining;

        // Interés penal 

        if($quota->estimated_days['penal'] >= $grace_period){
            //$quota->penal_payment = Util::round2($quota->balance * $interest->daily_penal_interest * $quota->paid_days['penal']);
            $quota->penal_payment = LoanPayment::interest_by_days($penal_days, $this->interest->penal_interest, $this->balance);
            if($quota->penal_payment >= 0){
                if($amount >= $quota->penal_payment){
                    $amount = $amount - $quota->penal_payment;
                }
                else{
                    $quota->penal_accumulated = Util::round2($quota->penal_remaining + ($quota->penal_payment - $amount));
                    //$quota->penal_remaining = $quota->penal_remaining + ($quota->penal_payment - $amount);
                    $quota->penal_payment = $amount;
                    $amount = 0;
                }
            }else{
                $quota->penal_payment = 0;
            }
            $total_interests += $quota->penal_payment;
        }


        // Interés corriente
        $quota->interest_payment = $interest_generated;
        if($amount >= $quota->interest_payment){
                $amount = $amount - $quota->interest_payment;
        }
        else{
            $quota->interest_accumulated = $quota->interests_remaining + ($quota->interest_payment - $amount);
            $quota->interest_payment = $amount;
            $amount = 0;
        }

        $total_interests += Util::round2($quota->interest_payment);

        // Calcular amortización de capital        
        if($liquidate)
        {
            $quota->capital_payment = $quota->balance;
        }
        else{
            if($this->regular_payment() && $this->payments->count()+1 == $this->loan_term){
                $quota->capital_payment = Util::round2($this->balance);
            }
            else{
                if($amount >= $this->balance){
                    $quota->capital_payment = Util::round2($this->balance);
                }
                else
                    $quota->capital_payment = Util::round2($amount);
            }
        }
        //calculo de la ultima cuota, solo si fue regular en los pagos

        /*if($this->verify_regular_payments() && $this->payments->count() == $this->loan_term){
            $amount = $this->balance + $quota->estimated_days;
            $quota->est += $quota->next_balance;
            $quota->next_balance = 0;
        }*/
        // Calcular monto total de la cuota

        if ($quota->balance == $quota->capital_payment) {
            $quota->next_balance = 0;
        } else {
            $quota->next_balance = Util::round2($this->balance - $quota->capital_payment);
        }
        $quota->estimated_quota = Util::round2($quota->capital_payment + $total_interests);
        $quota->next_balance = Util::round2($quota->balance - $quota->capital_payment);


        //calculo de los nuevos montos restantes

        $quota->penal_accumulated = Util::round2($quota->penal_accumulated + ($quota->estimated_days['penal_accumulated'] - $quota->penal_remaining));
        $quota->interest_accumulated = Util::round2($quota->interest_accumulated + ($quota->estimated_days['interest_accumulated'] - $quota->interest_remaining));

        //redondeos

        $quota->interest_remaining = Util::round2($quota->interest_remaining);
        $quota->penal_remaining = Util::round2($quota->penal_remaining);
        //$quota->excesive_payment = Util::round($total_amount - ($quota->estimated_quota));


        //validacion pago excesivo
        
        /*if($total_amount == 0)
            $quota->excesive_payment = 0;
        else
            $quota->excesive_payment = Util::round($total_amount - $quota->estimated_quota);*/

        //$total_amount = $quota->penal_accumulated + $quota->interest_accumulated + $loan->balance + $quota->interest_remaining + $quota->penal_remaining;

        //

        return $quota;
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
                $quota->penal_payment = Util::round2($quota->balance * $interest->daily_penal_interest * $quota->paid_days->penal);
                $total_interests += $quota->penal_payment;
                if ($total_interests > $amount) {
                    $quota->paid_days->penal = intval($amount * $quota->paid_days->penal / $quota->penal_payment);
                    $quota->paid_days->accumulated = $quota->paid_days->current = 0;
                }
            } while ($total_interests > $amount);
            // Interés acumulado
            do {
                $total_interests -= $quota->accumulated_payment;
                $quota->accumulated_payment = Util::round2($quota->balance * $interest->daily_current_interest * $quota->paid_days->accumulated);
                $total_interests += $quota->accumulated_payment;
                if ($total_interests > $amount) {
                    $quota->paid_days->accumulated = intval(($amount - $quota->penal_payment) * $quota->paid_days->accumulated / $quota->accumulated_payment);
                    $quota->paid_days->current = 0;
                }
            } while ($total_interests > $amount);
            // Interés corriente
            do {
                $total_interests -= $quota->interest_payment;
                $quota->interest_payment = Util::round2($quota->balance * $interest->daily_current_interest * $quota->paid_days->current);
                $total_interests += $quota->interest_payment;
                if ($total_interests > $amount) {
                    $quota->paid_days->current = intval(($amount - $quota->penal_payment - $quota->accumulated_payment) * $quota->paid_days->current / $quota->interest_payment);
                }
            } while ($total_interests > $amount);
            // Calcular amortización de capital
            //if ($total_interests > 0) {
                if (($quota->balance + $total_interests) > $amount) {
                    if($quota->quota_number == 1 && $quota->estimated_days->accumulated < 31){
                        $quota->capital_payment = Util::round2($amount + $quota->accumulated_payment - $total_interests);
                    }else{
                        $quota->capital_payment = Util::round2($amount - $total_interests);
                    }
                } else {
                    $quota->capital_payment = $quota->balance;
                }
            //}
            // Calcular monto total de la cuota
            $quota->estimated_quota = Util::round2($quota->capital_payment + $total_interests);
            $quota->next_balance = Util::round2($quota->balance - $quota->capital_payment);

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

    //obtener modalidad teniendo el tipo y el afiliado
    public static function get_modality($modality, $affiliate, $type_sismu, $cpop_affiliate,$remake_loan){
        $verify=false;
        $modality_name=$modality->name;
        if(strpos($modality->name, 'Refinanciamiento') === false){//para restringir para no tener prestamos paralelos de la misma sub mod
            foreach ($affiliate->active_loans() as $loan) {
                if($loan->modality->procedure_type->id == $modality->id)
                $verify=true;
            }
        }

        if($verify && !$remake_loan) abort(403, 'El affiliado tiene préstamos activos en la modalidad: '.$modality_name);
    
        $modality = null;
        if ($affiliate->affiliate_state){
            $affiliate_state = $affiliate->affiliate_state->name;
            $affiliate_state_type = $affiliate->affiliate_state->affiliate_state_type->name;
        switch($modality_name){
            case 'Préstamo Anticipo':
                if($affiliate_state_type == "Activo"){
                    if($affiliate_state == "Servicio" || $affiliate_state == "Comisión" )
                    {
                        $modality=ProcedureModality::whereShortened("ANT-ACT")->first(); //Anticipo activo
                    }else{
                        $modality=ProcedureModality::whereShortened("ANT-DIS")->first(); // Anicipo dismponibilidad
                    }
                }
                if($affiliate_state_type == "Pasivo"){
                    if($affiliate->pension_entity->name != 'SENASIR')
                    {
                    $modality=ProcedureModality::whereShortened("ANT-AFP")->first(); //  Prestamo a anticipo afp
                    }else{
                        $modality=ProcedureModality::whereShortened("ANT-SEN")->first(); // Prestamo a anticipo senasir
                    }
                }
            break;
            case 'Préstamo a Corto Plazo':
                if($affiliate_state_type == "Activo"){
                    if($affiliate_state == "Servicio" || $affiliate_state == "Comisión" )
                    {
                        $modality=ProcedureModality::whereShortened("COR-ACT")->first(); //corto plazo activo
                    }else{
                        $modality=ProcedureModality::whereShortened("COR-DIS")->first(); // corto plazo activo letra A, no le corresponde refinanciamiento segun Art 76 del reglamento
                    }
                }
                if($affiliate_state_type == "Pasivo"){
                    if($affiliate->pension_entity->name != 'SENASIR')
                    {
                    $modality=ProcedureModality::whereShortened("COR-AFP")->first(); //  Prestamo a corto plazo sector pasivo afp caso SISMU
                    }else{
                        $modality=ProcedureModality::whereShortened("COR-SEN")->first(); // Prestamo a corto plazo senarir caso SISMU
                    }
                }
            break;
            case 'Refinanciamiento Préstamo a Corto Plazo':
                if($affiliate_state_type == "Activo" && $affiliate_state !== "Disponibilidad") //affiliados con estado en disponibilidad no realizaran refinanciamientos 
                {
                    $modality = ProcedureModality::whereShortened("REF-COR-ACT")->first();//Refinanciamiento corto plazo activo                           
                }else{
                    if($affiliate_state_type == "Pasivo"){
                    
                        if($affiliate->pension_entity->name != 'SENASIR')
                        {
                        $modality=ProcedureModality::whereShortened("REF-COR-AFP")->first();// refi afp pasivo sismu
                        }else{
                            $modality=ProcedureModality::whereShortened("REF-COR-SEN")->first();// refi senasir pasivo sismu
                        }
                    }
                }
            break;
            case 'Préstamo a Largo Plazo':
                if($affiliate_state_type == "Activo")
                {
                    if($affiliate_state !== "Disponibilidad" ) // disponibilidad letra A o C no puede acceder a prestamos a largo plazo
                    {
                        if($cpop_affiliate){
                            $modality=ProcedureModality::whereShortened("LAR-CPOP")->first();
                        }else{
                            $modality=ProcedureModality::whereShortened("LAR-ACT")->first();
                        }
                    }
                }
                if($affiliate_state_type == "Pasivo")
                {
                    if((!$cpop_affiliate)){
                        if($affiliate->pension_entity->name != 'SENASIR')
                        {
                            $modality=ProcedureModality::whereShortened("LAR-AFP")->first();// Largo plazo Sector PAsivo
                        }else{
                            $modality=ProcedureModality::whereShortened("LAR-SEN")->first();// Largo plazo Sector PAsivo
                        }
                    }
                }
            break;  
            case 'Refinanciamiento Préstamo a Largo Plazo':
                if($affiliate_state_type == "Activo")
                {
                    if($affiliate_state !== "Disponibilidad" ) //disponibilidad letra A o C no tiene prestamos
                    {
                        if($cpop_affiliate){
                            $modality=ProcedureModality::whereShortened("REF-ACT-CPOP")->first(); //refi largo plazo activo  cpop
                        }else{
                            $modality=ProcedureModality::whereShortened("REF-LAR-ACT")->first(); //refi largo plazo activo
                        }
                    }
                }
                else{
                    if($affiliate_state_type == "Pasivo"){
                        if((!$cpop_affiliate)){
                            if($affiliate->pension_entity->name != 'SENASIR')
                            {
                                $modality=ProcedureModality::whereShortened("REF-LAR-AFP")->first();// ref Largo plazo Sector Pasivo
                            }else{
                                $modality=ProcedureModality::whereShortened("REF-LAR-SEN")->first();// ref Largo plazo Sector Pasivo
                            }
                        }
                    }
                }
            break;
            case 'Préstamo Hipotecario':
                if($affiliate_state_type == "Activo")
                {
                    if($affiliate_state_type !== "Comisión"){
                        
                        if($cpop_affiliate){
                            $modality=ProcedureModality::whereShortened("HIP-ACT-CPOP")->first(); //hipotecario CPOP
                        }else{
                            $modality=ProcedureModality::whereShortened("HIP-ACT")->first(); //hipotecario Sector Activo
                        }
                    }
                }
            break;
            case 'Refinanciamiento Préstamo Hipotecario':
                if($affiliate_state_type == "Activo")
                {
                    if($affiliate_state_type !== "Comisión" && $affiliate_state !== "Disponibilidad"){//affiliados con estado en disponibilidad no realizaran refinanciamientos 
                        if($cpop_affiliate){
                            $modality=ProcedureModality::whereShortened("REF-HIP-ACT-CPOP")->first(); // Refinanciamiento hipotecario CPOP
                        }else{
                            $modality=ProcedureModality::whereShortened("REF-HIP-ACT")->first(); // Refinanciamiento hipotecario Sector Activo
                        }
                    }                   
                }
            break;
            }
        }
        if ($modality) {
            $modality->loan_modality_parameter;
            $modality->procedure_type;
            return response()->json($modality);
        }else{
            return response()->json();
        }
    }


    //verificar pagos manuales consecutivos
   public function verify_payment_consecutive()
   {
     $loan_global_parameter  = $loan_global_parameter = LoanGlobalParameter::latest()->first();
     $number_payment_consecutive = $loan_global_parameter->consecutive_manual_payment;//3
     $modality_id=ProcedureModality::whereShortened("DIRECTO")->first()->id;

     $Pagado = LoanPaymentState::whereName('Pagado')->first()->id;
    
     $payments=$this->payments->where('procedure_modality_id','=',$modality_id)->where('state_id','=',$Pagado)->sortBy('estimated_date');
    
     $consecutive=1;
     $verify=false;

     if(count($payments)>=$number_payment_consecutive){
        foreach($payments as $i => $payment){
            // $j=$i+1;
             foreach($payments as $j => $paymentd){
              $stimated_date=CarbonImmutable::parse($payments[$i]->estimated_date);
              $stimated_date_compare=CarbonImmutable::parse($payments[$j++]->estimated_date);
              //return $payments[$j++]->estimated_date;
              if($stimated_date->startOfMonth()->diffInMonths($stimated_date_compare->startOfMonth()) == $consecutive){
               $consecutive++;
              }else{
               $consecutive=1;
              }
           }
           if($consecutive >= $number_payment_consecutive){
               $verify=true;
               break;
           }
           $consecutive=1;
         }
     }else{
        $verify=false;
     }
    return $verify;
   }
    public function get_sismu(){
        return Sismu::find($this->id);
    }
    public function user(){
        return $this->hasOne(User::class,'id','user_id');
    }

    //obtener mod 
    public static function get_modality_search($modality_name, $affiliate){
        $modality = null;
        if ($affiliate->affiliate_state){
            $affiliate_state = $affiliate->affiliate_state->name;
            $affiliate_state_type = $affiliate->affiliate_state->affiliate_state_type->name;
        switch($modality_name){
            case 'Préstamo Anticipo':
                if($affiliate_state_type == "Activo"){
                    if($affiliate_state == "Servicio" || $affiliate_state == "Comisión" )
                    {
                        $modality=ProcedureModality::whereShortened("ANT-ACT")->first(); //Anticipo activo
                    }else{
                        $modality=ProcedureModality::whereShortened("ANT-DIS")->first(); // Anicipo dismponibilidad
                    }
                }
            break;
            case 'Préstamo a Corto Plazo':
                if($affiliate_state_type == "Activo"){
                    if($affiliate_state == "Servicio" || $affiliate_state == "Comisión" )
                    {
                        $modality=ProcedureModality::whereShortened("COR-ACT")->first(); //corto plazo activo
                    }else{
                        $modality=ProcedureModality::whereShortened("COR-DIS")->first(); // corto plazo activo letra A, no le corresponde refinanciamiento segun Art 76 del reglamento
                    }
                }
            break;
            case 'Préstamo a Largo Plazo':
                if($affiliate_state_type == "Activo")
                {
                    if($affiliate_state !== "Disponibilidad" ) // disponibilidad letra A o C no puede acceder a prestamos a largo plazo
                    {
                        $modality=ProcedureModality::whereShortened("LAR-ACT")->first();
                    }
                }
            break; 
            case 'Préstamo Hipotecario':
                if($affiliate_state_type == "Activo")
                {
                    if($affiliate_state_type !== "Comisión"){
                        $modality=ProcedureModality::whereShortened("HIP-ACT")->first(); //hipotecario Sector Activo
                    }
                }
            break;
            }
        }
        if ($modality) {
            $modality->loan_modality_parameter;
            return $modality;
        }else{
            $modality=[];
            return $modality;
        }
    }
    //Saldo del padre a refinanciar 
    public function balance_parent_refi(){
        $balance_parent = 0;
       if($this->data_loan){
        $balance_parent=$this->data_loan->balance;
       }else{
           if($this->parent_loan && $this->parent_loan->payment_pending_confirmation() != null){
            $balance_parent = $this->parent_loan->payment_pending_confirmation()->estimated_quota;
           }
       }
       return  $balance_parent;

    }
    //fecha de corte para refi
     public function date_cut_refinancing(){
         $date_cut_refinancing= null;
       if($this->data_loan){
         $date_cut_refinancing=$this->data_loan->date_cut_refinancing;
       }else{
           if($this->parent_loan && $this->parent_loan->payment_pending_confirmation() != null){
            $date_cut_refinancing = $this->parent_loan->payment_pending_confirmation()->estimated_date;
           }
       }
       return  $date_cut_refinancing;
    }

    //Verifica si los pagos realizados fueron regulares y sin mora
    public function regular_payment()
    {
        $loan_payments = $this->payments;
        $quota_number = 1;
        $sw = false;
        foreach($loan_payments as $payments){
            if($quota_number == 1 && $payments->estimated_quota >= $this->estimated_quota)
                $sw = true;
            else{
                if($payments->estimated_quota == $this->estimated_quota)
                    $sw = true;
                else
                    break;
            }
            $quota_number++;
        }
        return $sw;
    }

    //verificacion de saldo con pagos
    public function verify_balance()
    {
        $payments = $this->payments;
        $loan_state = LoanPaymentState::where('name', 'Pagado')->first();
        $balance = $this->amount_approved;
        $sum = 0;
        foreach($payments as $payment)
        {
            if($payment->state_id == $loan_state->id)
                $sum += $payment->capital_payment;
        }
        return round($balance - $sum);
        //return ($balance - $this->payments->where('state_id', LoanPaymentState::where('name', 'Pagado')->first()->id)->sum('capital_payment'));
    }
    //muestra boletas de afiliado
    public function ballot_affiliate($affiliate_id){
        foreach($this->loan_affiliates as $affiliate){  
            if( $affiliate->id == $affiliate_id){
            $contributions = $affiliate->pivot->contributionable_ids;
            $contributions_type = $affiliate->pivot->contributionable_type;
            $ballots=json_decode($contributions);
            $ballot = collect();
            $adjusts = collect();
            $ballot_adjust = collect();
            $average_ballot_adjust = collect();
            $mount_adjust = 0;
            $sum_payable_liquid = 0;
            $sum_mount_adjust = 0;
            $sum_border_bonus = 0;
            $sum_position_bonus = 0;
            $sum_east_bonus = 0;
            $sum_public_security_bonus = 0;
            $sum_dignity_rent = 0;
            $count_records = 0;
            $contribution_type = null;
                if($contributions_type == "contributions"){ 
                    $contribution_type = "contributions";
                    foreach($ballots as $is_ballot_id){
                        if(Contribution::find($is_ballot_id))
                        $ballot->push(Contribution::find($is_ballot_id));
                        if(LoanContributionAdjust::where('adjustable_id', $is_ballot_id)->where('loan_id',$this->id)->first()){
                        $adjusts->push(LoanContributionAdjust::where('adjustable_id', $is_ballot_id)->where('loan_id',$this->id)->first());
                        }  
                    }    
                    $count_records = count($ballot);
                    $ballots = $ballot->sortBy('month_year');               
                    foreach($ballots as $ballot){
                        foreach($adjusts as $adjust){
                                if  ($ballot->id == $adjust->adjustable_id)
                                    $mount_adjust = $adjust->amount;                  
                        }
                        $ballot_adjust->push([
                            'month_year' => $ballot->month_year,
                            'payable_liquid' => $ballot->payable_liquid,
                            'mount_adjust' => $mount_adjust,
                            'border_bonus' => $ballot->border_bonus,
                            'position_bonus' => $ballot->position_bonus,
                            'east_bonus' => $ballot->east_bonus,
                            'public_security_bonus' => $ballot->public_security_bonus,                                
                        ]);    
                          $sum_payable_liquid = $sum_payable_liquid + $ballot->payable_liquid;
                          $sum_mount_adjust = $sum_mount_adjust + $mount_adjust;
                          $sum_border_bonus = $sum_border_bonus + $ballot->border_bonus;  
                          $sum_position_bonus = $sum_position_bonus + $ballot->position_bonus;
                          $sum_east_bonus = $sum_east_bonus + $ballot->east_bonu;
                          $sum_public_security_bonus = $sum_public_security_bonus + $ballot->public_security_bonus;     
                    }                              
                    $average_ballot_adjust->push([
                        'average_payable_liquid' => $sum_payable_liquid/$count_records,
                        'average_mount_adjust' => $sum_mount_adjust/$count_records,
                        'average_border_bonus' => $sum_border_bonus/$count_records,
                        'average_position_bonus' => $sum_position_bonus/$count_records,
                        'average_east_bonus' => $sum_east_bonus/$count_records,
                        'average_public_security_bonus' => $sum_public_security_bonus/$count_records,                       
                    ]);  
                }
                if($contributions_type == "aid_contributions"){
                    $contribution_type = "aid_contributions";
                    foreach($ballots as $is_ballot_id){
                        if(AidContribution::find($is_ballot_id))
                        $ballot->push(AidContribution::find($is_ballot_id));
                        if(LoanContributionAdjust::where('adjustable_id', $is_ballot_id)->where('loan_id',$this->id)->first())
                        $adjusts->push(LoanContributionAdjust::where('adjustable_id', $is_ballot_id)->where('loan_id',$this->id)->first());
                    }
                    $count_records = count($ballot); 
                    $ballots = $ballot->sortBy('month_year');                
                    foreach($ballots as $ballot){
                        foreach($adjusts as $adjust){
                            if($ballot->id == $adjust->adjustable_id)
                            $mount_adjust = $adjust->amount;
                        }
                        $ballot_adjust->push([
                            'month_year' => $ballot->month_year,
                            'payable_liquid' => $ballot->rent,
                            'mount_adjust' => $mount_adjust,
                            'dignity_rent' => $ballot->dignity_rent,                              
                        ]); 
                        $sum_payable_liquid = $sum_payable_liquid + $ballot->rent; 
                        $sum_mount_adjust = $sum_mount_adjust + $mount_adjust; 
                        $sum_dignity_rent = $sum_dignity_rent + $ballot->dignity_rent;
                    }
                    $average_ballot_adjust->push([
                        'average_payable_liquid' => $sum_payable_liquid/$count_records,
                        'average_mount_adjust' => $sum_mount_adjust/$count_records,
                        'average_dignity_rent' => $sum_dignity_rent/$count_records,
                    ]);                     
                }
                if($contributions_type == "loan_contribution_adjusts"){
                    $contribution_type = "loan_contribution_adjusts";
                    $liquid_ids= LoanContributionAdjust::where('loan_id',$this->id)->where('type_adjust',"liquid")->get()->pluck('id');
                    $adjust_ids= LoanContributionAdjust::where('loan_id',$this->id)->where('type_adjust',"adjust")->get()->pluck('id');
                    foreach($liquid_ids as $liquid_id){  
                        $ballot->push(LoanContributionAdjust::find($liquid_id));
                    }
                    foreach($adjust_ids as $adjust_id){  
                        $adjusts->push( LoanContributionAdjust::find($adjust_id));
                    } 
                    $count_records = count($ballot);   
                    $ballots = $ballot->sortBy('month_year');    
                    foreach($ballots as $ballot)
                    {
                        foreach($adjusts as $adjust){
                        if($list_ballot->period_date == $adjust->period_date)
                           $mount_adjust = $adjust->amount;
                        }
                        $ballot_adjust->push([
                            'month_year' => $ballot->period_date,
                            'payable_liquid' => $ballot->amount,
                            'mount_adjust' => $mount_adjust,                              
                        ]); 
                        $sum_payable_liquid = $sum_payable_liquid + $ballot->amount;
                        $sum_mount_adjust = $sum_mount_adjust + $mount_adjust; 
                    }            
                    $average_ballot_adjust->push([
                        'average_payable_liquid' => $sum_payable_liquid/$count_records,
                        'average_mount_adjust' => $sum_mount_adjust/$count_records,
                    ]);         
                }       
            }      
        }
        $data = [
            'contribution_type' =>$contribution_type,
            'average_ballot_adjust'=> $average_ballot_adjust,
            'ballot_adjusts'=> $ballot_adjust,
        ];
        return (object)$data; 
    }

    public function getPlanAttribute()
    {
        $plan = [];
        $loan_global_parameter = LoanGlobalParameter::latest()->first();
        $balance = $this->amount_approved;
        $days_aux = 0;
        $interest_rest = 0;
        $estimated_quota = $this->estimated_quota;
        for($i = 1 ;$i<= $this->loan_term; $i++){
            if($i == 1){
                $date_ini = Carbon::parse($this->disbursement_date)->format('d-m-Y');
                if(Carbon::parse($date_ini)->format('d') <= $loan_global_parameter->offset_interest_day){
                    $date_fin = Carbon::parse($date_ini)->endOfMonth();
                    $days = $date_fin->diffInDays($date_ini);
                    $interest = LoanPayment::interest_by_days($days, $this->interest->annual_interest, $balance);
                    $capital = $estimated_quota - $interest;
                }
                else{
                    $date_fin = Carbon::parse($date_ini)->startOfMonth()->addMonth()->endOfMonth();
                    $capital = ($estimated_quota - LoanPayment::interest_by_days($date_fin->day, $this->interest->annual_interest, $balance));
                    $days = $date_fin->diffInDays($date_ini);
                    $interest = LoanPayment::interest_by_days($date_fin->day, $this->interest->annual_interest, $balance) + LoanPayment::interest_by_days(Carbon::parse($date_ini)->endOfMonth()->format('d') - Carbon::parse($date_ini)->format('d'), $this->interest->annual_interest, $balance);
                }
                $payment = round(($capital + $interest),2);
            }
            else{
                $date_fin = Carbon::parse($date_ini)->endOfMonth();
                $days = $date_fin->diffInDays($date_ini)+1;
                $interest = LoanPayment::interest_by_days($days, $this->interest->annual_interest, $balance);
                $capital = $estimated_quota - $interest;
                $payment = $estimated_quota;
            }
            $balance = ($balance - $capital);
            if($i == 1){
                array_push($plan, (object)[
                'nro' => $i,
                'date' => Carbon::parse($date_fin)->format('d-m-Y'),
                'days' => $days + $days_aux,
                'interest' => $interest + $interest_rest,
                'capital' => $capital,
                'payment' => $payment + $interest_rest,
                'balance' => $balance,
                ]);
            }
            else{
                if($i == $this->loan_term){
                    array_push($plan, (object)[
                        'nro' => $i,
                        'date' => Carbon::parse($date_fin)->format('d-m-Y'),
                        'days' => $days,
                        'interest' => $interest,
                        'capital' => $capital+$balance,
                        'payment' => $payment+$balance,
                        'balance' => 0,
                        ]);
                }
                else{
                    array_push($plan, (object)[
                        'nro' => $i,
                        'date' => Carbon::parse($date_fin)->format('d-m-Y'),
                        'days' => $days,
                        'interest' => $interest,
                        'capital' => $capital,
                        'payment' => $payment,
                        'balance' => $balance,
                        ]);
                }
            }
            $date_ini = Carbon::parse($date_fin)->startOfMonth()->addMonth();
        }
        return $plan;
   }
   public function validate_loan_affiliate_edit($amount_approved,$loan_term){   
    $procedure_modality = ProcedureModality::findOrFail($this->procedure_modality_id);
        if($amount_approved <= $procedure_modality->loan_modality_parameter->maximum_amount_modality && $amount_approved >= $procedure_modality->loan_modality_parameter->minimum_amount_modality){
            if($loan_term <= $procedure_modality->loan_modality_parameter->maximum_term_modality && $loan_term >= $procedure_modality->loan_modality_parameter->minimum_term_modality){
                $quota_estimated = CalculatorController::quota_calculator($procedure_modality,$loan_term,$amount_approved);
                $new_indebtedness_calculated = Util::round2(($quota_estimated/$this->liquid_qualification_calculated)*100);
                $validate = false;
                if($new_indebtedness_calculated <= $procedure_modality->loan_modality_parameter->debt_index){                      
                    if((count($this->guarantors)>0) || (count($this->lenders) > 1)){
                        if($new_indebtedness_calculated <= Util::round2($this->indebtedness_calculated_previous)){ 
                            foreach ($this->lenders  as $lender) {    
                                $quota_estimated_lender = $quota_estimated/count($this->lenders);
                                $new_indebtedness_lender = Util::round2($quota_estimated_lender/(float)$lender->pivot->liquid_qualification_calculated*100);                    
                                if($new_indebtedness_lender <= (float)$lender->pivot->indebtedness_calculated_previous){
                                    $validate = true;               
                                }else {
                                    $validate = false;
                                }
                            }                       
                            if(count($this->guarantors) > 0){   
                                foreach ($this->guarantors  as $guarantor) {    
                                    $affiliate = Affiliate::find($guarantor->pivot->affiliate_id);
                                    $active_guarantees = $affiliate->active_guarantees();$sum_quota = 0;
                                    foreach($active_guarantees as $res)
                                        $sum_quota += ($res->estimated_quota * $res->pivot->payment_percentage)/100; // descuento en caso de tener garantias activas
                                        $active_guarantees_sismu = $affiliate->active_guarantees_sismu();
                                    foreach($active_guarantees_sismu as $res)
                                        $sum_quota += $res->PresCuotaMensual / $res->quantity_guarantors; // descuento en caso de tener garantias activas del sismu*/
                                        $quota_estimated_guarantor = $quota_estimated/count($this->guarantors);
                                        $new_indebtedness_calculated_guarantor = Util::round2((($quota_estimated_guarantor + $sum_quota - $guarantor->pivot->quota_treat)/$guarantor->pivot->liquid_qualification_calculated) * 100);
                                    if($new_indebtedness_calculated_guarantor <= (float)$guarantor->pivot->indebtedness_calculated_previous){
                                        $validate = true;                                                                            
                                    }else {
                                        $validate = false; 
                                    }                                 
                                }                                   
                            }  
                            if($validate){
                                $validate = true;
                            }else {
                                $message['message'] = 'El índice de endeudamiento del titular o garante no debe ser superior a la evaluación realizada en la creación del tramite';
                            }     
                            return $validate;                                                                                                
                        }else {
                        $message['message'] = 'El índice de endeudamiento no debe ser superior a '.$this->indebtedness_calculated_previous.'%, evaluación realizada en la creación del tramite';                        
                        } 
                    }else{ 
                        if(count($this->lenders) == 1){
                            foreach ($this->lenders  as $lender) {     
                                $validate = true;                       
                            }    
                            return $validate;                      
                        }
                    }
                }else {
                $message['message'] = 'El índice de endeudamiento no debe ser superior a '.$procedure_modality->loan_modality_parameter->debt_index.'%';
                }                       
            }else{
            $message['message'] = 'No se pudo realizar la edición. El plazo en meses solicitado no corresponde a la modalidad '.$procedure_modality->name;
            } 
        }else{
        $message['message'] = 'No se pudo realizar la edición. El monto solicitado no corresponde a la modalidad '.$procedure_modality->name;
        } 
    return $message;            
    }

    public function verify_regular_payments(){
        $date = Carbon::parse($this->disbursement_date)->format('Y-m-d');
        $regular = true;
        //nuevo procedimiento
        foreach($this->paymentsKardex as $payment)
        {
            if($payment->estimated_quota != $this->loan_plan->where('quota_number', $payment->quota_number)->first()->total_amount || $payment->estimated_date != $this->loan_plan->where('quota_number', $payment->quota_number)->first()->estimated_date)
            {
                $regular = false;
                break;
            }
        }
        /*$extra_amount = 0;
        if(Carbon::parse($date)->format('d') <= LoanGlobalParameter::first()->offset_interest_day)
            $date = Carbon::parse($date)->endOfMonth()->format('Y-m-d');
        else{
            $extra_days = Carbon::parse($date)->endOfMonth()->format('d') - Carbon::parse($this->disbursement_date)->format('d');
            $date = Carbon::parse($date)->startOfMonth()->addMonth()->endOfMonth()->format('Y-m-d');
            $extra_amount = number_format(((($this->interest->annual_interest/100)/360)*$extra_days*$this->amount_approved) , 2);
        }
        foreach($this->paymentsKardex as $payment)
        {
            if($date != Carbon::parse($payment->estimated_date)->format('Y-m-d') || $payment->estimated_quota != round(($this->estimated_quota + $extra_amount),2)){
                $regular = false;
                break;
            }
            else
                $date = Carbon::parse($date)->startOfMonth()->addMonth()->endOfMonth()->format('Y-m-d');
            $extra_amount = 0;
        }*/
        return $regular;
    }

    public function get_amount_payment($loan_payment_date, $liquidate, $type){
        $quota = 0;$penal_interest = 0;$suggested_amount = 0;
        $estimated_date = null;
       if($liquidate){
            if(!$this->last_payment_validated)
            {
                $days = Carbon::parse(Carbon::parse($this->disbursement_date)->format('d-m-Y'))->diffInDays(Carbon::parse($loan_payment_date));
                if(Carbon::parse($this->disbursement_date)->day <= LoanGlobalParameter::first()->offset_interest_day)
                    $estimated_date = Carbon::parse($this->disbursement_date)->endOfMonth()->format('d-m-Y');
                else
                    $estimated_date = Carbon::parse($this->disbursement_date)->startOfMonth()->addMonth()->endOfMonth()->format('d-m-Y');
            }
            else{
                $days = Carbon::parse(Carbon::parse($this->last_payment_validated->estimated_date)->format('d-m-Y'))->diffInDays($loan_payment_date);
            }
                $interest_by_days = LoanPayment::interest_by_days($days, $this->interest->annual_interest, $this->balance);
                if($days > LoanGlobalParameter::first()->days_current_interest + LoanGlobalParameter::first()->grace_period)
                {
                    if($estimated_date != null && $loan_payment_date > $estimated_date)
                        $penal_interest = LoanPayment::interest_by_days($days - LoanGlobalParameter::first()->days_current_interest, $this->interest->penal_interest, $this->balance);
                }
                    $suggested_amount = $this->balance + $interest_by_days + $penal_interest;
       }
       else{
            if($type == "T"){
                    if(!$this->last_payment_validated){
                            $date_ini = CarbonImmutable::parse($this->disbursement_date);
                            if($date_ini->day <= LoanGlobalParameter::latest()->first()->offset_interest_day){
                                //$date_pay = Carbon::parse($this->disbursement_date)->endOfMonth()->format('d-m-Y');
                                $extra_days = 0;
                                $suggested_amount = $this->estimated_quota;
                            }
                            else{
                                $extra_days = Carbon::parse(Carbon::parse($this->disbursement_date)->format('d-m-Y'))->diffInDays(Carbon::parse($this->disbursement_date)->endOfMonth()->format('d-m-Y'));
                                $date_pay = Carbon::parse($this->disbursement_date)->startOfMontH()->addMonth()->endOfMonth();
                                $loan_payment_date = Carbon::parse($loan_payment_date);
                                if($loan_payment_date->lt($date_pay))
                                    $suggested_amount = LoanPayment::interest_by_days($extra_days, $this->interest->annual_interest, $this->balance) + $this->estimated_quota;
                                else
                                    $suggested_amount = $this->estimated_quota;
                            }
                    }
                    else{
                        if($this->verify_regular_payments() && ($this->paymentsKardex->count()+1) == $this->loan_term){
                            $days = Carbon::parse($loan_payment_date)->format('d');
                            $interest_by_days = LoanPayment::interest_by_days($days, $this->interest->annual_interest, $this->balance);
                            $suggested_amount = $this->estimated_quota + $this->balance;
                        }
                        else{
                            if($this->balance > $this->estimated_quota)
                                $suggested_amount = $this->estimated_quota;
                            else{
                                $days = Carbon::parse($this->last_payment_validated->estimated_date)->diffInDays($loan_payment_date);
                                $interest_by_days = LoanPayment::interest_by_days($days, $this->interest->annual_interest, $this->balance);
                                $suggested_amount = $this->balance + $interest_by_days;
                            }
                        }
                    }
                }
                else
                    $suggested_amount = $this->guarantors->first()->pivot->quota_treat;
        }
        return  round($suggested_amount,2);
    }

    public function getBorrowerAttribute(){
        $data = collect([]);
        foreach($this->lenders as $lender)
        {
            $borrower = new Affiliate();
            if($lender->pivot->type == 'affiliates'){
                $borrower = $lender;
                $borrower->city_identity_card = $lender->city_identity_card;
                $borrower->type_initials = "T-".$lender->initials;
            }
            if($lender->pivot->type == 'spouses'){
                $borrower = $lender->spouse;
                $borrower->city_identity_card = $lender->spouse->city_identity_card;
                $borrower->type_initials = "T-".$lender->spouse->initials;
            }
            $borrower->account_number = $lender->account_number;
            $borrower->financial_entity = $lender->financial_entity;
            $borrower->type = $lender->pivot->type;
            $borrower->quota = $lender->pivot->quota_treat;
            $borrower->percentage_quota = $lender->pivot->percentage_quota;
            $borrower->state = $lender->affiliate_state;
            $borrower->address = $lender->address;
            if($this->affiliate_id == $lender->pivot->affiliate_id)
                $borrower->disbursable = true;
            else
                $borrower->disbursable = false;
                $data->push($borrower);
        }
        return $data;
    }

    public function getBorrowerGuarantorsAttribute(){
        $data = collect([]);
        foreach($this->guarantors as $guarantor){
            $titular_guarantor = new Affiliate();
            if($guarantor->pivot->type == "affiliates"){
                $titular_guarantor = $guarantor;
                $titular_guarantor->city_identity_card = $guarantor->city_identity_card;
                $titular_guarantor->type_initials = "G-".$guarantor->initials;
            }
            if($guarantor->pivot->type == "spouses"){
                $titular_guarantor = $guarantor->spouse;
                $titular_guarantor->city_identity_card = $guarantor->spouse->city_identity_card;
                $titular_guarantor->type_initials = "G-".$guarantor->spouse->initials;
            }
            $titular_guarantor->account_number = $guarantor->account_number;
            $titular_guarantor->financial_entity = $guarantor->financial_entity;
            $titular_guarantor->type = $guarantor->pivot->type;
            $titular_guarantor->quota = $guarantor->pivot->quota_treat;
            $titular_guarantor->percentage_quota = $guarantor->pivot->percentage_quota;
            $titular_guarantor->state = $guarantor->affiliate_state;
            $titular_guarantor->address = $guarantor->address;
            $data->push($titular_guarantor);
        }
        return $data;
    }
    public function getGuarantors(){
        $loans_guarantors = DB::table('view_loan_borrower_guarantors')
        ->where('guarantor_loan',true)
        ->where('id_loan',$this->id)
        ->select('*')
        ->get();
        return $loans_guarantors;
    }

    public function getBorrowers(){
        $loans_borrowers = DB::table('view_loan_borrower_guarantors')
        ->where('guarantor_loan',false)
        ->where('id_loan',$this->id)
        ->select('*')
        ->get();
        return $loans_borrowers;
    }
}