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

    /**
     * Handle the observation "updated" event.
     *
     * @param  \App\Observation  $observation
     * @return void
     */
    public function updating(Observation $observation)
    {
        Util::save_record($observation, $this->record_type, ($observation->enabled ? 'subsanó observación: ' : 'observó: ') . $observation->message, $observation->observable);
    }
}
