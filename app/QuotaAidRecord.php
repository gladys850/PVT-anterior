<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuotaAidRecord extends Model
{
    public $timestamps = true;
    public $guarded = ['id'];
    protected $fillable = ['user_id', 'quota_aid_id', 'message'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
