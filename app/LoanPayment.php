<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanPayment extends Model
{
    public $timestamps = true;

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}