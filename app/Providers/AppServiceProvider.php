<?php

namespace App\Providers;

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
        if (app()->environment('production')) {
            \URL::forceScheme('https');
        }

        // Shared Hosting Storage Symlink Fix
        // If public/storage doesn't exist, try to create it automatically
        if (!file_exists(public_path('storage'))) {
            @symlink(storage_path('app/public'), public_path('storage'));
        }
    }
}
