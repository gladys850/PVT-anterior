<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanRequest extends Model
{   public $timestamps = true;
    public $incrementing = false;
    protected $primaryKey = ['hash'];
    protected $casts = ['request' => 'array'];
    public $fillable=[
        'affiliate_id',
        'hash',
        'request',
    ];

}
