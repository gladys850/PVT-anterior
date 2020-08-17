<?php

namespace App\Observers;
use App\Voucher;
use App\Helpers\Util;

class VoucherObserver
{
    /**
     * Handle the contract "created" event.
     *
     * @param  \App\Voucher  $voucher
     * @return void
     */
    public function created(Voucher $object)
    {
        Util::save_record($object, 'datos-de-un-pago', 'registró pago : '. $object->code);
    }
    /**
     * Handle the voucher "updated" event.
     *
     * @param  \App\Voucher  $voucher
     * @return void
     */
    public function updating(Voucher $object)
    {
        Util::save_record($object, 'datos-de-un-pago', Util::concat_action($object));
    }
    /**
     * Handle the voucher "deleted" event.
     *
     * @param  \App\Voucher  $voucher
     * @return void
     */
    public function deleted(Voucher $object)
    {
        Util::save_record($object, 'datos-de-un-pago', 'eliminó registro pago: ' . $object->code);
    }


}
