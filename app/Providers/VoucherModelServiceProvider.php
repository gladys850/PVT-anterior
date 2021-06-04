<?php

namespace App\Providers;

use App\Voucher;
use App\Observers\VoucherObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class VoucherModelServiceProvider extends ServiceProvider
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
        if (Schema::connection('platform')->hasTable('vouchers')) Voucher::observe(VoucherObserver::class);

    }
}
