<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = "addresses";
    public $guarded =  [];
    protected $fillable = ['city_address_id', 'zone', 'street', 'number_address', 'latitude', 'longitude'];

    protected $attributes = array(
        'city_address_id' => null,
        'zone' => null,
        'street' => null,
        'number_address' => null,
    );

    public function affiliate()
    {
    	return $this->belongsToMany(Affiliate::class);
    }
    public function city()
    {
        return $this->belongsTo(City::class,'city_address_id','id');
    }
}
