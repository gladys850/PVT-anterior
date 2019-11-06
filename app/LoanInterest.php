<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanInterest extends Model
{
    public function getAnnualInterestDecimalAttribute()
    {
        return $this->annual_interest / 100;
    }

    public function getPenalInterestDecimalAttribute()
    {
        return $this->penal_interest / 100;
    }
}
