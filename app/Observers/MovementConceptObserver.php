<?php

namespace App\Observers;

use App\MovementConcept;
use App\Helpers\Util;
use App\User;

class MovementConceptObserver
{
    /**
     * Handle the movement concept "created" event.
     *
     * @param  \App\MovementConcept  $movementConcept
     * @return void
     */
    public function created(MovementConcept $movementConcept)
    {
        Util::save_record($movementConcept, 'datos-de-un-tramite', 'Registró Concepto de movimientos: '.$movementConcept->name);
    }

    /**
     * Handle the movement concept "updated" event.
     *
     * @param  \App\MovementConcept  $movementConcept
     * @return void
     */
    public function updated(MovementConcept $movementConcept)
    {
        Util::save_record($movementConcept, 'datos-de-un-tramite', Util::concat_action($movementConcept));
    }

    /**
     * Handle the movement concept "deleted" event.
     *
     * @param  \App\MovementConcept  $movementConcept
     * @return void
     */
    public function deleted(MovementConcept $movementConcept)
    {
        Util::save_record($movementConcept, 'datos-de-un-tramite', 'Eliminó Concepto de movimientos : '.$movementConcept->name);
    }

    /**
     * Handle the movement concept "restored" event.
     *
     * @param  \App\MovementConcept  $movementConcept
     * @return void
     */
    public function restored(MovementConcept $movementConcept)
    {
        Util::save_record($movementConcept, 'datos-de-un-tramite', 'Restauró Concepto de movimientos : '.$movementConcept->name);
    }

    /**
     * Handle the movement concept "force deleted" event.
     *
     * @param  \App\MovementConcept  $movementConcept
     * @return void
     */
    public function forceDeleted(MovementConcept $movementConcept)
    {
        Util::save_record($movementConcept, 'datos-de-un-tramite', 'Eliminó Concepto de movimientos : '.$movementConcept->name);
    }
}
