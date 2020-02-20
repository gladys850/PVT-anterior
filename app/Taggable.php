<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Taggable extends Model
{
    public $timestamps = true;
    protected $primaryKey = null;
    public $incrementing = false;
    protected $fillable = ['tag_id', 'user_id', 'taggable_id', 'taggable_type', 'date'];

    public function taggable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
