<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProcedureDocument extends Model
{
    public $timestamps = false;

    protected $fillable = ['name'];
}
