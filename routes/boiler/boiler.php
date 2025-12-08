<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Boiler\BoilerController;
use App\Http\Controllers\Boiler\SensorBoilerController;
use App\Http\Controllers\Boiler\DashboardBoilerController;

Route::middleware(['auth', 'access'])->group(function () {
    Route::prefix('boiler')->group(function () {

        Route::get('/form', [BoilerController::class, 'viewForm'])->name('boiler.form');
        Route::get('/data', [BoilerController::class, 'viewData'])->name('boiler.data');
        Route::post('/store', [BoilerController::class, 'store'])->name('boiler.store');
        Route::get('/get-data', [BoilerController::class, 'getData'])->name('boiler.get-data');
        Route::get('/show/{id}', [BoilerController::class, 'show'])->name('boiler.show');
        Route::put('/update/{id}', [BoilerController::class, 'update'])->name('boiler.update');
        Route::delete('/delete/{id}', [BoilerController::class, 'destroy'])->name('boiler.destroy');

        // Dashboard
        Route::get('/dashboard', [DashboardBoilerController::class, 'index'])->name('dashboard.boiler.realtime');
        Route::get('/dashboard/kpi', [DashboardBoilerController::class, 'viewDashboardKpi'])->name('dashboard.boiler.kpi');
    });
});
