<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProcedureDocument extends Model
{
    public function procedure_requirements()
    {
        return $this->hasMany(ProcedureDocument::class);
    }

    public function scanned_documents()
    {
        return $this->hasMany(ScannedDocument::class);
    }
}
