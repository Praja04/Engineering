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