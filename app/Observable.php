<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Observable extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $primaryKey = null;
    public $incrementing = false;
    protected $fillable = ['user_id', 'observation_type_id', 'observation_id', 'message', 'date', 'enabled'];

    public function observable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function observation_type()
    {
        return $this->belongsTo(ObservationType::class);
    }
}