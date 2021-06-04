<?php

namespace App\Observers;

use App\Observation;
use Util;

class ObservationObserver
{
    protected $record_type;

    public function __construct()
    {
        $this->record_type = 'observaciones';
    }

    /**
     * Handle the observation "created" event.
     *
     * @param  \App\Observation  $observation
     * @return void
     */
    public function created(Observation $observation)
    {
        Util::save_record($observation, $this->record_type, 'registró observación: ' . $observation->message, $observation->observable);
    }
}
