<?php

use App\Http\Controllers\Utility\AgendaTankFarmController;
use Illuminate\Support\Facades\Route;

Route::prefix('utility/agenda-tank-farm')->as('agenda-tank-farm.')->group(function () {
    Route::get('/', [AgendaTankFarmController::class, 'index'])->name('index');
    Route::get('/data', [AgendaTankFarmController::class, 'dataView'])->name('data');
    Route::get('/approval', [AgendaTankFarmController::class, 'approvalView'])->name('approval');

    // API Routes
    Route::post('/store', [AgendaTankFarmController::class, 'store'])->name('store');
    Route::get('/get-data', [AgendaTankFarmController::class, 'getData'])->name('get-data');
    Route::get('/export', [AgendaTankFarmController::class, 'export'])->name('export');
    Route::get('/get-collected', [AgendaTankFarmController::class, 'getCollectedData'])->name('get-collected');
    Route::get('/get-approval-data', [AgendaTankFarmController::class, 'getApprovalData'])->name('get-approval-data');
    Route::get('/show/{id}', [AgendaTankFarmController::class, 'show'])->name('show');
    Route::get('/show-monthly/{id}', [AgendaTankFarmController::class, 'showMonthlyDetails'])->name('show-monthly');
    Route::post('/update/{id}', [AgendaTankFarmController::class, 'update'])->name('update');
    Route::post('/submit-monthly', [AgendaTankFarmController::class, 'submitMonthly'])->name('submit-monthly');
    Route::delete('/destroy/{id}', [AgendaTankFarmController::class, 'destroy'])->name('destroy');

    // Approval Actions
    Route::post('/approve-foreman/{id}', [AgendaTankFarmController::class, 'approveForeman'])->name('approve-foreman');
    Route::post('/approve-supervisor/{id}', [AgendaTankFarmController::class, 'approveSupervisor'])->name('approve-supervisor');
    Route::post('/reject/{id}', [AgendaTankFarmController::class, 'reject'])->name('reject');
    Route::post('/bulk-approve', [AgendaTankFarmController::class, 'bulkApprove'])->name('bulk-approve');
    Route::post('/bulk-reject', [AgendaTankFarmController::class, 'bulkReject'])->name('bulk-reject');
});
