<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ProcedureModality;

class ProcedureDocument extends Model
{

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
}