<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AffiliateState extends Model
{
    protected $fillable = [
        'name',
        'affiliate_state_type_id'
    ];
    public function affiliates()
    {
        return $this->hasMany(Affiliate::class);
    }
    public function affiliate_state_type()
    {
        return $this->belongsTo(AffiliateStateType::class);
    }
}
