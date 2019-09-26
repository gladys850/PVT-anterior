<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
  public $fillable = ['name', 'first_shortened', 'second_shortened', 'third_shortened'];
  public function users()
  {
    return $this->hasMany(User::class);
  }
  public function affiliates_with_identity_cards()
	{
		return $this->hasMany(Affiliate::class,'city_identity_card_id','id');
	}
}
