<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laratrust\Traits\LaratrustUserTrait;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use LaratrustUserTrait, Notifiable, SoftDeletes;
    use Traits\EloquentGetTableNameTrait;

    public $timestamps = true;
    public $guarded = ['id'];
    protected $dates = ['deleted_at'];

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = ['city_id', 'first_name', 'last_name', 'phone', 'username', 'password', 'status', 'position', 'gender', 'is_commission'];

    /**
    * The attributes that should be hidden for arrays.
    *
    * @var array
    */
    protected $hidden = ['password', 'remember_token'];

    /**
    * Get the identifier that will be stored in the subject claim of the JWT.
    *
    * @return mixed
    */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
    * Return a key value array, containing any custom claims to be added to the JWT.
    *
    * @return array
    */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions');
    }
}
