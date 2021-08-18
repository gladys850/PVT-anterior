<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\CarbonImmutable;
use Carbon;
use Util;
use Illuminate\Support\Facades\DB;

class LoanPlanPayment extends Model
{
    use Traits\EloquentGetTableNameTrait;
    public $fillable = [
        'id',
        'loan_id',
        'quota_number',
        'estimated_date',
        'days',
        'capital',
        'interest',
        'total_amount',
        'balance',
    ];

    function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function loans()
    {
        return $this->hasOne(Loan::class);
    }
}