<?php

namespace App\Providers;

use App\RoleSequence;
use App\Observers\RoleSequenceObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class RoleSequenceModelServiceProvider extends ServiceProvider
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
        if (Schema::connection('platform')->hasTable('role_sequences')) RoleSequence::observe(RoleSequenceObserver::class);
    }
}
