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
    public function procedure_documents()
    {
        return $this->belongsToMany(ProcedureDocument::class, 'procedure_requirements');
    }
    public function procedure_type()
    {
        return $this->belongsTo(ProcedureType::class);
    }

    
}