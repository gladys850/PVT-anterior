<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon;
use Util;

class Loan extends Model
{
    use Traits\EloquentGetTableNameTrait;

    protected $appends = ['balance', 'estimated_quota', 'defaulted'];
    public $timestamps = true;
    // protected $hidden = ['pivot'];
    public $guarded = ['id'];
    public $fillable = [
        'code',
        'disbursable_id',
        'disbursable_type',
		'procedure_modality_id',
		'amount_disbursement',
		'parent_loan_id',
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
        return $this->belongsToMany(Affiliate::class, 'loan_affiliates')->withPivot(['payment_percentage'])->whereGuarantor(true);
    }

    public function lenders()
    {
        return $this->belongsToMany(Affiliate::class, 'loan_affiliates')->withPivot(['payment_percentage'])->whereGuarantor(false);
    }
    public function loan_affiliates()
    {
        return $this->belongsToMany(Affiliate::class, 'loan_affiliates');
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
        $quota->next_balance = Util::round($quota->balance - $quota->capital_payment);
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

            $plan[] = [
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
    // metodos para la calculadora
    // suma de numeros, la suma del liquido pagable, bonos
    public function add_numbers($amounts)
    {
        if(count($amounts)>0){
            $add_result=0;
            foreach($amounts as $data){ 
                $add_result+=$data; 
            }
            return $add_result;
        }
        return 0;
    }
    // funcion para sacar el liquido para calificacion
    public function liquid_qualification($ballots,$bonuses){
        $average_ballots = $this->average_ballots($ballots);
        $total_bonuses = $this->add_numbers($bonuses);
        return ($average_ballots-$total_bonuses);
    }
    public function average_ballots($ballots){
        if(count($ballots)>0){
           return ($this->add_numbers($ballots))/count($ballots);
        }
        return 0;
    }
    //funcion para sacar la cuota estimada con la calculadora
    public function quota_calculator($ballots,$bonuses,$modality_id,$months_term,$amount_request){
        if($modality_id !=null){
            $loan_intervals= (ProcedureModality::find($modality_id))->procedure_type->loan_intervals;
            $interest_rate=(LoanInterest::where('procedure_modality_id', '=',$modality_id)->latest()->first())->monthly_current_interest; 
            if($amount_request>0 && $months_term ==null){
                return ((($interest_rate)/(1-(1/pow((1+$interest_rate),$loan_intervals->maximum_term))))*$amount_request);   
            }if($amount_request ==null && $months_term>0){
                    $maximum_qualified_amount = $this->maximum_amount($modality_id,$ballots,$bonuses,$months_term);
                    return ((($interest_rate)/(1-(1/pow((1+$interest_rate),$months_term))))*$maximum_qualified_amount);   
                }if ($months_term>0 && $amount_request>0){
                        return ((($interest_rate)/(1-(1/pow((1+$interest_rate),$months_term))))*$amount_request);   
                    }else{
                        if(count($ballots)>0){
                            $maximum_qualified_amount = $this->maximum_amount($modality_id,$ballots,$bonuses,$months_term);
                            $estimated_quota=((($interest_rate)/(1-(1/pow((1+$interest_rate),$loan_intervals->maximum_term))))*$maximum_qualified_amount);
                            return $estimated_quota;
                        }else{
                            return 0;
                        }
                    }
        }else{
             return 0;
        } 
    }
    // monto maximo
    public function maximum_amount($modality_id,$ballots,$bonuses,$months_term){ 
        if($modality_id!=null){
            $interest_rate=(LoanInterest::where('procedure_modality_id', '=',$modality_id)->latest()->first())->monthly_current_interest;
            $loan_intervals= (ProcedureModality::find($modality_id))->procedure_type->loan_intervals;
            $debt_index=(LoanModalityParameter::where('procedure_modality_id', '=',$modality_id)->latest()->first())->decimal_index;
            $liquid_qualification = $this->liquid_qualification($ballots,$bonuses);
            if($months_term ==null){
                $months_term = $loan_intervals->maximum_term;
            }
            $maximum_qualified_amount = (1-(1/pow((1+$interest_rate),$months_term)))*($debt_index*$liquid_qualification)/$interest_rate;
            $maximum_qualified_amount=floor(round(floor($maximum_qualified_amount))/1000)*1000;
            if ($maximum_qualified_amount > ($loan_intervals->maximum_amount)){
                $maximum_qualified_amount = $loan_intervals->maximum_amount;
            } else {
                $maximum_qualified_amount = $maximum_qualified_amount; 
            }
            return $maximum_qualified_amount;
        }else{
            return 0;
        }   
    }
    public function calculator($ballots,$bonuses,$modality_id,$months_term,$amount_request)
    {
        //$ballots = [2000];$bonuses = [24,24,1000,300]; $modality_id=47;$months_term=null;$amount_request=null;
        $payable_liquid_average= $this->average_ballots($ballots);
        $total_bonuses=$this->add_numbers($bonuses);
        $liquid_qualification=$this->liquid_qualification($ballots,$bonuses);
        $quota_calculation = $this->quota_calculator($ballots,$bonuses,$modality_id, $months_term, $amount_request);
        $maximum_suggested_amount = $this->maximum_amount($modality_id,$ballots,$bonuses,$months_term);
        if($payable_liquid_average!=0){
            $index_calculated =$quota_calculation/(($this->average_ballots($ballots))-($this->add_numbers($bonuses)))*100 ;
            $debt_index=(LoanModalityParameter::where('procedure_modality_id', '=',$modality_id)->latest()->first())->debt_index;
            if($index_calculated > $debt_index){
                $major_index="El índice de Endeudamiento no debe ser mayor que el : " .$debt_index." %";
            }else{
                $major_index="El índice de Endeudamiento es menor que el : " .$debt_index."%";
            }

        }else{
            $index_calculated=0;
        }
        return[
                'promedio liquido pagable'=>$payable_liquid_average,
                'total bonos'=>$total_bonuses,
                'liquido para calificacion'=>$liquid_qualification,
                'calculo de cuota'=>$quota_calculation,
                'indice de endeudamiento'=>$index_calculated,
                'monto maximo sugerido'=>  $maximum_suggested_amount,
                'Nota'=>$major_index
             ];       
    }
    public function calculator_guarantor($ballots,$bonuses){
        $liquid_qualification = $this->liquid_qualification($ballots,$bonuses);
        return $liquid_qualification;
    }
    //obtener modalidad teniendo el tipo y el afiliado
    public function get_modality($modality_id, $affiliate_id){
        $affiliate = Affiliate::findOrFail($affiliate_id); $modality=null;
        $modality = ProcedureType::findOrFail($modality_id)->name;
        if ($affiliate->affiliate_state){
            $affiliate_state = $affiliate->affiliate_state->name;
            $affiliate_state_type = $affiliate->affiliate_state->affiliate_state_type->name;
        } 
        switch($modality){
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
            return response()->json($modality); 
            break;
            case 'Préstamo a corto plazo':
                if($affiliate_state_type == "Activo"){
                    if($affiliate->active_loans){
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
                            if($affiliate->active_loans){
                                $modality=ProcedureModality::whereShortened("PCP-R-SP-AFP")->first();
                                // refi afp pasivo
                            }else{
                                $modality=ProcedureModality::whereShortened("PCP-SP-AFP")->first();
                                //$sub_modality="Prestamo a corto plazo sector pasivo afp";
                            }
                              
                        }else{
                            if($affiliate->active_loans){
                                $modality=ProcedureModality::whereShortened("PCP-R-SP-SEN")->first();
                                // refi senasir pasivo
                            }else{
                                $modality=ProcedureModality::whereShortened("PCP-SP-SEN")->first();
                            }
                        }                       
                    }
                }
                return response()->json($modality); 
                break;
            case 'Préstamo a largo plazo':
                if($affiliate_state_type == "Activo")
                {
                    if($cpop){
                        if($affiliate->active_loans){
                            $modality=ProcedureModality::whereShortened("PLP-R-SA")->first();
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
                            if($affiliate->active_loans){
                                if($cpop){
                                    $modality=ProcedureModality::whereShortened("PLP-R-SP")->first();
                                    // Refi largo plazo pasivo 1 solo garante
                                }
                            }else{
                                $modality=ProcedureModality::whereShortened("PLP-GP-SP")->first();  
                            }
                        }
                }
                return response()->json($modality); 
                break;
            case 'Préstamo hipotecario':
                if($affiliate_state_type == "Activo")
                {
                    if($cpop){
                        $modality=ProcedureModality::whereShortened("PLP-GH-CPOP")->first();
                        //hipotecario CPOP
                    }else{
                        $modality=ProcedureModality::whereShortened("PLP-GH-SA")->first();
                    } 
                }
                return response()->json($modality); 
                break;
        } 
             
    }

}
