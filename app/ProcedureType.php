<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProcedureType extends Model
{
    protected $fillable = [
        'module_id',
        'name',
        'second_name'
      ];
    public function procedure_modalities()
    {
      return $this->belongsTo(ProcedureModality::class,'id','procedure_type_id' );
    } 
    public function loan_interval()
    {
        return $this->hasOne(LoanInterval::class);
    } 
}
