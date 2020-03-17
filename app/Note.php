<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    public $timestamps = true;
    public $guarded = ['id'];
    protected $hidden = ['user_id'];
    public $fillable = ['annotable_id', 'annotable_type', 'message', 'date'];

    public function annotable()
    {
        return $this->morphTo();
    }
}
