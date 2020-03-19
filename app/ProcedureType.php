<?php

namespace App;

use Illuminate\Support\Facades\DB;
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

    public function getRolesAttribute()
    {
        $keys = [
            "role_id" => 0,
            "next_role_id" => 1
        ];
        $roles = DB::table('role_sequences')->where('procedure_type_id', $this->id)->select(collect($keys)->keys()->toArray())->get()->toArray();
        $roles = array_map(function($o) use ($keys) {
            return array_intersect_key((array)$o, $keys);
        }, $roles);
        $roles = collect($roles)->flatten()->unique()->values();
        foreach ($roles as $key => $role) {
            $roles[$key] = Role::find($role);
        }
        return $roles;
    }
}