<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Boiler\DashboardController;
use App\Http\Controllers\Boiler\SensorBoilerController;

Route::middleware(['auth', 'access'])->group(function () {
    Route::prefix('boiler')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('boiler.dashboard');
        Route::get('send/tele', [SensorBoilerController::class, 'Notif_boiler']);
        Route::view('/realtime', 'boiler.realtime');
        Route::view('/datatren', 'boiler.datatren');
        Route::get('/kondensat/data', [SensorBoilerController::class, 'getKondensatData']);

        Route::prefix('sensor')->group(function () {
            Route::get('/boiler/data-filter', [SensorBoilerController::class, 'getFilteredBoilerData']);
            Route::get('/boiler-data', [SensorBoilerController::class, 'getBoilerData']);

            Route::get('/boiler/data-harian', [SensorBoilerController::class, 'getBoilerDataHarian']);
            Route::get('/boiler/data-mingguan', [SensorBoilerController::class, 'getBoilerDataMingguan']);
            Route::get('/boiler-realtime', [SensorBoilerController::class, 'getSensorData']);
            Route::get('/rhtemp', [SensorBoilerController::class, 'getAbnormalPeriodsRHTemp']);
            Route::get('/lhtemp', [SensorBoilerController::class, 'getAbnormalPeriodsLHTemp']);
            Route::get('/pvsteam', [SensorBoilerController::class, 'getAbnormalPeriodsPVSteam']);
            Route::get('/levelfeedwater', [SensorBoilerController::class, 'getAbnormalPeriodsLevelFeedWater']);
        });
    });
});
