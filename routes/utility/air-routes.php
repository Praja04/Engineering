<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Utility\AirController;

Route::prefix('utility')->middleware('auth')->group(function () {

    Route::post('/data/air/store', [AirController::class, 'storeAir'])->name('air.store'); // Simpan data baru
    Route::get('/air-area', [AirController::class, 'getAirAreas']);
    Route::get('api/air/{mode}', [AirController::class, 'getPemakaianAir']);
    Route::get('data/air', [AirController::class, 'getPemakaianAirData']);
    Route::get('/trend-pemakaian-air', [AirController::class, 'getTrendPemakaianAir']);

    Route::get('/top5/air', [AirController::class, 'getTopJenisPemakaianAir']);
    Route::get('/top5/air/raw', [AirController::class, 'getTopJenisPemakaianAirRaw']);
    Route::get('/top5/operator/air', [AirController::class, 'getTopOperatorPemakaianAir']);
    Route::post('/update-pemakaian-air', [AirController::class, 'updateAir']);
   Route::get('/export-pemakaian-air', [AirController::class, 'exportPemakaianAirSpreadsheet']);

});
