<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VoucherType extends Model
{
    public $timestamps = false;
    public $fillable = ['name'];
    public $guarded = ['id'];

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }
}
