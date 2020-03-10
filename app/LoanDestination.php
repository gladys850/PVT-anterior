<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanDestination extends Model
{
    public $timestamps = false;
    public $fillable = ['procedure_type_id','name', 'description'];
}
