<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon;
use App\Loan;
use App\Auth;
use App\MovementConcept;

use Illuminate\Support\Facades\DB;

class MovementFundRotatory extends Model
{
    use Traits\EloquentGetTableNameTrait;
    use SoftDeletes;
    public $timestamps = true;
    protected $table = 'movement_fund_rotatories';
    public $guarded = ['id'];
    public $fillable = [
          'loan_id',
          'date_check_delivery',
          //'check_number',
          'description',
          'entry_amount',
          'output_amount',
          'balance',
          'movement_concept_code',
          'movement_concept_id', 
          'user_id',
          'role_id',
      ];   
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
    public function movement_concept()
    {
        return $this->belongsTo(MovementConcept::class);
    }
    // add records
    public function records()
    {
      return $this->morphMany(Record::class, 'recordable');
    }
    public function is_last()
    {
        $is_last = false;
        $last_mov = MovementFundRotatory::orderBy('id')->get();
        $movement_last = $last_mov->last();
        if($movement_last->id == $this->id) $is_last = true;
        return $is_last;
    }
    public static function register_advance_fund($loan_id,$role_id,$moviment_concept_disbursement_id)
    {
        $loan = Loan::find($loan_id);
        $fund_rotatory = MovementFundRotatory::orderBy('id')->get()->last();    
        $abbreviated_supporting_document = MovementConcept::find($moviment_concept_disbursement_id)->abbreviated_supporting_document;
        $movement_concept_code = MovementFundRotatory::where('movement_concept_id',$moviment_concept_disbursement_id)->withTrashed()->count()+1;
        $name_affiliate = DB::table('view_loan_borrower')
        ->where("view_loan_borrower.id_loan", "=", $loan_id)
        ->select('full_name_borrower as full_name_borrower')
        ->get();
        $FundRotatoryOutput = new MovementFundRotatory;
        $FundRotatoryOutput->user_id = auth()->id();
        $FundRotatoryOutput->loan_id = $loan_id;
        $FundRotatoryOutput->movement_concept_code =$abbreviated_supporting_document."-".$movement_concept_code.'/'.Carbon::now()->year;
        $FundRotatoryOutput->description = $name_affiliate[0]->full_name_borrower;
        $FundRotatoryOutput->movement_concept_id = $moviment_concept_disbursement_id;
        $FundRotatoryOutput->role_id = $role_id;
        $FundRotatoryOutput->output_amount = $loan->amount_approved;
        $FundRotatoryOutput->balance = $fund_rotatory->balance - $loan->amount_approved;
        $FundRotatoryOutput = MovementFundRotatory::create($FundRotatoryOutput->toArray());
        return $FundRotatoryOutput;
    }
}
