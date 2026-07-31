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
        // Super Admin & Director IT global bypass for all system permissions
        Gate::before(function ($user, $ability) {
            if (
                in_array($user->email, ['maazaliswati@gmail.com', 'directorit@uvasswat.edu.pk']) ||
                $user->hasRole('Super Admin') ||
                $user->hasRole('Director IT') ||
                $user->hasRole('UVAS SWAT') ||
                $user->hasRole('University Admin')
            ) {
                return true;
            }
            return null;
        });
    }
}
