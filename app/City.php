<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
  public $timestamps = false;
  public $fillable = ['name', 'first_shortened', 'second_shortened', 'third_shortened', 'latitude', 'longitude'];

  public function getLatitudeAttribute($value)
  {
    return floatval($value);
  }

  public function getLongitudeAttribute($value)
  {
    return floatval($value);
  }
  public function users()
  {
    return $this->hasMany(User::class);
  }
  public function affiliates_with_identity_cards()
	{
		return $this->hasMany(Affiliate::class,'city_identity_card_id','id');
  }
  public function loans()
	{
		return $this->hasMany(Loan::class);
  }
  
}
