<?php

use App\Http\Controllers\Utility\AhuController;
use Illuminate\Support\Facades\Route;

Route::prefix('utility/ahu')->as('ahu.')->group(function () {
    Route::get('/', [AhuController::class, 'index'])->name('index');
    Route::get('/data', [AhuController::class, 'dataView'])->name('data');
    Route::get('/approval', [AhuController::class, 'approvalView'])->name('approval');

    // API Routes
    Route::post('/store', [AhuController::class, 'store'])->name('store');
    Route::get('/get-data', [AhuController::class, 'getData'])->name('get-data');
    Route::get('/export', [AhuController::class, 'export'])->name('export');
    Route::get('/get-collected', [AhuController::class, 'getCollectedData'])->name('get-collected');
    Route::get('/get-approval-data', [AhuController::class, 'getApprovalData'])->name('get-approval-data');
    Route::get('/show/{id}', [AhuController::class, 'show'])->name('show');
    Route::get('/show-monthly/{id}', [AhuController::class, 'showMonthlyDetails'])->name('show-monthly');
    Route::post('/update/{id}', [AhuController::class, 'update'])->name('update');
    Route::delete('/destroy/{id}', [AhuController::class, 'destroy'])->name('destroy');
    Route::post('/submit-monthly', [AhuController::class, 'submitMonthly'])->name('submit-monthly');

    // Approval Actions
    Route::post('/approve-foreman/{id}', [AhuController::class, 'approveForeman'])->name('approve-foreman');
    Route::post('/approve-supervisor/{id}', [AhuController::class, 'approveSupervisor'])->name('approve-supervisor');
    Route::post('/reject/{id}', [AhuController::class, 'reject'])->name('reject');
});
