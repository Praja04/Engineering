<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Kalibrasi\KalibrasiController;
use App\Http\Controllers\Kalibrasi\KalibrasiPressureController;


Route::prefix('kalibrasi')->group(function () {
    Route::get('/data/master/alat', [KalibrasiController::class, 'getDataAlatKalibrasi']);
    Route::get('/show/master/alat/{id}', [KalibrasiController::class, 'showAlatKalibrasi']);
    Route::get('/master/filters', [KalibrasiController::class, 'getFilters']);

    Route::prefix('pressure')->group(function () {
        Route::post('/store', [KalibrasiPressureController::class, 'store']);
        Route::get('/data/alat/{id}', [KalibrasiPressureController::class, 'show']);
    });
});


