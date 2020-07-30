<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FinancialEntity extends Model
{
    public $timestamps = true;
    public $fillable = ['name'];

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}
