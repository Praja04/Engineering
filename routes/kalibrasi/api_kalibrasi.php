<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Kalibrasi\KalibrasiController;
use App\Http\Controllers\Kalibrasi\KalibrasiPressureController;
use App\Http\Controllers\Kalibrasi\KalibrasiVolumetrikController;
use App\Http\Controllers\Kalibrasi\KalibrasiCertificateController;
use App\Http\Controllers\Kalibrasi\KalibrasiDimensiController;
use App\Http\Controllers\Kalibrasi\KalibrasiFlowmeterController;
use App\Http\Controllers\Kalibrasi\KalibrasiInstrumenController;
use App\Http\Controllers\Kalibrasi\KalibrasiTemperatureController;
use App\Http\Controllers\Kalibrasi\KalibrasiJangkaSorongController;
use App\Http\Controllers\Kalibrasi\KalibrasiThermohygrometerController;
use App\Http\Controllers\Kalibrasi\KalibrasiTimbanganController;

Route::prefix('kalibrasi')->group(function () {
    Route::get('/data/master/alat', [KalibrasiController::class, 'getDataAlatKalibrasi']);
    Route::get('/show/master/alat/{id}', [KalibrasiController::class, 'showAlatKalibrasi']);
    Route::get('/master/filters', [KalibrasiController::class, 'getFilters']);
    Route::get('/schedule', [KalibrasiController::class, 'getSchedule']);

    // Certificate
    Route::get('/certificate/data', [KalibrasiCertificateController::class, 'getDataCertificate']);
    Route::get('/approvals/data', [KalibrasiCertificateController::class, 'getUserApprovals']);
    Route::get('/certificate/approval/data/{id?}', [KalibrasiCertificateController::class, 'getSertifikatData']);


    Route::prefix('pressure')->group(function () {
        Route::get('/data/alat/{id}', [KalibrasiPressureController::class, 'show']);
        Route::get('/data', [KalibrasiPressureController::class, 'getData']);
    });

    Route::get('volumetrik/data', [KalibrasiVolumetrikController::class, 'getData']);
    Route::get('temperature/data', [KalibrasiTemperatureController::class, 'getData']);
    Route::get('thermohygrometer/data', [KalibrasiThermohygrometerController::class, 'getData']);
    Route::get('jangka-sorong/data', [KalibrasiJangkaSorongController::class, 'getData']);
    Route::get('timbangan/data', [KalibrasiTimbanganController::class, 'getData']);
    Route::get('instrumen/data', [KalibrasiInstrumenController::class, 'getData']);
    Route::get('dimensi/data', [KalibrasiDimensiController::class, 'getData']);
    Route::get('flowmeter/data', [KalibrasiFlowmeterController::class, 'getData']);
});

Route::prefix('notifications')->group(function () {
    Route::post('/read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::get('/kalibrasi/approval', [NotificationController::class, 'kalibrasiCertificate'])->name('notifications');
});
