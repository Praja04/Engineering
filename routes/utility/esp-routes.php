<?php

use App\Http\Controllers\Utility\EspShiftReportController;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Utility\EspOperationalReportController;
use App\Http\Controllers\Utility\EspCoalHandoverController;

Route::prefix('utility/esp-operational-report')->middleware('auth')->name('esp-operational-report.')->group(function () {
    Route::get('/',         [EspOperationalReportController::class, 'index'])->name('index');     // form input
    Route::get('/data',     [EspOperationalReportController::class, 'dataView'])->name('data');   // rekap & export
    Route::get('/json',     [EspOperationalReportController::class, 'getData'])->name('json');    // API JSON
    Route::post('/store',   [EspOperationalReportController::class, 'store'])->name('store');
    Route::get('/export',   [EspOperationalReportController::class, 'export'])->name('export');
});


Route::prefix('utility/esp-shift-report')->middleware('auth')->name('esp-shift-report.')->group(function () {
    Route::get('/',                          [EspShiftReportController::class, 'index'])->name('index');           // form input (landing + toggle)
    Route::get('/json',                      [EspShiftReportController::class, 'getData'])->name('json');          // API JSON untuk tabel data & approval
    Route::post('/store',                    [EspShiftReportController::class, 'store'])->name('store');           // submit operator
    Route::post('/mass-approve',             [EspShiftReportController::class, 'massApprove'])->name('mass-approve');
    Route::put('/{id}',                      [EspShiftReportController::class, 'update'])->name('update');         // edit non-operator
    Route::post('/{id}/approve-foreman',     [EspShiftReportController::class, 'approveForeman'])->name('approve-foreman');
    Route::post('/{id}/approve-supervisor',  [EspShiftReportController::class, 'approveSupervisor'])->name('approve-supervisor');
    Route::get('/approval',                  [EspShiftReportController::class, 'approvalView'])->name('approval'); // halaman approval
});


Route::prefix('utility/esp-coal-handover')->middleware('auth')->name('esp-coal-handover.')->group(function () {
    Route::get('/json',     [EspCoalHandoverController::class, 'getData'])->name('json');
    Route::post('/store',   [EspCoalHandoverController::class, 'store'])->name('store');
    Route::put('/{id}',     [EspCoalHandoverController::class, 'update'])->name('update');
});