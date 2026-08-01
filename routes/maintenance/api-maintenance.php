<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Maintenance\MtcSipilController;
use App\Http\Controllers\Maintenance\MtcBatteryController;
use App\Http\Controllers\Maintenance\MtcUtilityController;
use App\Http\Controllers\Maintenance\MtcDieselP2hController;
use App\Http\Controllers\Maintenance\MtcMotorPumpController;
use App\Http\Controllers\Maintenance\MtcElectricalController;
use App\Http\Controllers\Maintenance\MtcElectricP2hController;
use App\Http\Controllers\Maintenance\MtcP2hController;
use App\Http\Controllers\Maintenance\MtcRefrigerasiController;
use App\Http\Controllers\Maintenance\MtcDieselEngineController;
use App\Http\Controllers\Maintenance\MtcElectricEngineController;
use App\Http\Controllers\Maintenance\MtcMainController;
use App\Http\Controllers\Maintenance\MtcMasterMesinController;

Route::prefix('mtc')->group(function () {
    Route::post('/store', [MtcMotorPumpController::class, 'store'])->name('mtc.store');
    Route::get('/motor-pump/get-data', [MtcMotorPumpController::class, 'getData']);
    Route::get('/utility/get-data', [MtcUtilityController::class, 'getData']);
    Route::get('/electrical/get-data', [MtcElectricalController::class, 'getData']);
    Route::get('/refrigerasi/get-data', [MtcRefrigerasiController::class, 'getData']);
    Route::get('/electric-engine/get-data', [MtcElectricEngineController::class, 'getData']);
    Route::get('/diesel-engine/get-data', [MtcDieselEngineController::class, 'getData']);
    Route::get('/sipil/get-data', [MtcSipilController::class, 'getData']);
    Route::get('/battery/get-data', [MtcBatteryController::class, 'getData']);
    Route::get('/electric-p2h/get-data', [MtcElectricP2hController::class, 'getData']);
    Route::get('/diesel-p2h/get-data', [MtcDieselP2hController::class, 'getData']);
    Route::get('/p2h/get-data', [MtcP2hController::class, 'getData']);
    Route::get('/master/mesin/get-data', [MtcMasterMesinController::class, 'getData']);
    Route::get('/users/approvers', [MtcMainController::class, 'getApprovers']);
});
