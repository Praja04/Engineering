<?php

use App\Http\Controllers\Maintenance\MtcApprovalController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Utility\KpiController;
use App\Http\Controllers\Maintenance\MtcAgendaController;
use App\Http\Controllers\Maintenance\MtcSipilController;
use App\Http\Controllers\Maintenance\MtcBatteryController;
use App\Http\Controllers\Maintenance\MtcUtilityController;
use App\Http\Controllers\Maintenance\MtcDieselP2hController;
use App\Http\Controllers\Maintenance\MtcMotorPumpController;
use App\Http\Controllers\Maintenance\MtcElectricalController;
use App\Http\Controllers\Maintenance\MtcElectricP2hController;
use App\Http\Controllers\Maintenance\MtcRefrigerasiController;
use App\Http\Controllers\Maintenance\MtcDieselEngineController;
use App\Http\Controllers\Maintenance\MtcElectricEngineController;
use App\Http\Controllers\Maintenance\MtcMainController;
use App\Http\Controllers\Maintenance\MtcMasterMesinController;

Route::middleware(['auth'])->group(function () {
    Route::prefix('mtc')->group(function () {
        Route::get('/agenda', [MtcAgendaController::class, 'index'])->name('agenda.index');
        Route::prefix('form')->group(function () {
            Route::prefix('motor-pump')->group(function () {
                Route::get('/index', [MtcMotorPumpController::class, 'index'])->name('mtc.motor-pump.index');
                Route::post('/store', [MtcMotorPumpController::class, 'store'])->name('mtc.motor-pump.store');
            });
            Route::prefix('utility')->group(function () {
                Route::get('/index', [MtcUtilityController::class, 'index'])->name('mtc.utility.index');
                Route::post('/store', [MtcUtilityController::class, 'store'])->name('mtc.utility.store');
            });
            Route::prefix('electrical')->group(function () {
                Route::get('/index', [MtcElectricalController::class, 'index'])->name('mtc.electrical.index');
                Route::post('/store', [MtcElectricalController::class, 'store'])->name('mtc.electrical.store');
            });
            Route::prefix('refrigerasi')->group(function () {
                Route::get('/index', [MtcRefrigerasiController::class, 'index'])->name('mtc.refrigerasi.index');
                Route::post('/store', [MtcRefrigerasiController::class, 'store'])->name('mtc.refrigerasi.store');
            });
            Route::prefix('electric-engine')->group(function () {
                Route::get('/index', [MtcElectricEngineController::class, 'index'])->name('mtc.electric-engine.index');
                Route::post('/store', [MtcElectricEngineController::class, 'store'])->name('mtc.electric-engine.store');
            });
            Route::prefix('diesel-engine')->group(function () {
                Route::get('/index', [MtcDieselEngineController::class, 'index'])->name('mtc.diesel-engine.index');
                Route::post('/store', [MtcDieselEngineController::class, 'store'])->name('mtc.diesel-engine.store');
            });
            Route::prefix('sipil')->group(function () {
                Route::get('/index', [MtcSipilController::class, 'index'])->name('mtc.sipil.index');
                Route::post('/store', [MtcSipilController::class, 'store'])->name('mtc.sipil.store');
            });
            Route::prefix('battery')->group(function () {
                Route::get('/index', [MtcBatteryController::class, 'index'])->name('mtc.battery.index');
                Route::post('/store', [MtcBatteryController::class, 'store'])->name('mtc.battery.store');
            });
            Route::prefix('electric-p2h')->group(function () {
                Route::get('/index', [MtcElectricP2hController::class, 'index'])->name('mtc.electric-p2h.index');
                Route::post('/store', [MtcElectricP2hController::class, 'store'])->name('mtc.electric-p2h.store');
            });
            Route::prefix('diesel-p2h')->group(function () {
                Route::get('/index', [MtcDieselP2hController::class, 'index'])->name('mtc.diesel-p2h.index');
                Route::post('/store', [MtcDieselP2hController::class, 'store'])->name('mtc.diesel-p2h.store');
            });
        });

        Route::prefix('main')->group(function () {
            Route::get('/tracking/{id}', [MtcMainController::class, 'tracking'])->name('mtc.tracking.data.tracking');
            Route::delete('/delete/{id}', [MtcMainController::class, 'destroy'])->name('mtc.main.data.delete');
        });

        Route::prefix('data')->group(function () {
            Route::prefix('motor-pump')->group(function () {
                Route::get('/index', [MtcMotorPumpController::class, 'viewData'])->name('mtc.motor-pump.data.index');
                Route::post('/update/{id}', [MtcMotorPumpController::class, 'update'])->name('mtc.motor-pump.data.update');
            });
            Route::prefix('utility')->group(function () {
                Route::get('/index', [MtcUtilityController::class, 'viewData'])->name('mtc.utility.data.index');
                Route::post('/update/{id}', [MtcUtilityController::class, 'update'])->name('mtc.utility.data.update');
            });
            Route::prefix('electrical')->group(function () {
                Route::get('/index', [MtcElectricalController::class, 'viewData'])->name('mtc.electrical.data.index');
                Route::post('/update/{id}', [MtcElectricalController::class, 'update'])->name('mtc.electrical.data.update');
            });
            Route::prefix('refrigerasi')->group(function () {
                Route::get('/index', [MtcRefrigerasiController::class, 'viewData'])->name('mtc.refrigerasi.data.index');
                Route::post('/update/{id}', [MtcRefrigerasiController::class, 'update'])->name('mtc.refrigerasi.data.update');
            });
            Route::prefix('electric-engine')->group(function () {
                Route::get('/index', [MtcElectricEngineController::class, 'viewData'])->name('mtc.electric-engine.data.index');
                Route::post('/update/{id}', [MtcElectricEngineController::class, 'update'])->name('mtc.electric-engine.data.update');
            });
            Route::prefix('diesel-engine')->group(function () {
                Route::get('/index', [MtcDieselEngineController::class, 'viewData'])->name('mtc.diesel-engine.data.index');
                Route::post('/update/{id}', [MtcDieselEngineController::class, 'update'])->name('mtc.diesel-engine.data.update');
            });
            Route::prefix('sipil')->group(function () {
                Route::get('/index', [MtcSipilController::class, 'viewData'])->name('mtc.sipil.data.index');
                Route::post('/update/{id}', [MtcSipilController::class, 'update'])->name('mtc.sipil.data.update');
            });
            Route::prefix('battery')->group(function () {
                Route::get('/index', [MtcBatteryController::class, 'viewData'])->name('mtc.battery.data.index');
                Route::post('/update/{id}', [MtcBatteryController::class, 'update'])->name('mtc.battery.data.update');
            });
            Route::prefix('electric-p2h')->group(function () {
                Route::get('/index', [MtcElectricP2hController::class, 'viewData'])->name('mtc.electric-p2h.data.index');
                Route::post('/update/{id}', [MtcElectricP2hController::class, 'update'])->name('mtc.electric-p2h.data.update');
            });
            Route::prefix('diesel-p2h')->group(function () {
                Route::get('/index', [MtcDieselP2hController::class, 'viewData'])->name('mtc.diesel-p2h.data.index');
                Route::post('/update/{id}', [MtcDieselP2hController::class, 'update'])->name('mtc.diesel-p2h.data.update');
            });
        });

        Route::prefix('approval')->group(function () {
            Route::get('/index', [MtcApprovalController::class, 'index'])->name('mtc.approval.index');
            Route::get('/show', [MtcApprovalController::class, 'show'])->name('mtc.approval.show');
            Route::get('/detail/{main}', [MtcApprovalController::class, 'detail']);
            Route::post('/approve/{id}', [MtcApprovalController::class, 'approve'])->name('mtc.approval.approve');
            Route::post('/reject/{id}', [MtcApprovalController::class, 'reject'])->name('mtc.approval.reject');
        });

        Route::prefix('master')->group(function () {
            Route::prefix('mesin')->group(function () {
                Route::get('/index', [MtcMasterMesinController::class, 'index'])->name('master.mtc.mesin.index');
                Route::post('/store', [MtcMasterMesinController::class, 'store'])->name('master.mtc.mesin.store');
                Route::delete('/delete/{id}', [MtcMasterMesinController::class, 'destroy'])->name('master.mtc.mesin.delete');
                Route::post('/update/{id}', [MtcMasterMesinController::class, 'update'])->name('master.mtc.mesin.update');
                Route::get('/download-template', [MtcMasterMesinController::class, 'downloadTemplate'])->name('downloadTemplate');
                Route::post('/upload-excel',     [MtcMasterMesinController::class, 'uploadExcel'])->name('uploadExcel');
            });
        });

        // Route for downloading maintenance data
        Route::get('/download-data/{jenis_mtc}', [MtcMainController::class, 'downloadMaintenanceData'])->name('mtc.download.data');
    });
});
