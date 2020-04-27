<?php

namespace App\Observers;
use App\Loan;
use App\Helpers\Util;

class LoanObserver
{
    /**
    * Handle the contract "created" event.
    *
    * @param  \App\Loan  $contract
    * @return void
    */
    public function created(Loan $object)
    {
        Util::save_record($object, 'datos-de-un-tramite', 'registró préstamo : '. $object->code);
    }
    /**
    * Handle the loan "updating" event.
    *
    * @param  \App\Loan  $Loan
    * @return void
    */
    public function updating(Loan $object)
    {
        Util::save_record($object, 'datos-de-un-tramite', Util::concat_action($object));
    }
    /**
    * Handle the loan "deleted" event.
    *
    * @param  \App\Loan  $Loan
    * @return void
    */
    public function deleted(Loan $object)
    {
        Util::save_record($object, 'datos-de-un-tramite', 'eliminó préstamo: ' . $object->code);
    }

    public function pivotUpdating(Loan $object, $relationName, $pivotIds, $pivotIdsAttributes)
    {
        Util::save_record($object, 'datos-de-un-tramite', Util::relation_action($object, $relationName, $pivotIds, $pivotIdsAttributes, 'modificó'));
    }
}
