<?php

use App\Http\Controllers\Utility\AgendaCompressorController;
use Illuminate\Support\Facades\Route;

Route::prefix('utility/agenda-compressor')->as('agenda-compressor.')->group(function () {
    Route::get('/', [AgendaCompressorController::class, 'index'])->name('index');
    Route::get('/data', [AgendaCompressorController::class, 'dataView'])->name('data');
    Route::get('/approval', [AgendaCompressorController::class, 'approvalView'])->name('approval');

    // API Routes
    Route::post('/store', [AgendaCompressorController::class, 'store'])->name('store');
    Route::get('/get-data', [AgendaCompressorController::class, 'getData'])->name('get-data');
    Route::get('/export', [AgendaCompressorController::class, 'export'])->name('export');
    Route::get('/get-collected', [AgendaCompressorController::class, 'getCollectedData'])->name('get-collected');
    Route::get('/get-approval-data', [AgendaCompressorController::class, 'getApprovalData'])->name('get-approval-data');
    Route::get('/show/{id}', [AgendaCompressorController::class, 'show'])->name('show');
    Route::get('/show-monthly/{id}', [AgendaCompressorController::class, 'showMonthlyDetails'])->name('show-monthly');
    Route::post('/update/{id}', [AgendaCompressorController::class, 'update'])->name('update');
    Route::delete('/destroy/{id}', [AgendaCompressorController::class, 'destroy'])->name('destroy');
    Route::post('/submit-monthly', [AgendaCompressorController::class, 'submitMonthly'])->name('submit-monthly');

    // Approval Actions
    Route::post('/approve-foreman/{id}', [AgendaCompressorController::class, 'approveForeman'])->name('approve-foreman');
    Route::post('/approve-supervisor/{id}', [AgendaCompressorController::class, 'approveSupervisor'])->name('approve-supervisor');
    Route::post('/reject/{id}', [AgendaCompressorController::class, 'reject'])->name('reject');
});
