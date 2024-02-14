<?php

namespace App\Providers;

use App\Services\AddressService;
use App\Services\Impl\AddressServiceImpl;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class AddressServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function provides()
    {
        return [AddressService::class];
    }
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(AddressService::class, AddressServiceImpl::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
