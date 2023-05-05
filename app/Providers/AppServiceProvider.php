<?php

namespace App\Providers;

use App\Config\ConfiguredFeedProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ConfiguredFeedProvider::class, function ($app) {
            $provider = new ConfiguredFeedProvider();
            $provider->loadFromConfig();
            return $provider;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
