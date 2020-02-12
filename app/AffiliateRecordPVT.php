<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AffiliateRecordPVT extends Model
{
    protected $table = 'affiliate_records_pvt';
    public $timestamps = true;
    public $guarded = ['id'];
    protected $fillable = ['user_id', 'affiliate_id', 'message'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}