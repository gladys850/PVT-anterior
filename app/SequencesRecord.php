<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SequencesRecord extends Model
{
    public $timestamps = true;
    public $guarded = ['id'];
    protected $fillable = ['user_id', 'wf_state_id', 'message'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
