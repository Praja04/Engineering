<?php

use App\Http\Controllers\Boiler\DashboardBoilerController;
use Illuminate\Support\Facades\Route;

Route::prefix('boiler')->group(function () {
    Route::prefix('dashboard')->group(function () {
        Route::get('/bbsteam', [DashboardBoilerController::class, 'getBatuBaraSteam']);
        Route::get('/steamfg', [DashboardBoilerController::class, 'getSteamFg']);
        Route::get('/bbfg', [DashboardBoilerController::class, 'getBatuBaraFg']);
    });
});
