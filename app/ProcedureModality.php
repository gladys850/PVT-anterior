<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProcedureModality extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'procedure_type_id',
        'name',
        'shortened',
        'is_valid'
    ];

}