<?php

namespace App\Observers;
use App\LoanPayment;
use App\Helpers\Util;


class LoanPaymentObserver
{
    /**
     * Handle the contract "created" event.
     *
     * @param  \App\LoanPayment  $contract
     * @return void
     */
    public function created(LoanPayment $object)
    {
        Util::save_record($object, 'datos-de-un-pago', 'registró pago : '. $object->id);
    }

    /**
     * Handle the loan payment "updated" event.
     *
     * @param  \App\LoanPayment  $loanPayment
     * @return void
     */
    /*public function updated(LoanPayment $object)
    {
        Util::save_record($object, 'datos-de-un-pago', Util::concat_action($object));
    }*/
    public function updating(LoanPayment $object)
    {
        Util::save_record($object, 'Datos de un registro pago', Util::concat_action($object));
    }

    /**
     * Handle the loan payment "deleted" event.
     *
     * @param  \App\LoanPayment  $loanPayment
     * @return void
     */
    public function deleted(LoanPayment $object)
    {
        Util::save_record($object, 'Datos de un registro pago', 'eliminó pago: ' . $object->id);
    }

    /**
     * Handle the loan payment "restored" event.
     *
     * @param  \App\LoanPayment  $loanPayment
     * @return void
     */
    public function restored(LoanPayment $object)
    {
        //
    }

    /**
     * Handle the loan payment "force deleted" event.
     *
     * @param  \App\LoanPayment  $loanPayment
     * @return void
     */
    public function forceDeleted(LoanPayment $object)
    {
        //
    }

    public function pivotUpdating(LoanPayment $object, $relationName, $pivotIds, $pivotIdsAttributes)
    {
        Util::save_record($object, 'datos-de-un-pago', Util::relation_action($object, $relationName, $pivotIds, $pivotIdsAttributes, 'modificó'));
    }
}
