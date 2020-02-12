<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PersonalReference extends Model
{
    public $timestamps = true;
    public $fillable = ['loan_id', 'full_name', 'address', 'cell_phone'];   
   
    public function loan()
    {
      return $this->belongsTo(Loan::class);
    }
}
