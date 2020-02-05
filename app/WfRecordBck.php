<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WfRecordBck extends Model
{
    protected $table = 'wf_records_bck';
    public $timestamps = true;
    public $guarded = ['id'];
    protected $fillable = ['user_id', 'wf_state_id', 'eco_com_id', 'ret_fun_id', 'date', 'record_type_id', 'message'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
