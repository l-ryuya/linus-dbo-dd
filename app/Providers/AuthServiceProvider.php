<?php

declare(strict_types=1);

namespace App\Providers;

use App\Auth\Guards\M5TokenGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Auth::extend('m5-token', function ($app, $name, array $config) {
            return new M5TokenGuard($app['request']);
        });
    }
}
