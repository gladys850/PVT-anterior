<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Degree extends Model
{
    protected $fillable = [
        'hierarchy_id',
        'code',
        'name',
        'shortened'
    ];

    public function affiliates()
    {
        return $this->hasMany(Affiliate::class);
    }
    
}