<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanModalityParameter extends Model
{
    public $timestamps = true;
    public $fillable = ['procedure_modality_id','debt_index','quantity_ballots'];

}
