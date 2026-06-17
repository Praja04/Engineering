<?php

use App\Http\Controllers\Utility\CompressorController;
use Illuminate\Support\Facades\Route;

Route::prefix('utility/compressor')->as('compressor.')->group(function () {
    Route::get('/', [CompressorController::class, 'index'])->name('index');
    Route::get('/data', [CompressorController::class, 'dataView'])->name('data');
    Route::get('/approval', [CompressorController::class, 'approvalView'])->name('approval');

    // API Routes
    Route::post('/store', [CompressorController::class, 'store'])->name('store');
    Route::get('/get-data', [CompressorController::class, 'getData'])->name('get-data');
    Route::get('/export', [CompressorController::class, 'export'])->name('export');
    Route::get('/get-collected', [CompressorController::class, 'getCollectedData'])->name('get-collected');
    Route::get('/get-approval-data', [CompressorController::class, 'getApprovalData'])->name('get-approval-data');
    Route::get('/show/{id}', [CompressorController::class, 'show'])->name('show');
    Route::get('/show-weekly/{id}', [CompressorController::class, 'showWeeklyDetails'])->name('show-weekly');
    Route::post('/update/{id}', [CompressorController::class, 'update'])->name('update');
    Route::post('/submit-weekly', [CompressorController::class, 'submitWeekly'])->name('submit-weekly');
    Route::delete('/destroy/{id}', [CompressorController::class, 'destroy'])->name('destroy');

    // Approval Actions
    Route::post('/approve-foreman/{id}', [CompressorController::class, 'approveForeman'])->name('approve-foreman');
    Route::post('/approve-supervisor/{id}', [CompressorController::class, 'approveSupervisor'])->name('approve-supervisor');
    Route::post('/reject/{id}', [CompressorController::class, 'reject'])->name('reject');
    Route::post('/bulk-approve', [CompressorController::class, 'bulkApprove'])->name('bulk-approve');
    Route::post('/bulk-reject', [CompressorController::class, 'bulkReject'])->name('bulk-reject');
});
