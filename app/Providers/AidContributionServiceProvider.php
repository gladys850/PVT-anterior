<?php

namespace App\Providers;

use App\AidContribution;
use Illuminate\Support\ServiceProvider;
use App\Observers\AidContributionObserver;
use Illuminate\Support\Facades\Schema;

class AidContributionServiceProvider extends ServiceProvider
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
        if (Schema::connection('platform')->hasTable('records')) AidContribution::observe(AidContributionObserver::class);
    }
}
