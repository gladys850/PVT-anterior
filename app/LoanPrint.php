<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanPrint extends Model
{
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['procedure_type_id','role_id','print'];
}
