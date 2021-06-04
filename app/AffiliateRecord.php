<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AffiliateRecord extends Model
{
    use SoftDeletes;
    protected $dates = ['date'];
    public $timestamps = true;
    public $guarded = ['id'];
    protected $fillable = ['user_id', 'affiliate_id', 'affiliate_state_id', 'degree_id', 'unit_id', 'date', 'type_id', 'message', 'category_id', 'pension_entity_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
