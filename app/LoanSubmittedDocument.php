<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanSubmittedDocument extends Model
{
    public $fillable = ['loan_id','procedure_document_id','comment','is_valid'];

}
