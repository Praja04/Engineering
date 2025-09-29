<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KalibrasController;
use App\Http\Controllers\Kalibrasi\KalibrasiController;
use App\Http\Controllers\ScoringMesin\DashboardController;
use App\Http\Controllers\Kalibrasi\KalibrasiPressureController;

use App\Http\Controllers\ScoringMesin\MachineController;
use App\Http\Controllers\ScoringMesin\ProcessParameterController;
use App\Http\Controllers\ScoringMesin\SectionController;
use App\Http\Controllers\ScoringMesin\PartController;
use App\Http\Controllers\ScoringMesin\StandardStateController;

Route::get('/', [AuthController::class, 'showLoginForm'])->name('home');
// Route::get('/mesin', [DashboardController::class, 'master_mesin']);
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


///////////   View Routes   ///////////
Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    // Kalibrasi Routes
    Route::prefix('kalibrasi')->group(function () {

        // Maste Alat Kalibrasi
        Route::get('/master/alat', [KalibrasiController::class, 'viewMasterAlat'])->name('master.alat');
        Route::post('/store/master/alat', [KalibrasiController::class, 'storeAlatKalibrasi'])->name('store.master.alat');
        Route::put('/update/master/alat/{id}', [KalibrasiController::class, 'updateAlatKalibrasi'])->name('update.master.alat');
        Route::delete('/delete/master/alat/{id}', [KalibrasiController::class, 'destroyAlatKalibrasi'])->name('delete.master.alat');
        Route::get('/master/download/template', [KalibrasiController::class, 'downloadTemplateAlatKalibrasi'])->name('master.download.template');
        Route::post('/master/import', [KalibrasiController::class, 'importAlatKalibrasi'])->name('master.import');

        // pressure routes
        Route::prefix('pressure')->group(function () {
            Route::get('/index', [KalibrasiPressureController::class, 'index'])->name('kalibrasi.pressure.index');
        });
    });
});



//////////    Scoring Mesin Routes   ///////////
Route::prefix('scoring-mesin')->name('scoring-mesin.')->group(function () {
    // Machine routes
    Route::get('machines/statistics', [MachineController::class, 'statistics'])->name('machines.statistics');
    Route::resource('machines', MachineController::class);

    // Other resources
    Route::resource('process-parameters', ProcessParameterController::class);
    Route::resource('sections', SectionController::class);
    Route::resource('parts', PartController::class);
    Route::resource('standard-states', StandardStateController::class);
});

//////////    End Scoring Mesin Routes   ///////////



//////////    End View Routes   ///////////
