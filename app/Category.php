<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
		'from',
		'to',
		'name',
		'percentage'
	];
	public function affiliates()
	{
		return $this->hasMany(Affiliate::class);
	}

	
}