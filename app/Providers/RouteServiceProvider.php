<?php
// app/Providers/RouteServiceProvider.php
namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Limiter general para API/SPAs
        RateLimiter::for('api', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(60)->by($request->user()->id)
                : Limit::perMinute(30)->by($request->ip());
        });

        // Limiter específico de login
        RateLimiter::for('login', function (Request $request) {
            // 5 intentos por minuto por IP + email
            $key = sprintf('login|%s|%s', $request->ip(), (string) $request->input('email'));
            return [
                Limit::perMinute(5)->by($key)->response(function () {
                    return response()->json([
                        'message' => 'Demasiados intentos de inicio de sesión. Inténtalo de nuevo en 1 minuto.'
                    ], 429);
                }),
            ];
        });

        // (Opcional) registro de usuarios
        RateLimiter::for('register', fn (Request $r) => [ Limit::perMinute(3)->by($r->ip()) ]);
    }
}