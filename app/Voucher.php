<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    //
    public $timestamps = true;
    public $guarded = ['id'];
    protected $fillable = ['user_id','affiliate_id', 'voucher_type_id','code','total','payment_date','bank','bank_pay_number', 'payable_id', 'payable_type', 'payment_tye_id'];

    public function payable()
    {
        return $this->morphTo();
    }

    public function payment_type()
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function voucher_type()
    {
        return $this->belongsTo(VoucherType::class);
    }
}
