<?php

use App\Http\Controllers\Utility\AnalisisUtilityController;
use Illuminate\Support\Facades\Route;

Route::prefix('utility/analisis-utility')->as('analisis-utility.')->group(function () {
    Route::get('/', [AnalisisUtilityController::class, 'index'])->name('index');
    Route::get('/data', [AnalisisUtilityController::class, 'dataView'])->name('data');
    Route::get('/approval', [AnalisisUtilityController::class, 'approvalView'])->name('approval');

    // API Routes
    Route::post('/store', [AnalisisUtilityController::class, 'store'])->name('store');
    Route::get('/get-data', [AnalisisUtilityController::class, 'getData'])->name('get-data');
    Route::get('/export', [AnalisisUtilityController::class, 'export'])->name('export');
    Route::get('/get-collected', [AnalisisUtilityController::class, 'getCollectedData'])->name('get-collected');
    Route::get('/get-approval-data', [AnalisisUtilityController::class, 'getApprovalData'])->name('get-approval-data');
    Route::get('/show/{id}', [AnalisisUtilityController::class, 'show'])->name('show');
    Route::get('/show-monthly/{id}', [AnalisisUtilityController::class, 'showMonthlyDetails'])->name('show-monthly');
    Route::post('/update/{id}', [AnalisisUtilityController::class, 'update'])->name('update');
    Route::delete('/destroy/{id}', [AnalisisUtilityController::class, 'destroy'])->name('destroy');
    Route::post('/submit-monthly', [AnalisisUtilityController::class, 'submitMonthly'])->name('submit-monthly');

    // Approval Actions
    Route::post('/approve-foreman/{id}', [AnalisisUtilityController::class, 'approveForeman'])->name('approve-foreman');
    Route::post('/approve-supervisor/{id}', [AnalisisUtilityController::class, 'approveSupervisor'])->name('approve-supervisor');
    Route::post('/reject/{id}', [AnalisisUtilityController::class, 'reject'])->name('reject');
});
