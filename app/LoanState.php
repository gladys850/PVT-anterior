<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanState extends Model
{
    public $timestamps = false;
    public $guarded = ['id'];

    public function loans()
    {
        return $this->hasMany(Loans::class);
    }
}