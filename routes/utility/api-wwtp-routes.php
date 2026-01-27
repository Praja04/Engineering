<?php

use App\Http\Controllers\Utility\WWTPControllerPerformance;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Utility\WWTPControllerProses;
use App\Http\Controllers\Utility\WWTPControllerSludge;

// WWTP Proses Routes
Route::prefix('wwtp')->group(function () {
    // WWTP Daily Data Routes (Influent Harian) - HARUS DI ATAS /{id}
    Route::get('/influent-harian', [WWTPControllerProses::class, 'indexHarian']);
    Route::post('/influent-harian', [WWTPControllerProses::class, 'storeinfluentHarian'])->name('wwtp.influent-harian.store');
    Route::get('/influent-harian/{id}', [WWTPControllerProses::class, 'showHarian']);
    Route::put('/influent-harian/{id}', [WWTPControllerProses::class, 'updateHarian']);
    Route::delete('/influent-harian/{id}', [WWTPControllerProses::class, 'destroyHarian']);

    Route::prefix('dashboard')->group(function () {
        Route::get('/statistics', [WWTPControllerProses::class, 'getStatistics']);
        Route::get('/influent-chart/{period?}', [WWTPControllerProses::class, 'getInfluentChartData']);
        Route::get('/effluent-chart/{period?}', [WWTPControllerProses::class, 'getEffluentChartData']);
        Route::get('/monthly-comparison', [WWTPControllerProses::class, 'getMonthlyComparison']);
        Route::get('/recent-records/{limit?}', [WWTPControllerProses::class, 'getRecentRecords']);
    });

    // WWTP Proses Routes - HARUS DI BAWAH /influent-harian
    Route::post('/', [WWTPControllerProses::class, 'store']);
    Route::get('/', [WWTPControllerProses::class, 'index']);
    Route::get(
        '/{id}',
        [WWTPControllerProses::class, 'show']
    );
    Route::put(
        '/{id}',
        [WWTPControllerProses::class, 'update']
    );
    Route::delete('/{id}', [WWTPControllerProses::class, 'destroy']);
});
// WWTP Performance Routes (menggunakan prefix berbeda)
Route::prefix('wwtp-performance')->group(function () {

    Route::get('/ph-harian', [WWTPControllerPerformance::class, 'indexPHHarian']);
    Route::post('/ph-harian', [WWTPControllerPerformance::class, 'storePHHarian']);
    Route::get('/ph-harian/{id}', [WWTPControllerPerformance::class, 'showPHHarian']);
    Route::put('/ph-harian/{id}', [WWTPControllerPerformance::class, 'updatePHHarian']);
    Route::delete('/ph-harian/{id}', [WWTPControllerPerformance::class, 'destroyPHHarian']);

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
    Route::post('/', [WWTPControllerPerformance::class, 'store']);
    Route::delete('/{id}', [WWTPControllerPerformance::class, 'destroy']);
    Route::post('/{id}', [WWTPControllerPerformance::class, 'update']); // Changed to POST for form-data
    Route::get('/', [WWTPControllerPerformance::class, 'index']);
    Route::get('/{id}', [WWTPControllerPerformance::class, 'show']);
});

// WWTP Sludge Routes
Route::prefix('wwtp-sludge')->group(function () {
    Route::post('/', [WWTPControllerSludge::class, 'store']);
    Route::delete('/{id}', [WWTPControllerSludge::class, 'destroy']);
    Route::post('/{id}', [WWTPControllerSludge::class, 'update']);
    Route::get('/', [WWTPControllerSludge::class, 'index']);
    Route::get('/{id}', [WWTPControllerSludge::class, 'show']);
});
