<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProcedureRequirement extends Model
{
    public function procedure_document()
    {
        return $this->belongsTo(ProcedureDocument::class);
    }
}
