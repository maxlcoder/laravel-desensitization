<?php

namespace Maxlcoder\LaravelDesensitization;

use Illuminate\Support\ServiceProvider;

class LaravelDesensitizationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('laravel-desensitization.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        //
    }
}
