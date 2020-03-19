<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ProcedureModality;

class LoanModalityParameter extends Model
{
    public $timestamps = true;
    public $fillable = [
        'procedure_modality_id',
        'debt_index',
        'quantity_ballots',
        'guarantors',
        'min_guarantor_category',
        'max_guarantor_category',
        'personal_reference'
    ];

    public function getDecimalIndexAttribute()
    {
        return $this->debt_index / (100);
    }

    public function procedure_modality()
    {
        return $this->belongsTo(ProcedureModality::class);
    }
}
