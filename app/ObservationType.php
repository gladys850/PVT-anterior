<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ObservationType extends Model
{
    public $timestamps = false;
    public $guarded = ['id'];
    protected $fillable = ['name', 'module_id', 'description', 'type', 'shortened'];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function observations()
    {
        return $this->hasMany(Observation::class);
    }
}