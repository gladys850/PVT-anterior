<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    public $timestamps = false;
    public $guarded = ['id'];
    protected $fillable = ['name', 'description'];

    public function roles()
    {
        return $this->hasMany(Role::class);
    }
}