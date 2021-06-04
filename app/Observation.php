<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Observation extends Model
{
    use SoftDeletes;

    protected $table = 'observables';
    public $timestamps = true;
    protected $primaryKey = null;
    public $incrementing = false;
    protected $fillable = ['user_id', 'observation_type_id', 'observable_id', 'observable_type', 'message', 'date', 'enabled'];
    // protected $hidden = ['observable_id', 'observable_type'];

    public function observable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function type()
    {
        return $this->belongsTo(ObservationType::class);
    }

    public function records()
    {
        return $this->morphMany(Record::class, 'recordable')->latest('updated_at');
    }
}