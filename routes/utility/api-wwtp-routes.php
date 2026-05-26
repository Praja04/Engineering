<?php

use App\Http\Controllers\Utility\WWTPControllerPerformance;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Utility\WWTPControllerProses;
use App\Http\Controllers\Utility\WWTPControllerSludge;
use App\Http\Controllers\Utility\WWTPControllerAnalisa;
use App\Http\Controllers\Utility\WWTPController;

// WWTP Proses Routes
Route::prefix('wwtp')->group(function () {
    // WWTP Daily Data Routes (Influent Harian) - HARUS DI ATAS /{id}
    Route::get('/all/export', [WWTPController::class, 'export']);
    Route::get('/influent-harian', [WWTPControllerProses::class, 'indexHarian']);
    Route::post('/influent-harian', [WWTPControllerProses::class, 'storeinfluentHarian'])->name('wwtp.influent-harian.store');
    Route::get('/influent-harian/{id}', [WWTPControllerProses::class, 'showHarian']);
    Route::put('/influent-harian/{id}', [WWTPControllerProses::class, 'updateHarian']);
    Route::delete('/influent-harian/{id}', [WWTPControllerProses::class, 'destroyHarian']);

    Route::prefix('dashboard')->group(function () {
        Route::get('/statistics', [WWTPControllerProses::class, 'getStatistics']);
        Route::get('/influent-chart', [WWTPControllerProses::class, 'getInfluentChartData']);
        Route::get('/effluent-chart', [WWTPControllerProses::class, 'getEffluentChartData']);
        Route::get('/monthly-comparison', [WWTPControllerProses::class, 'getMonthlyComparison']);
        Route::get('/recent-records/{limit?}', [WWTPControllerProses::class, 'getRecentRecords']);

        //harian
        Route::get('/statistics-harian', [WWTPControllerProses::class, 'getStatisticsHarian']);
        Route::get('/influent-harian-chart', [WWTPControllerProses::class, 'getInfluentHarianChartData']);
        Route::get('/shift-breakdown', [WWTPControllerProses::class, 'getShiftBreakdownData']);
        Route::get('/monthly-comparison-harian', [WWTPControllerProses::class, 'getMonthlyComparisonHarian']);
        Route::get('/recent-records-harian/{limit?}', [WWTPControllerProses::class, 'getRecentRecordsHarian']);
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
    Route::get('/photo-gallery', [WWTPControllerPerformance::class, 'getPhotoGallery'])
        ->name('wwtp.performance.photo-gallery');
    Route::get('/ph-harian', [WWTPControllerPerformance::class, 'indexPHHarian']);
    Route::post('/ph-harian', [WWTPControllerPerformance::class, 'storePHHarian']);
    Route::get('/ph-harian/{id}', [WWTPControllerPerformance::class, 'showPHHarian']);
    Route::put('/ph-harian/{id}', [WWTPControllerPerformance::class, 'updatePHHarian']);
    Route::delete('/ph-harian/{id}', [WWTPControllerPerformance::class, 'destroyPHHarian']);

    Route::get('/jenis-sampel', [WWTPControllerPerformance::class, 'getJenisSampel']);

    // CRUD Sample
    Route::get('/sample', [WWTPControllerPerformance::class, 'indexSample']);
    Route::get('/sample/{wwtpPerformanceSample}', [WWTPControllerPerformance::class, 'showsample']);
    Route::post('/sample', [WWTPControllerPerformance::class, 'storeSample']);
    Route::put('/sample/{wwtpPerformanceSample}', [WWTPControllerPerformance::class, 'updateSample']);
    Route::delete('/sample/{wwtpPerformanceSample}', [WWTPControllerPerformance::class, 'destroySample']);

    Route::prefix('dashboard')->group(function () {
        // Sample - Dashboard & Chart
        Route::get('/sample/statistics',          [WWTPControllerPerformance::class, 'getStatisticsSample']);
        Route::get('/sample/chart',               [WWTPControllerPerformance::class, 'getChartDataSample']);
        Route::get('/sample/monthly-comparison',  [WWTPControllerPerformance::class, 'getMonthlyComparisonSample']);
        Route::get('/sample/recent',              [WWTPControllerPerformance::class, 'getRecentRecordsSample']);


        // ========== WEEKLY DATA ENDPOINTS ==========
        // Dashboard Statistics
        Route::get('/statistics', [WWTPControllerPerformance::class, 'getStatistics']);

        // Chart Data per jenis (equal, anaerob, aerob, daf, outlet) with date range
        Route::get('/chart/{jenis}', [WWTPControllerPerformance::class, 'getChartData']);

        // Monthly comparison (6 bulan)
        Route::get('/monthly-comparison', [WWTPControllerPerformance::class, 'getMonthlyComparison']);

        // Recent records (default limit 10)
        Route::get('/recent/{limit?}', [WWTPControllerPerformance::class, 'getRecentRecords'])
            ->where('limit', '[0-9]+');

        // Weekly performance (opsional jika dipakai)
        Route::get('/weekly', [WWTPControllerPerformance::class, 'getWeeklyPerformance']);

        // ========== DAILY DATA ENDPOINTS (NEW) ==========
        // Dashboard Statistics for Daily PH Harian
        Route::get('/statistics-harian', [WWTPControllerPerformance::class, 'getStatisticsHarian']);

        // Chart Data for PH Harian with date range filter
        Route::get('/chart-harian', [WWTPControllerPerformance::class, 'getChartDataHarian']);

        // Shift breakdown data for pie chart with date range
        Route::get('/shift-breakdown', [WWTPControllerPerformance::class, 'getShiftBreakdownData']);

        // Monthly comparison for PH Harian (6 months)
        Route::get('/monthly-comparison-harian', [WWTPControllerPerformance::class, 'getMonthlyComparisonHarian']);

        // Recent PH Harian records
        Route::get('/recent-harian/{limit?}', [WWTPControllerPerformance::class, 'getRecentRecordsHarian'])
            ->where('limit', '[0-9]+');
    });

    Route::post('/', [WWTPControllerPerformance::class, 'store']);
    Route::delete('/{id}', [WWTPControllerPerformance::class, 'destroy']);
    Route::post('/{id}', [WWTPControllerPerformance::class, 'update']); // Changed to POST for form-data
    Route::get('/', [WWTPControllerPerformance::class, 'index']);
    Route::get('/{id}', [WWTPControllerPerformance::class, 'show']);
});

// WWTP Sludge Routes
Route::prefix('wwtp-sludge')->group(function () {


    Route::prefix('dashboard')->group(function () {

        // Statistics endpoint
        Route::get('statistics', [WWTPControllerSludge::class, 'getStatistics']);

        // Chart data endpoints
        Route::get('drain-chart', [WWTPControllerSludge::class, 'getDrainChart']);
        Route::get('running-hour-chart', [WWTPControllerSludge::class, 'getRunningHourChart']);
        Route::get('hasil-lumpur-chart', [WWTPControllerSludge::class, 'getHasilLumpurChart']);
        Route::get('sludge-content-chart', [WWTPControllerSludge::class, 'getSludgeContentChart']);
        Route::get('pengangkutan-chart', [WWTPControllerSludge::class, 'getPengangkutanChart']);
        Route::get('shift-breakdown', [WWTPControllerSludge::class, 'getShiftBreakdown']);

        // Monthly comparison
        Route::get('monthly-comparison', [WWTPControllerSludge::class, 'getMonthlyComparison']);

        // Recent records
        Route::get('recent-records/{limit?}', [WWTPControllerSludge::class, 'getRecentRecords']);
    });

    Route::prefix('pengangkutan')->group(function () {
        Route::post('/', [WWTPControllerSludge::class, 'store_pengangkutan']);
        Route::delete('/{id}', [WWTPControllerSludge::class, 'destroy_pengangkutan']);
        Route::post('/{id}', [WWTPControllerSludge::class, 'update_pengangkutan']);
        Route::get('/', [WWTPControllerSludge::class, 'index_pengangkutan']);
        Route::get('/{id}', [WWTPControllerSludge::class, 'show_pengangkutan']);
    });
  

    Route::post('/', [WWTPControllerSludge::class, 'store']);
    Route::delete('/{id}', [WWTPControllerSludge::class, 'destroy']);
    Route::post('/{id}', [WWTPControllerSludge::class, 'update']);
    Route::get('/', [WWTPControllerSludge::class, 'index']);
    Route::get('/{id}', [WWTPControllerSludge::class, 'show']);
});

// WWTP Analisa Routes
Route::prefix('wwtp-analisa')->group(function () {
    Route::get('/check-filled', [WWTPControllerAnalisa::class, 'checkFilledParameters']);
    Route::post('/', [WWTPControllerAnalisa::class, 'store']);
    Route::delete('/{id}', [WWTPControllerAnalisa::class, 'destroy']);
    Route::post('/{id}', [WWTPControllerAnalisa::class, 'update']);
    Route::get('/', [WWTPControllerAnalisa::class, 'index']);
    Route::get('/{id}', [WWTPControllerAnalisa::class, 'show']);
});
