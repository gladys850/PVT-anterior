<?php

namespace App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Util;

class LoanContributionAdjust extends Model
{
    public $timestamps = true;
    public $guarded = ['id'];
    protected $fillable = [
        'user_id',
        'loan_id',
        'affiliate_id',
        'adjustable_id',
        'adjustable_type',
        'type_affiliate',
        'amount',
        'type_adjust',
        'period_date',
        'description'
    ];
    public function Loan()
    {
        return $this->belongsTo(Loan::class);
    }
    public function adjustable()
    {
        return $this->morphTo();
    }
  
}
