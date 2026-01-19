<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Boiler\BoilerController;
use App\Http\Controllers\Boiler\DashboardBoilerController;

Route::prefix('boiler')->group(function () {
    Route::get('/get-data', [BoilerController::class, 'getData'])->name('boiler.get-data');
    Route::prefix('dashboard')->group(function () {
        Route::get('/bbsteam', [DashboardBoilerController::class, 'getBatuBaraSteam']);
        Route::get('/steamfg', [DashboardBoilerController::class, 'getSteamFg']);
        Route::get('/steamfg-monthly', [DashboardBoilerController::class, 'getSteamFgMonthly']);
        Route::get('/bbfg', [DashboardBoilerController::class, 'getBatuBaraFg']);
        Route::get('/bbfg-monthly', [DashboardBoilerController::class, 'getBatuBaraFgMonthly']);
    });
});
