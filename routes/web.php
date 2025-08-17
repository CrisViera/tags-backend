<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:login'); 

Route::post('/users', [UserController::class, 'store'])
    ->middleware('throttle:register');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');