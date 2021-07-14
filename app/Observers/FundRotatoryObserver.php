<?php

namespace App\Observers;

use App\FundRotatory;
use App\Helpers\Util;
use App\User;

class FundRotatoryObserver
{
    /**
     * Handle the fund rotatory "created" event.
     *
     * @param  \App\FundRotatory  $fundRotatory
     * @return void
     */
    public function created(FundRotatory $fundRotatory)
    {
        Util::save_record($fundRotatory, 'datos-de-un-tramite', 'Registró Fondo rotatorio : '.$fundRotatory->code_entry);
    }

    /**
     * Handle the fund rotatory "updated" event.
     *
     * @param  \App\FundRotatory  $fundRotatory
     * @return void
     */
    public function updated(FundRotatory $fundRotatory)
    {
        Util::save_record($fundRotatory, 'datos-de-un-tramite', Util::concat_action($fundRotatory));
    }

    /**
     * Handle the fund rotatory "deleted" event.
     *
     * @param  \App\FundRotatory  $fundRotatory
     * @return void
     */
    public function deleted(FundRotatory $fundRotatory)
    {
        Util::save_record($fundRotatory, 'datos-de-un-tramite', 'Eliminó Fondo rotatorio : '.$fundRotatory->code_entry);
    }

    /**
     * Handle the fund rotatory "restored" event.
     *
     * @param  \App\FundRotatory  $fundRotatory
     * @return void
     */
    public function restored(FundRotatory $fundRotatory)
    {
        Util::save_record($fundRotatory, 'datos-de-un-tramite', 'Restauró Fondo rotatorio : '.$fundRotatory->code_entry);
    }

    /**
     * Handle the fund rotatory "force deleted" event.
     *
     * @param  \App\FundRotatory  $fundRotatory
     * @return void
     */
    public function forceDeleted(FundRotatory $fundRotatory)
    {
        Util::save_record($fundRotatory, 'datos-de-un-tramite', 'Eliminó Fondo rotatorio : '.$fundRotatory->code_entry);
    }
}
