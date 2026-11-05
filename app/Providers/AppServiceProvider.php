<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

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
        // Visitas a enlaces cortos: 60 por minuto por IP.
        RateLimiter::for('redirect', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        // Creación de enlaces: 10 por minuto por usuario.
        RateLimiter::for('create-links', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });
    }
}
