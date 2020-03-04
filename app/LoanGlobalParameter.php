<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanGlobalParameter extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'offset_ballot_day',
        'offset_interest_day',
        'livelihood_amount',
    ];
}
