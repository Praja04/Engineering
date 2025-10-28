<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Kalibrasi\KalibrasiCertificateController;
use App\Http\Controllers\Kalibrasi\KalibrasiController;
use App\Http\Controllers\Kalibrasi\KalibrasiPressureController;
use App\Http\Controllers\Kalibrasi\KalibrasiVolumtrikController;

// All Access
Route::middleware(['auth', 'access:Engineering Kalibrasi'])->group(function () {

    Route::prefix('kalibrasi')->group(function () {
        Route::prefix('form')->group(function () {
            Route::get('/dashboard', [KalibrasiController::class, 'dashboardForm'])->name('kalibrasi.form.dashboard');
            Route::get('/pressure', [KalibrasiPressureController::class, 'index'])->name('kalibrasi.form.pressure');
            Route::get('/volumetrik', [KalibrasiVolumtrikController::class, 'showForm'])->name('kalibrasi.form.volumetrik');
            Route::get('/dev-page', [KalibrasiController::class, 'viewDevPage'])->name('kalibrasi.form.dev-page');
        });

        Route::prefix('data')->group(function () {
            Route::get('/dashboard', [KalibrasiController::class, 'dashboardData'])->name('kalibrasi.data.dashboard');
            Route::get('/pressure', [KalibrasiPressureController::class, 'viewData'])->name('kalibrasi.data.pressure');
            Route::get('/volumetrik', [KalibrasiVolumtrikController::class, 'viewData'])->name('kalibrasi.data.volumetrik');
            Route::get('/dev-page', [KalibrasiController::class, 'viewDevPage'])->name('kalibrasi.data.dev-page');
        });

        Route::get('/schedule', [KalibrasiController::class, 'viewSchedule'])->name('kalibrasi.schedule');
        Route::get('/certificate', [KalibrasiController::class, 'viewCertificate'])->name('kalibrasi.certificate');
        Route::post('/certificate/req-approval/{id}', [KalibrasiCertificateController::class, 'reqApproval'])->name('kalibrasi.certificate.req-approval');
        // Route::get('/certificate/approvals/', [KalibrasiCertificateController::class, 'showApprovalPage'])->name('kalibrasi.certificate.approval.detail');
        Route::get('/certificate/data', [KalibrasiCertificateController::class, 'getDataCertificate']);
        Route::get('/certificate/approvals', [KalibrasiCertificateController::class, 'showApprovalPage'])->name('kalibrasi.certificate.approvals');
        Route::post('/certificate/process-approval', [KalibrasiCertificateController::class, 'processApproval'])->name('kalibrasi.certificate.process-approval');
        Route::get('/certificate/approval/data', [KalibrasiCertificateController::class, 'getSertifikatData']);
        Route::post('/certificate/approval/action', [KalibrasiCertificateController::class, 'handleApproval'])->name('kalibrasi.certificate.approval.action');
        Route::get('/certificate/cetak/{id}', [KalibrasiCertificateController::class, 'printSertifikat'])->name('kalibrasi.certificate.cetak');
        Route::get('/certificate/download/{id}', [KalibrasiCertificateController::class, 'downloadSertifikat'])->name('kalibrasi.certificate.download');

        // Pressure Routes
        Route::prefix('pressure')->group(function () {
            Route::post('/store', [KalibrasiPressureController::class, 'store'])->name('kalibrasi.pressure.store');
            Route::delete('/delete/{id}', [KalibrasiPressureController::class, 'destroy'])->name('kalibrasi.pressure.delete');
        });

        Route::prefix('volumetrik')->group(function () {
            Route::post('/store', [KalibrasiVolumtrikController::class, 'store'])->name('kalibrasi.volumetrik.store');
        });
    });
});

// Not Operator
Route::middleware(['auth', 'access'])->group(function () {
    Route::prefix('kalibrasi')->group(function () {
        Route::get('/master/alat', [KalibrasiController::class, 'viewMasterAlat'])->name('master.alat');
        Route::post('/store/master/alat', [KalibrasiController::class, 'storeAlatKalibrasi'])->name('store.master.alat');
        Route::put('/update/master/alat/{id}', [KalibrasiController::class, 'updateAlatKalibrasi'])->name('update.master.alat');
        Route::delete('/delete/master/alat/{id}', [KalibrasiController::class, 'destroyAlatKalibrasi'])->name('delete.master.alat');
        Route::get('/master/download/template', [KalibrasiController::class, 'downloadTemplateAlatKalibrasi'])->name('master.download.template');
        Route::post('/master/import', [KalibrasiController::class, 'importAlatKalibrasi'])->name('master.import');
    });
});
