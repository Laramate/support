<?php

namespace Laramate\Support\Providers;

use Illuminate\Support\ServiceProvider;

class SupportServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            // Registering package commands.
        }
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}
