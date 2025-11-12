<?php

use App\Http\Controllers\Boiler\BoilerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Boiler\DashboardController;
use App\Http\Controllers\Boiler\SensorBoilerController;

Route::middleware(['auth', 'access'])->group(function () {
    Route::prefix('boiler')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('boiler.dashboard');
        Route::get('/form', [BoilerController::class, 'viewForm'])->name('boiler.form');
        Route::get('/data', [BoilerController::class, 'viewData'])->name('boiler.data');
        Route::post('/store', [BoilerController::class, 'store'])->name('boiler.store');
        Route::get('/get-data', [BoilerController::class, 'getData'])->name('boiler.get-data');
        Route::get('/show/{id}', [BoilerController::class, 'show'])->name('boiler.show');
        Route::put('/update/{id}', [BoilerController::class, 'update'])->name('boiler.update');
        Route::delete('/delete/{id}', [BoilerController::class, 'destroy'])->name('boiler.destroy');
    });
});
