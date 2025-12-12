<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Kalibrasi\KalibrasiController;
use App\Http\Controllers\Kalibrasi\KalibrasiMasterController;
use App\Http\Controllers\Kalibrasi\KalibrasiPressureController;
use App\Http\Controllers\Kalibrasi\KalibrasiTimbanganController;
use App\Http\Controllers\Kalibrasi\KalibrasiVolumetrikController;
use App\Http\Controllers\Kalibrasi\KalibrasiCertificateController;
use App\Http\Controllers\Kalibrasi\KalibrasiTemperatureController;
use App\Http\Controllers\Kalibrasi\KalibrasiJangkaSorongController;
use App\Http\Controllers\Kalibrasi\KalibrasiThermohygrometerController;
use App\Http\Controllers\Kalibrasi\Master\KalibrasiMasterTimbanganController;
use App\Http\Controllers\Kalibrasi\Master\KalibrasiMasterJangkaSorongController;

// All Access
Route::middleware(['auth', 'access:Engineering Kalibrasi'])->group(function () {

    Route::prefix('kalibrasi')->group(function () {
        Route::prefix('form')->group(function () {
            Route::get('/dashboard', [KalibrasiController::class, 'dashboardForm'])->name('kalibrasi.form.dashboard');
            Route::get('/pressure', [KalibrasiPressureController::class, 'index'])->name('kalibrasi.form.pressure');
            Route::get('/volumetrik', [KalibrasiVolumetrikController::class, 'showForm'])->name('kalibrasi.form.volumetrik');
            Route::get('/temperature', [KalibrasiTemperatureController::class, 'showForm'])->name('kalibrasi.form.temperature');
            Route::get('/thermohygrometer', [KalibrasiThermohygrometerController::class, 'showForm'])->name('kalibrasi.form.thermohygrometer');
            Route::get('/jangka-sorong', [KalibrasiJangkaSorongController::class, 'showForm'])->name('kalibrasi.form.jangka-sorong');
            Route::get('/timbangan', [KalibrasiTimbanganController::class, 'showForm'])->name('kalibrasi.form.timbangan');
            Route::get('/dev-page', [KalibrasiController::class, 'viewDevPage'])->name('kalibrasi.form.dev-page');
        });

        Route::prefix('data')->group(function () {
            Route::get('/dashboard', [KalibrasiController::class, 'dashboardData'])->name('kalibrasi.data.dashboard');
            Route::get('/pressure', [KalibrasiPressureController::class, 'viewData'])->name('kalibrasi.data.pressure');
            Route::get('/volumetrik', [KalibrasiVolumetrikController::class, 'viewData'])->name('kalibrasi.data.volumetrik');
            Route::get('/temperature', [KalibrasiTemperatureController::class, 'viewData'])->name('kalibrasi.data.temperature');
            Route::get('/thermohygrometer', [KalibrasiThermohygrometerController::class, 'viewData'])->name('kalibrasi.data.thermohygrometer');
            Route::get('/jangka-sorong', [KalibrasiJangkaSorongController::class, 'viewData'])->name('kalibrasi.data.jangka-sorong');
            Route::get('/timbangan', [KalibrasiTimbanganController::class, 'viewData'])->name('kalibrasi.data.timbangan');
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
            Route::post('/store', [KalibrasiVolumetrikController::class, 'store'])->name('kalibrasi.volumetrik.store');
            Route::delete('/delete/{id}', [KalibrasiVolumetrikController::class, 'destroy'])->name('kalibrasi.volumetrik.delete');
        });
        Route::post('temperature/store', [KalibrasiTemperatureController::class, 'store'])->name('kalibrasi.temperature.store');
        Route::delete('temperature/delete/{id}', [KalibrasiTemperatureController::class, 'destroy'])->name('kalibrasi.temperature.delete');
        Route::post('thermohygrometer/store', [KalibrasiThermohygrometerController::class, 'store'])->name('kalibrasi.thermohygrometer.store');
        Route::delete('thermohygrometer/delete/{id}', [KalibrasiThermohygrometerController::class, 'destroy'])->name('kalibrasi.thermohygrometer.delete');
        Route::post('jangka-sorong/store', [KalibrasiJangkaSorongController::class, 'store'])->name('kalibrasi.jangka-sorong.store');
        Route::delete('jangka-sorong/delete/{id}', [KalibrasiJangkaSorongController::class, 'destroy'])->name('kalibrasi.jangka-sorong.delete');
        Route::post('timbangan/store', [KalibrasiTimbanganController::class, 'store'])->name('kalibrasi.timbangan.store');
        Route::delete('timbangan/delete/{id}', [KalibrasiTimbanganController::class, 'destroy'])->name('kalibrasi.timbangan.delete');
    });
});

// Not Operator
Route::middleware(['auth', 'access'])->group(function () {
    Route::prefix('kalibrasi')->group(function () {
        Route::prefix('master')->group(function () {
            Route::get('/alat', [KalibrasiController::class, 'viewMasterAlat'])->name('master.kalibrasi.alat');
            Route::post('/store/alat', [KalibrasiController::class, 'storeAlatKalibrasi'])->name('store.master.alat');
            Route::put('/update/alat/{id}', [KalibrasiController::class, 'updateAlatKalibrasi'])->name('update.master.alat');
            Route::delete('/delete/alat/{id}', [KalibrasiController::class, 'destroyAlatKalibrasi'])->name('delete.master.alat');
            Route::get('/download/template', [KalibrasiController::class, 'downloadTemplateAlatKalibrasi'])->name('master.download.template');
            Route::post('/import', [KalibrasiController::class, 'importAlatKalibrasi'])->name('master.import');

            // Pengukuran
            Route::get('/jangka-sorong/index', [KalibrasiMasterJangkaSorongController::class, 'viewJangkaSorong'])->name('master.kalibrasi.jangka_sorong');
            Route::get('/timbangan/index', [KalibrasiMasterTimbanganController::class, 'viewTimbangan'])->name('master.kalibrasi.timbangan');
            Route::resource('/jangka-sorong', KalibrasiMasterJangkaSorongController::class);
            Route::resource('/timbangan', KalibrasiMasterTimbanganController::class);
        });
    });
});

Route::get('/test-notif', function () {
    $approval = (object) [
        'approver_id' => 1,
        'approver' => (object) [
            'departmen' => 'warehouse',
        ],
        'sertifikat' => (object) [
            'created_at' => now(),
        ],
    ];

    \App\Helpers\NotificationHelper::pushToPortalUser($approval);

    return 'Notif test dikirim';
});
