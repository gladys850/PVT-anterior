<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Carbon;
use Util;

class Loan extends Model
{
    use Traits\EloquentGetTableNameTrait;

    // protected $appends = ['balance', 'estimated_quota', 'defaulted'];
    public $timestamps = true;
    // protected $hidden = ['pivot'];
    public $guarded = ['id'];
    public $fillable = [
        'code',
        'disbursable_id',
        'disbursable_type',
		'procedure_modality_id',
		'parent_loan_id',
        'parent_reason',
        'request_date',
        'amount_requested',
        'city_id',
        'loan_interest_id',
        'loan_state_id',
        'amount_approved',
        'loan_term',
        'disbursement_date',
        'disbursement_type_id',
        'modification_date',
        'account_number',
        'loan_destiny_id',
        'personal_reference_id',
        'role_id'
    ];

    function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->request_date = Carbon::now();
        $state = LoanState::whereName('En Proceso')->first();
        if ($state) {
            $this->loan_state_id = $state->id;
        }
        $latest_loan = DB::table('loans')->orderBy('created_at', 'desc')->limit(1)->first();
        if (!$latest_loan) $latest_loan = (object)['id' => 0];
        $this->code = implode(['PTMO', str_pad($latest_loan->id + 1, 6, '0', STR_PAD_LEFT), '-', Carbon::now()->year]);
    }

    public function setProcedureModalityIdAttribute($id)
    {
        $this->attributes['procedure_modality_id'] = $id;
        $this->attributes['loan_interest_id'] = $this->modality->current_interest->id;
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

    public function submitted_documents()
    {
        return $this->belongsToMany(ProcedureDocument::class, 'loan_submitted_documents', 'loan_id')->withPivot('reception_date', 'comment', 'is_valid');
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
    //desembolso --> afiliado, esposa
    public function disbursable()
    {
        return $this->morphTo();
    }

    public function destiny()
    {
        return $this->belongsTo(LoanDestiny::class, 'loan_destiny_id', 'id');
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

    public function last_payment()
    {
        return $this->payments()->latest()->first();
    }

    public static function get_percentage($dato)
    {
        if(count($dato)>0){
            return Util::round(1/count($dato)*100);
        }  
    }

    public function last_quota()
    {
        $latest_quota = $this->last_payment();
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

    public function getNextPaymentAttribute()
    {
        $quota = new LoanPayment();
        $next_payment = LoanPayment::quota_date($this);
        $quota->estimated_date = $next_payment->date;
        $quota->quota_number = $next_payment->quota;
        $interest = $this->interest;
        $quota->interest_days = LoanPayment::days_interest($this, $quota->estimated_date);
        // Calcular intereses
        $quota->balance = $this->balance;
        $quota->interest_payment = Util::round($quota->balance * $interest->daily_current_interest * $quota->interest_days->current);
        $quota->penal_payment = Util::round($quota->balance * $interest->daily_penal_interest * $quota->interest_days->penal);
        $quota->accumulated_interest = Util::round($quota->balance * $interest->daily_current_interest * $quota->interest_days->accumulated);
        // Calcular amortización de capital
        $total_interests = $quota->interest_payment + $quota->penal_payment + $quota->accumulated_interest;
        if (($quota->balance + $total_interests) > $this->estimated_quota) {
            $quota->capital_payment = $this->estimated_quota - $total_interests;
        } else {
            $quota->capital_payment = $quota->balance;
        }
        // Calcular monto total de la cuota
        $quota->estimated_quota = $quota->capital_payment + $total_interests;
        $quota->next_balance = Util::round($quota->balance - $quota->capital_payment);
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
        $modality=null;
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
                        $modality = ProcedureModality::whereShortened("PCP-R-SA")->first();//Refinanciamiento corto plazo activo
                    }else{
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
                                $modality=ProcedureModality::whereShortened("PCP-R-SP-AFP")->first();
                                // refi afp pasivo
                            }else{
                                $modality=ProcedureModality::whereShortened("PCP-SP-AFP")->first();
                                //$sub_modality="Prestamo a corto plazo sector pasivo afp";
                            }
                              
                        }else{
                            if($affiliate->active_loans()){
                                $modality=ProcedureModality::whereShortened("PCP-R-SP-SEN")->first();
                                // refi senasir pasivo
                            }else{
                                $modality=ProcedureModality::whereShortened("PCP-SP-SEN")->first();
                            }
                        }                       
                    }
                }
                break;
            case 'Préstamo a largo plazo':
                if($affiliate_state_type == "Activo")
                {
                    if($affiliate->cpop){
                        if($affiliate->active_loans()){
                            $modality=ProcedureModality::whereShortened("PLP-R-SA-CPOP")->first();
                            // Refi largo plazo activo 1 solo garante
                        }else{
                            $modality=ProcedureModality::whereShortened("PLP-CPOP")->first();
                        }
                    }else{
                        $modality=ProcedureModality::whereShortened("PLP-GP-SAYADM")->first();
                    }
                }
                else{
                        if($affiliate_state_type == "Pasivo"){
                            if($affiliate->active_loans()){
                                if($affiliate->cpop){
                                    $modality=ProcedureModality::whereShortened("PLP-R-SP-CPOP")->first();
                                    // Refi largo plazo pasivo 1 solo garante
                                }
                            }else{
                                $modality=ProcedureModality::whereShortened("PLP-GP-SP")->first();  
                            }
                        }
                }
                break;
            case 'Préstamo hipotecario':
                if($affiliate_state_type == "Activo")
                {
                    if($affiliate->cpop){
                        $modality=ProcedureModality::whereShortened("PLP-GH-CPOP")->first();
                        //hipotecario CPOP
                    }else{
                        $modality=ProcedureModality::whereShortened("PLP-GH-SA")->first();
                    } 
                }
                break;
            } 
        }
        if ($modality) $modality->loan_modality_parameter;
        return response()->json($modality);
             
    }
}
