<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laratrust\Traits\LaratrustUserTrait;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use LaratrustUserTrait, Notifiable;
    use Traits\EloquentGetTableNameTrait;

    public $timestamps = true;
    public $guarded = ['id'];

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = ['first_name', 'last_name', 'username', 'password', 'active', 'position'];

    /**
    * The attributes that should be hidden for arrays.
    *
    * @var array
    */
    protected $hidden = ['password'];

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

    public function affiliate_records()
    {
        return $this->hasMany(AffiliateRecord::class);
    }

    public function affiliate_records_pvt()
    {
        return $this->hasMany(AffiliateRecordPVT::class);
    }

    public function eco_com_records()
    {
        return $this->hasMany(EcoComRecord::class);
    }

    public function procedure_records()
    {
        return $this->hasMany(ProcedureRecord::class);
    }

    public function quota_aid_records()
    {
        return $this->hasMany(QuotaAidRecord::class);
    }

    public function ret_fun_records()
    {
        return $this->hasMany(RetFunRecord::class);
    }

    public function sequences_records()
    {
        return $this->hasMany(SequencesRecord::class);
    }

    public function wf_records()
    {
        return $this->hasMany(WfRecord::class);
    }

    public function wf_records_bck()
    {
        return $this->hasMany(WfRecordBck::class);
    }

    public function has_records()
    {
        if ($this->affiliate_records()->count() == 0 && $this->affiliate_records_pvt()->count() == 0 && $this->eco_com_records()->count() == 0 && $this->procedure_records()->count() == 0 && $this->quota_aid_records()->count() == 0 && $this->ret_fun_records()->count() == 0 && $this->sequences_records()->count() == 0 && $this->wf_records()->count() == 0 && $this->wf_records_bck()->count() == 0 && $this->has_records()->count() == 0) {
            return false;
        } else {
            return true;
        }
    }
}