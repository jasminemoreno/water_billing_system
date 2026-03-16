<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Middleware\Authenticate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any authentication / authorization services.
     */
    public function boot(): void
    {
        // Custom redirect for Authenticate middleware
        Authenticate::redirectUsing(function ($request) {
            // If route is under customer prefix, redirect to customer login
            if ($request->is('customer/*')) {
                return route('customer.login');
            }

            // Default redirect for admin
            return route('login');
        });
    }
}
