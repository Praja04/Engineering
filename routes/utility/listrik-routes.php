<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Utility\ListrikController;
use App\Http\Controllers\Utility\UtilityApprovalController;

Route::prefix('utility')->middleware('auth')->group(function () {
    Route::get('/form', [ListrikController::class, 'formUtility']);
    Route::get('/data', [ListrikController::class, 'DataUtility']);
    Route::post('/data/listrik/store', [ListrikController::class, 'storeListrik'])->name('listrik.store');
    Route::post('/update-panel-listrik', [ListrikController::class, 'updateListrik']);
    Route::get('/export-pemakaian-listrik', [ListrikController::class, 'exportPemakaianListrikSpreadsheet']);
    
    // Monthly Approval routes
    Route::get('/approval', [UtilityApprovalController::class, 'approvalView'])->name('utility.approval');
    Route::get('/approval/check', [UtilityApprovalController::class, 'checkApproval']);
    Route::get('/approval/collected', [UtilityApprovalController::class, 'getCollectedData']);
    Route::post('/approval/submit', [UtilityApprovalController::class, 'submitMonthly']);
    Route::get('/approval/list', [UtilityApprovalController::class, 'getApprovalList']);
    Route::get('/approval/show/{id}', [UtilityApprovalController::class, 'showMonthlyDetails']);
    Route::post('/approval/{id}/approve', [UtilityApprovalController::class, 'approve']);
    Route::post('/approval/{id}/reject', [UtilityApprovalController::class, 'reject']);
});
