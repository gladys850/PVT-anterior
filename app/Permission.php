<?php

namespace App;

use Laratrust\Models\LaratrustPermission;

class Permission extends LaratrustPermission
{
  public $timestamps = true;
  public $guarded = ['id'];
  protected $fillable = ['name', 'display_name', 'description'];

  public function role()
  {
    return $this->hasMany(Role::class, 'role_permissions');
  }
}
