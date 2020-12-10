<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AmortizationType extends Model
{
    public $timestamps = true;
    public $fillable = ['name'];
    public $guarded = ['id'];

}


