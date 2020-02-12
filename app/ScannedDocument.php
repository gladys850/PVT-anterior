<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScannedDocument extends Model
{
    protected $table = 'affiliate_scanned_documents';

    public function procedure_document()
    {
        return $this->belongsTo('Muserpol\Models\ProcedureDocument');
    } 
}
