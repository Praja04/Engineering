<?php

use App\Http\Controllers\Utility\PemantauanPompaUtilityController;
use Illuminate\Support\Facades\Route;

Route::prefix('utility/pemantauan-pompa-utility')->as('pemantauan-pompa-utility.')->group(function () {
    Route::get('/', [PemantauanPompaUtilityController::class, 'index'])->name('index');
    Route::get('/data', [PemantauanPompaUtilityController::class, 'dataView'])->name('data');
    Route::get('/approval', [PemantauanPompaUtilityController::class, 'approvalView'])->name('approval');

    // API Routes
    Route::post('/store', [PemantauanPompaUtilityController::class, 'store'])->name('store');
    Route::get('/get-data', [PemantauanPompaUtilityController::class, 'getData'])->name('get-data');
    Route::get('/export', [PemantauanPompaUtilityController::class, 'export'])->name('export');
    Route::get('/get-collected', [PemantauanPompaUtilityController::class, 'getCollectedData'])->name('get-collected');
    Route::get('/get-approval-data', [PemantauanPompaUtilityController::class, 'getApprovalData'])->name('get-approval-data');
    Route::get('/show/{id}', [PemantauanPompaUtilityController::class, 'show'])->name('show');
    Route::get('/show-monthly/{id}', [PemantauanPompaUtilityController::class, 'showMonthlyDetails'])->name('show-monthly');
    Route::post('/update/{id}', [PemantauanPompaUtilityController::class, 'update'])->name('update');
    Route::delete('/destroy/{id}', [PemantauanPompaUtilityController::class, 'destroy'])->name('destroy');
    Route::post('/submit-monthly', [PemantauanPompaUtilityController::class, 'submitMonthly'])->name('submit-monthly');

    // Approval Actions
    Route::post('/approve-foreman/{id}', [PemantauanPompaUtilityController::class, 'approveForeman'])->name('approve-foreman');
    Route::post('/approve-supervisor/{id}', [PemantauanPompaUtilityController::class, 'approveSupervisor'])->name('approve-supervisor');
    Route::post('/reject/{id}', [PemantauanPompaUtilityController::class, 'reject'])->name('reject');
});
