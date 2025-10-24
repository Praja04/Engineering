<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Kalibrasi\KalibrasiController;
use App\Http\Controllers\Kalibrasi\KalibrasiPressureController;
use App\Http\Controllers\Kalibrasi\KalibrasiCertificateController;

Route::prefix('kalibrasi')->group(function () {
    Route::get('/data/master/alat', [KalibrasiController::class, 'getDataAlatKalibrasi']);
    Route::get('/show/master/alat/{id}', [KalibrasiController::class, 'showAlatKalibrasi']);
    Route::get('/master/filters', [KalibrasiController::class, 'getFilters']);
    Route::get('/schedule', [KalibrasiController::class, 'getSchedule']);

    // Certificate
    Route::get('/certificate/data', [KalibrasiCertificateController::class, 'getDataCertificate']);
    Route::get('/approvals/data', [KalibrasiCertificateController::class, 'getUserApprovals']);
    Route::get('/certificate/approval/data/{id?}', [KalibrasiCertificateController::class, 'getSertifikatData']);
    Route::post('/approval/{id}/approve', [KalibrasiCertificateController::class, 'approve'])->name('approval.approve');
    Route::post('/approval/{id}/reject', [KalibrasiCertificateController::class, 'reject'])->name('approval.reject');

    Route::prefix('pressure')->group(function () {
        Route::get('/data/alat/{id}', [KalibrasiPressureController::class, 'show']);
        Route::get('/data', [KalibrasiPressureController::class, 'getData']);
    });
});

Route::prefix('notifications')->group(function () {
    Route::post('/read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.read');
});


////// api utility listrik routes ///////
@include 'utility/api-listrik-routes.php';
