<?php

namespace App\Providers;

use App\Services\ContactService;
use App\Services\Impl\ContactServiceImpl;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class ContactServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function provides()
    {
        return [ContactService::class];
    }
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ContactService::class, ContactServiceImpl::class);
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
