<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanDestination extends Model
{
    use Traits\EloquentGetTableNameTrait;
    public $timestamps = true;
    public $fillable = ['procedure_type_id','name', 'description'];
}
