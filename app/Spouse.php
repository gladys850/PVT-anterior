<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laratrust\Traits\LaratrustUserTrait;

class Spouse extends Model
{
    use Traits\EloquentGetTableNameTrait;
    protected $fillable = [
        'user_id',
        'affiliate_id',
        'city_identity_card_id',
        'identity_card',
        'registration',
        'last_name',
        'mothers_last_name',
        'first_name',
        'second_name',
        'surname_husband',
        'civil_status',
        'birth_date',
        'date_death',
        'reason_death',
        'city_birth_id'
    ];
    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }

}
