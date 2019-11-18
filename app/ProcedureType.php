<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProcedureType extends Model
{
    protected $fillable = [
        'module_id',
        'name',
        'second_name'
      ];
}
