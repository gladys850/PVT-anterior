<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanInterest extends Model
{
    public $timestamps = false;
    protected $fillable = ['procedure_modality_id', 'annual_interest','penal_interest'];
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
    public function getAnnualInterestDecimalAttribute()
    {
        return $this->annual_interest / 100;
    }

    public function getPenalInterestDecimalAttribute()
    {
        return $this->penal_interest / 100;
    }
}
