<?php

namespace App\Observers;

use App\AidContribution;
use App\Helpers\Util;

class AidContributionObserver
{
    /**
     * Handle the aid contribution "created" event.
     *
     * @param  \App\AidContribution  $aidContribution
     * @return void
     */
    public function created(AidContribution $aidContribution)
    {
        //$aid_contribution = AidContribution::find($object);
        Util::save_record($aidContribution, 'contribuciones', 'Registró contribución : '. $aidContribution->id.' de fecha '.$aidContribution->month_year);
    }

    /**
     * Handle the aid contribution "updated" event.
     *
     * @param  \App\AidContribution  $aidContribution
     * @return void
     */
    public function updated(AidContribution $aidContribution)
    {
        Util::save_record($aidContribution, 'contribuciones', 'Actualizó contribución : '. $aidContribution->id.' de fecha '.$aidContribution->month_year);
    }

    /**
     * Handle the aid contribution "deleted" event.
     *
     * @param  \App\AidContribution  $aidContribution
     * @return void
     */
    public function deleted(AidContribution $aidContribution)
    {
        Util::save_record($aidContribution, 'contribuciones', 'Eliminó contribución : '. $aidContribution->id.' de fecha '.$aidContribution->month_year);
    }

    /**
     * Handle the aid contribution "restored" event.
     *
     * @param  \App\AidContribution  $aidContribution
     * @return void
     */
    public function restored(AidContribution $aidContribution)
    {
        //
    }

    /**
     * Handle the aid contribution "force deleted" event.
     *
     * @param  \App\AidContribution  $aidContribution
     * @return void
     */
    public function forceDeleted(AidContribution $aidContribution)
    {
        //
    }
}
