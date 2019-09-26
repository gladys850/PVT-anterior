<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    public $timestamps = true;
    public $guarded = ['id'];
    protected $fillable = ['user_id', 'record_type_id', 'recordable_id', 'recordable_type', 'action'];

    public function getActionAttribute()
    {
        $action = "[{$this->record_type->display_name}] El usuario {$this->user->username} {$this->attributes['action']}";
        if ($this->recordable instanceof \App\Affiliate) {
            $affiliate = $this->recordable;
            $action .= " del afiliado $affiliate->full_name";
        }
        unset($this['record_type'], $this['user'], $this['recordable']);
        return $action;
    }

    public function recordable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function record_type()
    {
        return $this->belongsTo(RecordType::class);
    }
}
