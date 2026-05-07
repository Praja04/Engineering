<?php

use App\Http\Controllers\Utility\MdpMonitoringController;
use Illuminate\Support\Facades\Route;

Route::prefix('utility/mdp-monitoring')->name('mdp-monitoring.')->group(function () {
    Route::get('/', [MdpMonitoringController::class, 'index'])->name('index');     // form input
    Route::get('/data', [MdpMonitoringController::class, 'dataView'])->name('data');   // rekap & export
    Route::get('/json', [MdpMonitoringController::class, 'getData'])->name('json');    // API JSON
    Route::get('/json/{id}', [MdpMonitoringController::class, 'show'])->name('show');  // Detail JSON
    Route::post('/store', [MdpMonitoringController::class, 'store'])->name('store');
    Route::get('/approval', [MdpMonitoringController::class, 'approvalView'])->name('approval');
    Route::post('/approve-foreman/{id}', [MdpMonitoringController::class, 'approveForeman'])->name('approve-foreman');
    Route::post('/approve-supervisor/{id}', [MdpMonitoringController::class, 'approveSupervisor'])->name('approve-supervisor');
    Route::post('/reject/{id}', [MdpMonitoringController::class, 'reject'])->name('reject');
    Route::post('/bulk-approve', [MdpMonitoringController::class, 'bulkApprove'])->name('bulk-approve');
    Route::post('/bulk-reject', [MdpMonitoringController::class, 'bulkReject'])->name('bulk-reject');
    Route::get('/export', [MdpMonitoringController::class, 'export'])->name('export');
    Route::put('/update/{id}', [MdpMonitoringController::class, 'update'])->name('update');
});
