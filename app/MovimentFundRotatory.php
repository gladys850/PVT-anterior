<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovimentFundRotatory extends Model
{
    use Traits\EloquentGetTableNameTrait;
    use SoftDeletes;
    protected $table = 'moviment_fund_rotatories';
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
}
