<?php

use App\Http\Controllers\Utility\KpiController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::prefix('kpi')->group(function () {
        Route::get('/form', [KpiController::class, 'viewForm'])->name('kpi.form');
        Route::get('/data', [KpiController::class, 'viewData'])->name('kpi.data');
        Route::post('/store', [KpiController::class, 'store'])->name('kpi.store');
        Route::get('/get-data', [KpiController::class, 'getData'])->name('kpi.get-data');
        Route::get('/show/{id}', [KpiController::class, 'show'])->name('kpi.show');
        Route::put('/update/{id}', [KpiController::class, 'update'])->name('kpi.update');
        Route::delete('/delete/{id}', [KpiController::class, 'destroy'])->name('kpi.destroy');
    });
});
