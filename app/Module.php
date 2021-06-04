<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use Traits\EloquentGetTableNameTrait;

    public $timestamps = false;
    public $guarded = ['id'];
    protected $fillable = ['name', 'display_name'];

    public function roles()
    {
        //return $this->hasMany(Role::class)->orderBy('sequence_number');
        return $this->hasMany(Role::class);
    }

    public function procedure_types()
    {
        return $this->hasMany(ProcedureType::class)->orderBy('name');
    }

    public function observation_types()
    {
        return $this->hasMany(ObservationType::class)->orderBy('module_id')->orderBy('shortened');
    }
}