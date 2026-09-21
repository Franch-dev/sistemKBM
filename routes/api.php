<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;


// Publik
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

// Wajib login (semua role)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/dashboard', [DashboardController::class, 'show']);

    // Fitur #1 "Mengelola Akun User" — khusus Admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/users/options', [UserController::class, 'options']);
        Route::apiResource('users', UserController::class);
    });
});
