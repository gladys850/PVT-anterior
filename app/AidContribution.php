<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Facades\DB;
use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;
use Carbon;
use Util;

class AidContribution extends Model
{
    use SoftDeletes;
    use Traits\EloquentGetTableNameTrait;
    public $guarded = ['id'];
    protected $fillable = [
        'user_id',
        'affiliate_id',
        'month_year',
        'type',
        'quotable',
        'rent',
        'dignity_rent',
        'interest',
        'total',
        'affiliate_contribution',
        'mortuary_aid',
        'valid'
    ];
    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function user(){
        return $this->hasOne(User::class,'id','id');
    }

      // add records
      public function records()
      {
        return $this->morphMany(Record::class, 'recordable');
      }
}
