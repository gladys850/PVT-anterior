<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanSubmittedDocument extends Model
{
    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
    public function procedure_document()
    {
        return $this->belongsTo(ProcedureDocument::class);
    }
}
