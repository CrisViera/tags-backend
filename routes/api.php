<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
*/

// Registro (pública)
Route::post('/users', [UserController::class, 'store']);

// Login 
Route::post('/login', [AuthController::class, 'login']);


/*
|--------------------------------------------------------------------------
| Rutas protegidas con Sanctum (SPA con cookies)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Usuario autenticado
    Route::get('/user', fn(Request $request) => $request->user());

    // CRUD de recordatorios
    Route::apiResource('reminders', ReminderController::class);

    // Logout para Sanctum SPA (cookies de sesión)
    Route::post('/logout', function (Request $request) {
        Auth::guard('web')->logout();

        return response()->json(['message' => 'Logout correcto']);
    });
});