<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use Traits\EloquentGetTableNameTrait;
    public $timestamps = true;
    public $guarded = ['id'];
    protected $fillable = ['user_id','affiliate_id', 'voucher_type_id','code','total','payment_date','paid_amount','bank','bank_pay_number', 'payable_id', 'payable_type', 'payment_type_id'];

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
