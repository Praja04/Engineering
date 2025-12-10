<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Utility\WWTPController;

Route::prefix('wwtp')->group(function () {

    // GET /api/wwtp
    Route::get('/', [WwtpController::class, 'index']);

    // POST /api/wwtp
    Route::post('/', [WwtpController::class, 'store']);

    // GET /api/wwtp/{id}
    Route::get('/{id}', [WwtpController::class, 'show']);

    // PUT /api/wwtp/{id}
    Route::put('/{id}', [WwtpController::class, 'update']);

    // DELETE /api/wwtp/{id}
    Route::delete('/{id}', [WwtpController::class, 'destroy']);

    Route::prefix('dashboard')->group(function () {
        Route::get('/statistics', [WwtpController::class, 'getStatistics']);
        Route::get('/influent-chart/{period?}', [WwtpController::class, 'getInfluentChartData']);
        Route::get('/effluent-chart/{period?}', [WwtpController::class, 'getEffluentChartData']);
        Route::get('/monthly-comparison', [WwtpController::class, 'getMonthlyComparison']);
        Route::get('/recent-records/{limit?}', [WwtpController::class, 'getRecentRecords']);
    });
});

