<?php

namespace App\Providers;


use App\LoanContributionAdjust;
use App\Observers\LoanContributionAdjustObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class LoanContributionAdjustModelServiceProvider extends ServiceProvider
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
        if (Schema::connection('platform')->hasTable('loan_contribution_adjusts')) LoanContributionAdjust::observe(LoanContributionAdjustObserver::class);
    }
}
