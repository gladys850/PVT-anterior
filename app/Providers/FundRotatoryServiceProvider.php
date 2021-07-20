<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\FundRotatory;
use App\Observers\FundRotatoryObserver;
use Illuminate\Support\Facades\Schema;

class FundRotatoryServiceProvider extends ServiceProvider
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
        if (Schema::connection('platform')->hasTable('records')) FundRotatory::observe(FundRotatoryObserver::class);
    }
}
