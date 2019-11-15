<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model
{
    public $timestamps = false;

    public $fillable = ['name'];
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

}
