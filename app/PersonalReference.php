<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PersonalReference extends Model
{
    use Traits\EloquentGetTableNameTrait;
    public $timestamps = true;
    public $fillable = [
        'city_identity_card_id',
        'identity_card',
        'last_name',
        'mothers_last_name',
        'first_name',
        'second_name',
        'surname_husband',
        'birth_date',
        'gender',
        'civil_status',
        'phone_number',
        'cell_phone_number'
    ];

    public function getIdentityCardExtAttribute()
    {
        return $this->identity_card . ' ' . $this->city_identity_card->first_shortened;
    }

    public function getFullNameAttribute()
    {
        return preg_replace('/[[:blank:]]+/', ' ', join(' ', [$this->first_name, $this->second_name, $this->last_name, $this->mothers_last_name]));
    }

    public function city_identity_card()
    {
        return $this->belongsTo(City::class, 'city_identity_card_id', 'id');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}
