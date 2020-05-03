<?php

namespace App\Providers;

use App\Loan;
use App\Observers\LoanObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class LoanModelServiceProvider extends ServiceProvider
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
        if (Schema::connection('platform')->hasTable('loans')) Loan::observe(LoanObserver::class);
    }
}
