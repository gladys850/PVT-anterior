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
}
