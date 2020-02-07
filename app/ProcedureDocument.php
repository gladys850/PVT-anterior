<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ProcedureModality;
use App\Loan;

class ProcedureDocument extends Model
{
    use Traits\EloquentGetTableNameTrait;

    public $timestamps = false;
    // protected $hidden = ['pivot'];
    protected $fillable = ['name', 'expire_date'];

    public function modality()
    {
        return $this->belongsToMany(ProcedureModality::class, 'procedure_requirements')->withPivot(['number']);
    }

    public function scanned_documents()
    {
        return $this->hasMany(ScannedDocument::class);
    }

    public function loans()
    {
        return $this->belongsToMany(Loan::class, 'loan_submitted_documents', 'procedure_document_id')->withPivot('reception_date', 'comment', 'is_valid')->orderBy('pivot_reception_date','desc');
    }
}