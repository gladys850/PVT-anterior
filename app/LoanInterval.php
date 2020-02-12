<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanInterval extends Model
{
    public $timestamps = false;
    public $fillable = ['maximum_amount', 'minimum_amount','maximum_term','minimum_term','procedure_type_id'];

    public function procedure_type()
    {
        return $this->belongsTo(ProcedureType::class);
    }
    public function procedure_modalities()
    {
      return $this->hasMany(ProcedureModality::class,'procedure_type_id','procedure_type_id' )->with('loan_modality_parameter');
    } 
}
