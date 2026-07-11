<?php

use App\Http\Controllers\Utility\AgendaCoolingTowerController;
use Illuminate\Support\Facades\Route;

Route::prefix('utility/agenda-cooling-tower')->as('agenda-cooling-tower.')->group(function () {
    Route::get('/', [AgendaCoolingTowerController::class, 'index'])->name('index');
    Route::get('/data', [AgendaCoolingTowerController::class, 'dataView'])->name('data');
    Route::get('/approval', [AgendaCoolingTowerController::class, 'approvalView'])->name('approval');

    // API Routes
    Route::post('/store', [AgendaCoolingTowerController::class, 'store'])->name('store');
    Route::get('/get-data', [AgendaCoolingTowerController::class, 'getData'])->name('get-data');
    Route::get('/export', [AgendaCoolingTowerController::class, 'export'])->name('export');
    Route::get('/get-collected', [AgendaCoolingTowerController::class, 'getCollectedData'])->name('get-collected');
    Route::get('/get-approval-data', [AgendaCoolingTowerController::class, 'getApprovalData'])->name('get-approval-data');
    Route::get('/show/{id}', [AgendaCoolingTowerController::class, 'show'])->name('show');
    Route::get('/show-monthly/{id}', [AgendaCoolingTowerController::class, 'showMonthlyDetails'])->name('show-monthly');
    Route::post('/update/{id}', [AgendaCoolingTowerController::class, 'update'])->name('update');
    Route::delete('/destroy/{id}', [AgendaCoolingTowerController::class, 'destroy'])->name('destroy');
    Route::post('/submit-monthly', [AgendaCoolingTowerController::class, 'submitMonthly'])->name('submit-monthly');

    // Approval Actions
    Route::post('/approve-foreman/{id}', [AgendaCoolingTowerController::class, 'approveForeman'])->name('approve-foreman');
    Route::post('/approve-supervisor/{id}', [AgendaCoolingTowerController::class, 'approveSupervisor'])->name('approve-supervisor');
    Route::post('/reject/{id}', [AgendaCoolingTowerController::class, 'reject'])->name('reject');
});
