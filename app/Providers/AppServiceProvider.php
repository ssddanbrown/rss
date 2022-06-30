<?php

namespace App\Providers;

use App\Config\ConfiguredFeedProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ConfiguredFeedProvider::class, function ($app) {
            $provider = new ConfiguredFeedProvider();
            $provider->loadFromEnvironment();
            return $provider;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
