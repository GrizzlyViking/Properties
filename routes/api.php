<?php

use App\Http\Controllers\Auth\API\AuthController;
use App\Http\Controllers\NodeController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::post('/createRentalContract/{Property}', [NodeController::class, 'createRentalContract'])->name('property.createRentalContract');
    Route::get('/{Property}/tenants', [NodeController::class, 'getPropertyTenants'])->name('property.tenants');
});
