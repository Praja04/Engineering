<?php

use App\Http\Controllers\Utility\BoilerLogController;
use Illuminate\Support\Facades\Route;

Route::prefix('utility/boiler-logs')->name('boiler-logs.')->group(function () {
    Route::get('/data', [BoilerLogController::class, 'dataView'])->name('data');
    Route::get('/form', [BoilerLogController::class, 'formView'])->name('form');
    Route::get('/approval', [BoilerLogController::class, 'approvalView'])->name('approval');
    Route::get('/json', [BoilerLogController::class, 'getData'])->name('json');
    Route::get('/approval/json', [BoilerLogController::class, 'getApprovalData'])->name('approval.json');
    Route::get('/json/{id}', [BoilerLogController::class, 'show'])->name('show');
    Route::post('/submit/{id}', [BoilerLogController::class, 'submitDaily'])->name('submit');
    Route::post('/mass-submit', [BoilerLogController::class, 'massSubmitDaily'])->name('mass-submit');
    Route::post('/approve/{id}', [BoilerLogController::class, 'approveDaily'])->name('approve');
    Route::post('/reject/{id}', [BoilerLogController::class, 'rejectDaily'])->name('reject');
    Route::post('/mass-approve', [BoilerLogController::class, 'massApproveDaily'])->name('mass-approve');
    Route::post('/mass-reject', [BoilerLogController::class, 'massRejectDaily'])->name('mass-reject');
    Route::get('/export', [BoilerLogController::class, 'export'])->name('export');
    Route::get('/users', [BoilerLogController::class, 'getUsersForApproval'])->name('users');
    Route::post('/store', [BoilerLogController::class, 'store'])->name('store');
    Route::post('/update/{id}', [BoilerLogController::class, 'update'])->name('update');
});
