<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovementConcept extends Model
{
    protected $table = 'movement_concepts';

    public $timestamps = true;
    public $guarded = ['id'];
    public $fillable = ['name','shortened','description','type', 'is_valid','user_id','role_id'];
    use SoftDeletes;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function getLastAttribute()
    {
        return $this->get()->last();
    }
    // add records
    public function records()
    {
      return $this->morphMany(Record::class, 'recordable');
    }
}
