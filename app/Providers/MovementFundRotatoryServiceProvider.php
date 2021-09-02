<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\MovementFundRotatory;
use App\Observers\MovementFundRotatoryObserver;
use Illuminate\Support\Facades\Schema;

class MovementFundRotatoryServiceProvider extends ServiceProvider
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
        if (Schema::connection('platform')->hasTable('records')) MovementFundRotatory::observe(MovementFundRotatoryObserver::class);
    }
}
