<?php

namespace App\Providers;

use App\Record;
use App\Observers\RecordObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class RecordModelServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if (Schema::connection('platform')->hasTable('records')) Record::observe(RecordObserver::class);
    }
}
