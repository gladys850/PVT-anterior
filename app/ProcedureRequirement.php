<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProcedureRequirement extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'procedure_modality_id',
        'procedure_document_id',
        'number'
    ];
}
