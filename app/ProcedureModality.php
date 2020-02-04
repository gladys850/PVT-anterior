<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ProcedureDocument;

class ProcedureModality extends Model
{
    use Traits\EloquentGetTableNameTrait;

    public $timestamps = false;
    // protected $hidden = ['pivot'];

    protected $fillable = [
        'procedure_type_id',
        'name',
        'shortened',
        'is_valid'
    ];

    public function required_documents()
    {
        return $this->belongsToMany(ProcedureDocument::class, 'procedure_requirements')->withPivot(['number'])->wherePivot('number', '!=', 0);
    }

    public function optional_documents()
    {
        return $this->belongsToMany(ProcedureDocument::class, 'procedure_requirements')->withPivot(['number'])->wherePivot('number', 0);
    }

    public function getRequirementsListAttribute()
    {
        return [
            'required' => $this->required_documents->sortBy('pivot.number')->groupBy(['pivot.number'])->each(function($list) {
                $list->each(function($element) {
                    unset($element['pivot']);
                });
            })->values(),
            'optional' => $this->required_documents->each(function($element) {
                unset($element['pivot']);
            })
        ];
    }

    public function procedure_type()
    {
        return $this->belongsTo(ProcedureType::class);
    }
}