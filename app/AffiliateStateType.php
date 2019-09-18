<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AffiliateStateType extends Model
{
    protected $fillable = [
        'id',
        'name'	
    ];
    public function affiliate_state()
    {
        return $this->hasMany(AffiliateState::class);
    }
}


