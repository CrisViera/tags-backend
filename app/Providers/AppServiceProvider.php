<?php

// app/Providers/AppServiceProvider.php
namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Limiter usado por 'throttle:api'
        RateLimiter::for('api', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(60)->by($request->user()->id)
                : Limit::perMinute(30)->by($request->ip());
        });

        // Limiter específico para /login (opcional pero recomendado)
        RateLimiter::for('login', function (Request $request) {
            $key = 'login|'.$request->ip().'|'.(string) $request->input('email');
            return [ Limit::perMinute(5)->by($key) ];
        });
    }
}