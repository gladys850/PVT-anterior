<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProcedureType extends Model
{
    use Traits\EloquentGetTableNameTrait;

    protected $fillable = [
        'module_id',
        'name',
        'second_name'
      ];
    public function procedure_modalities()
    {
        return $this->hasMany(ProcedureModality::class);
    }

    public function interval()
    {
        return $this->hasOne(LoanInterval::class);
    }

    public function destinies()
    {
        return $this->hasMany(LoanDestiny::class);
    }

    public function workflow()
    {
        return $this->belongsToMany(Role::class, 'role_sequences')->withPivot('next_role_id')->orderBy('sequence_number')->orderBy('name');
    }
}