<?php

namespace App\Observers;

use App\Record;
use App\Events\RecordSavedEvent;

class RecordObserver
{
    /**
     * Handle the record "created" event.
     *
     * @param  \App\Record  $record
     * @return void
     */
    public function created(Record $record)
    {
        event(new RecordSavedEvent($record));
    }

    /**
     * Handle the record "updated" event.
     *
     * @param  \App\Record  $record
     * @return void
     */
    public function updated(Record $record)
    {
        //
    }

    /**
     * Handle the record "deleted" event.
     *
     * @param  \App\Record  $record
     * @return void
     */
    public function deleted(Record $record)
    {
        //
    }

    /**
     * Handle the record "restored" event.
     *
     * @param  \App\Record  $record
     * @return void
     */
    public function restored(Record $record)
    {
        //
    }

    /**
     * Handle the record "force deleted" event.
     *
     * @param  \App\Record  $record
     * @return void
     */
    public function forceDeleted(Record $record)
    {
        //
    }
}
