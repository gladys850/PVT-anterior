<?php

namespace App\Observers;

use App\MovementFundRotatory;
use App\Helpers\Util;
use App\User;

class MovementFundRotatoryObserver
{
    /**
     * Handle the fund rotatory "created" event.
     *
     * @param  \App\MovementFundRotatory  $movementFundRotatory
     * @return void
     */
    public function created(MovementFundRotatory $movementFundRotatory)
    {
        Util::save_record($movementFundRotatory, 'datos-de-un-tramite', 'Registró Movimiento de Fondo Rotatorio : '.$movementFundRotatory->movement_concept_code);
    }

    /**
     * Handle the fund rotatory "updated" event.
     *
     * @param  \App\MovementFundRotatory  $movementFundRotatory
     * @return void
     */
    public function updated(MovementFundRotatory $movementFundRotatory)
    {
        Util::save_record($movementFundRotatory, 'datos-de-un-tramite', Util::concat_action($movementFundRotatory));
    }

    /**
     * Handle the fund rotatory "deleted" event.
     *
     * @param  \App\MovementFundRotatory  $movementFundRotatory
     * @return void
     */
    public function deleted(MovementFundRotatory $movementFundRotatory)
    {
        Util::save_record($movementFundRotatory, 'datos-de-un-tramite', 'Eliminó Movimiento de Fondo Rotatorio : '.$movementFundRotatory->movement_concept_code);
    }

    /**
     * Handle the fund rotatory "restored" event.
     *
     * @param  \App\MovementFundRotatory  $movementFundRotatory
     * @return void
     */
    public function restored(MovementFundRotatory $movementFundRotatory)
    {
        Util::save_record($movementFundRotatory, 'datos-de-un-tramite', 'Restauró Movimiento de Fondo Rotatorio : '.$movementFundRotatory->movement_concept_code);
    }

    /**
     * Handle the fund rotatory "force deleted" event.
     *
     * @param  \App\MovementFundRotatory  $movementFundRotatory
     * @return void
     */
    public function forceDeleted(MovementFundRotatory $movementFundRotatory)
    {
        Util::save_record($movementFundRotatory, 'datos-de-un-tramite', 'Eliminó Movimiento de Fondo Rotatorio : '.$movementFundRotatory->movement_concept_code);
    }
}
