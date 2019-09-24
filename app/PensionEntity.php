<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PensionEntity extends Model
{
    protected $table = 'pension_entities';

	protected $fillable = [

		'type',
		'name'
	];
}
