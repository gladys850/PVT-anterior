<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanSubmittedDocument extends Model
{
    protected $fillable = ['loan_id','procedure_document_id','reception_date','comment','is_valid'];

}
