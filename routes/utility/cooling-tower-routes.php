<?php

use App\Http\Controllers\Utility\CoolingTowerController;
use Illuminate\Support\Facades\Route;

Route::prefix('utility/cooling-tower')->as('cooling-tower.')->group(function () {
    Route::get('/', [CoolingTowerController::class, 'index'])->name('index');
    Route::get('/data', [CoolingTowerController::class, 'dataView'])->name('data');
    Route::get('/approval', [CoolingTowerController::class, 'approvalView'])->name('approval');

    // API Routes
    Route::post('/store', [CoolingTowerController::class, 'store'])->name('store');
    Route::get('/get-data', [CoolingTowerController::class, 'getData'])->name('get-data');
    Route::get('/export', [CoolingTowerController::class, 'export'])->name('export');
    Route::get('/get-collected', [CoolingTowerController::class, 'getCollectedData'])->name('get-collected');
    Route::get('/get-approval-data', [CoolingTowerController::class, 'getApprovalData'])->name('get-approval-data');
    Route::get('/show/{id}', [CoolingTowerController::class, 'show'])->name('show');
    Route::get('/show-monthly/{id}', [CoolingTowerController::class, 'showMonthlyDetails'])->name('show-monthly');
    Route::post('/update/{id}', [CoolingTowerController::class, 'update'])->name('update');
    Route::post('/submit-monthly', [CoolingTowerController::class, 'submitMonthly'])->name('submit-monthly');
    Route::delete('/destroy/{id}', [CoolingTowerController::class, 'destroy'])->name('destroy');

    // Approval Actions
    Route::post('/approve-foreman/{id}', [CoolingTowerController::class, 'approveForeman'])->name('approve-foreman');
    Route::post('/approve-supervisor/{id}', [CoolingTowerController::class, 'approveSupervisor'])->name('approve-supervisor');
    Route::post('/reject/{id}', [CoolingTowerController::class, 'reject'])->name('reject');
    Route::post('/bulk-approve', [CoolingTowerController::class, 'bulkApprove'])->name('bulk-approve');
    Route::post('/bulk-reject', [CoolingTowerController::class, 'bulkReject'])->name('bulk-reject');
});
