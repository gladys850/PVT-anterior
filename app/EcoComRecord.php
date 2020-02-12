<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EcoComRecord extends Model
{
    public $timestamps = true;
    public $guarded = ['id'];
    protected $fillable = ['user_id', 'economic_complement_id', 'message'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
