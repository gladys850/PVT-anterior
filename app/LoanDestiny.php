<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanDestiny extends Model
{
    use Traits\EloquentGetTableNameTrait;
    public $timestamps = true;
    public $fillable = ['name', 'description'];

    public function procedure_types()
    {
        return $this->belongsToMany(ProcedureType::class);
    }
}
