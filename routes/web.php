<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KalibrasController;
use App\Http\Controllers\Kalibrasi\KalibrasiController;
use App\Http\Controllers\ScoringMesin\DashboardController;
use App\Http\Controllers\Kalibrasi\KalibrasiPressureController;


Route::get('/', [AuthController::class, 'showLoginForm'])->name('home');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


///////////   View Routes   ///////////
Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    // Kalibrasi Routes
    Route::prefix('kalibrasi')->group(function () {

        // Master Alat Kalibrasi
        Route::get('/master/alat', [KalibrasiController::class, 'viewMasterAlat'])->name('master.alat');
        Route::post('/store/master/alat', [KalibrasiController::class, 'storeAlatKalibrasi'])->name('store.master.alat');
        Route::put('/update/master/alat/{id}', [KalibrasiController::class, 'updateAlatKalibrasi'])->name('update.master.alat');
        Route::delete('/delete/master/alat/{id}', [KalibrasiController::class, 'destroyAlatKalibrasi'])->name('delete.master.alat');
        Route::get('/master/download/template', [KalibrasiController::class, 'downloadTemplateAlatKalibrasi'])->name('master.download.template');
        Route::post('/master/import', [KalibrasiController::class, 'importAlatKalibrasi'])->name('master.import');

        Route::get('/schedule', [KalibrasiController::class, 'viewSchedule'])->name('kalibrasi.schedule');
        Route::get('/certificate', [KalibrasiController::class, 'viewCertificate'])->name('kalibrasi.certificate');
        Route::post('/certificate/req-approval/{id}', [KalibrasiController::class, 'reqApprovalStore'])->name('kalibrasi.certificate.req-approval');

        // Pressure Routes
        Route::prefix('pressure')->group(function () {
            Route::get('/index', [KalibrasiPressureController::class, 'index'])->name('kalibrasi.pressure.index');
            Route::post('/store', [KalibrasiPressureController::class, 'store'])->name('kalibrasi.pressure.store');
            Route::get('/data', [KalibrasiPressureController::class, 'viewData'])->name('kalibrasi.pressure.data');
            Route::delete('/delete/{id}', [KalibrasiPressureController::class, 'destroy'])->name('kalibrasi.pressure.delete');
        });
    });
});
//////////    End View Routes   ///////////
