<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\MovementConcept;
use App\Observers\MovementConceptObserver;
use Illuminate\Support\Facades\Schema;

class MovementConceptServiceProvider extends ServiceProvider
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
        if (Schema::connection('platform')->hasTable('records')) MovementConcept::observe(MovementConceptObserver::class);
    }
}
