<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Super Admin bypass for all permissions & abilities across all roles (UVAS SWAT, Super Admin, University Admin)
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('Super Admin') || $user->hasRole('UVAS SWAT') || $user->hasRole('Super-Admin') || $user->hasRole('University Admin')) {
                return true;
            }
            return null;
        });
    }
}
