<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    public $timestamps = true;
    public $guarded = ['id'];

    public function state()
    {
        return $this->belongsTo(LoanState::class);
    }

    public function guarantors()
    {
        return $this->belongsToMany(Affiliate::class, 'loan_guarantors');
    }

    public function defaulted()
    {
        return $this->penal_interest() > 0 ? true : false;
    }

    // Cálculo de días de interés penal
    public function penal_interest_days()
    {
        return rand(0, 3);
    }
}