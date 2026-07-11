<?php

use App\Http\Controllers\Utility\ReverseOsmosisController;
use Illuminate\Support\Facades\Route;

Route::prefix('utility/reverse-osmosis')->as('reverse-osmosis.')->group(function () {
    Route::get('/', [ReverseOsmosisController::class, 'index'])->name('index');
    Route::get('/data', [ReverseOsmosisController::class, 'dataView'])->name('data');
    Route::get('/approval', [ReverseOsmosisController::class, 'approvalView'])->name('approval');

    // API Routes
    Route::post('/store', [ReverseOsmosisController::class, 'store'])->name('store');
    Route::get('/get-data', [ReverseOsmosisController::class, 'getData'])->name('get-data');
    Route::get('/export', [ReverseOsmosisController::class, 'export'])->name('export');
    Route::get('/get-collected', [ReverseOsmosisController::class, 'getCollectedData'])->name('get-collected');
    Route::get('/get-approval-data', [ReverseOsmosisController::class, 'getApprovalData'])->name('get-approval-data');
    Route::get('/show/{id}', [ReverseOsmosisController::class, 'show'])->name('show');
    Route::get('/show-monthly/{id}', [ReverseOsmosisController::class, 'showMonthlyDetails'])->name('show-monthly');
    Route::post('/update/{id}', [ReverseOsmosisController::class, 'update'])->name('update');
    Route::post('/submit-monthly', [ReverseOsmosisController::class, 'submitMonthly'])->name('submit-monthly');
    Route::delete('/destroy/{id}', [ReverseOsmosisController::class, 'destroy'])->name('destroy');

    // Approval Actions
    Route::post('/approve-foreman/{id}', [ReverseOsmosisController::class, 'approveForeman'])->name('approve-foreman');
    Route::post('/approve-supervisor/{id}', [ReverseOsmosisController::class, 'approveSupervisor'])->name('approve-supervisor');
    Route::post('/reject/{id}', [ReverseOsmosisController::class, 'reject'])->name('reject');
    Route::post('/bulk-approve', [ReverseOsmosisController::class, 'bulkApprove'])->name('bulk-approve');
    Route::post('/bulk-reject', [ReverseOsmosisController::class, 'bulkReject'])->name('bulk-reject');
});
