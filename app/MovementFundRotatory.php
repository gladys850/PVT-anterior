<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovementFundRotatories extends Model
{
    use Traits\EloquentGetTableNameTrait;
    use SoftDeletes;
    public $timestamps = true;
    protected $table = 'movement_fund_rotatories';
    public $guarded = ['id'];
    public $fillable = [
          'loan_id',
          'date_check_delivery',
          'check_number',
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
  

  

 

}
