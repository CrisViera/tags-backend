<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

/*
Rutas públicas
*/
Route::post('/users', [UserController::class, 'store']);
/*
Rutas protegidas con Sanctum
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn(Request $request) => $request->user());

    Route::apiResource('reminders', ReminderController::class);

    Route::post('/logout', function (Request $request) {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logout correcto']);
    });

    // CRUD de recordatorios
    Route::apiResource('reminders', ReminderController::class);
});