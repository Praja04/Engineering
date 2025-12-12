<?php

use App\Http\Controllers\Utility\WWTPControllerPerformance;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Utility\WWTPControllerProses;

// WWTP Proses Routes
Route::prefix('wwtp')->group(function () {
    Route::get('/', [WWTPControllerProses::class, 'index']);
    Route::post('/', [WWTPControllerProses::class, 'store']);
    Route::get('/{id}', [WWTPControllerProses::class, 'show']);
    Route::put('/{id}', [WWTPControllerProses::class, 'update']);
    Route::delete('/{id}', [WWTPControllerProses::class, 'destroy']);

    // Dashboard routes for Proses
    Route::prefix('dashboard')->group(function () {
        Route::get('/statistics', [WWTPControllerProses::class, 'getStatistics']);
        Route::get('/influent-chart/{period?}', [WWTPControllerProses::class, 'getInfluentChartData']);
        Route::get('/effluent-chart/{period?}', [WWTPControllerProses::class, 'getEffluentChartData']);
        Route::get('/monthly-comparison', [WWTPControllerProses::class, 'getMonthlyComparison']);
        Route::get('/recent-records/{limit?}', [WWTPControllerProses::class, 'getRecentRecords']);
    });
});

// WWTP Performance Routes (menggunakan prefix berbeda)
Route::prefix('wwtp-performance')->group(function () {
    Route::get('/', [WWTPControllerPerformance::class, 'index']);
    Route::post('/', [WWTPControllerPerformance::class, 'store']);
    Route::get('/{id}', [WWTPControllerPerformance::class, 'show']);
    Route::post('/{id}', [WWTPControllerPerformance::class, 'update']); // Changed to POST for form-data
    Route::delete('/{id}', [WWTPControllerPerformance::class, 'destroy']);

    Route::prefix('dashboard')->group(function () {
        // Dashboard Statistics
        Route::get('/statistics', [WWTPControllerPerformance::class, 'getStatistics']);

        // Chart Data per jenis (equal, anaerob, aerob, daf, outlet)
        Route::get('/chart/{jenis}/{period?}', [WWTPControllerPerformance::class, 'getChartData'])
            ->where('period', '[0-9]+');

        // Monthly comparison (6 bulan)
        Route::get('/monthly-comparison', [WWTPControllerPerformance::class, 'getMonthlyComparison']);

        // Recent records (default limit 10)
        Route::get('/recent/{limit?}', [WWTPControllerPerformance::class, 'getRecentRecords'])
            ->where('limit', '[0-9]+');

        // Weekly performance (opsional jika dipakai)
        Route::get('/weekly', [WWTPControllerPerformance::class, 'getWeeklyPerformance']);
    });
});
