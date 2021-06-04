<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RetFunRecord extends Model
{
    public $timestamps = true;
    public $guarded = ['id'];
    protected $fillable = ['user_id', 'ret_fun_id', 'message'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
