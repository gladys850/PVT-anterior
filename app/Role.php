<?php

namespace App;

use Laratrust\Models\LaratrustRole;

class Role extends LaratrustRole
{
    public $timestamps = true;
    protected $hidden = ['pivot'];
    public $guarded = ['id'];
    protected $fillable = ['module_id', 'name', 'display_name'];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }
}