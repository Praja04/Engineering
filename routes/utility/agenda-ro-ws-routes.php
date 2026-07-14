<?php

use App\Http\Controllers\Utility\AgendaRoWsController;
use Illuminate\Support\Facades\Route;

Route::prefix('utility/agenda-ro-ws')->as('agenda-ro-ws.')->group(function () {
    Route::get('/', [AgendaRoWsController::class, 'index'])->name('index');
    Route::get('/data', [AgendaRoWsController::class, 'dataView'])->name('data');
    Route::get('/approval', [AgendaRoWsController::class, 'approvalView'])->name('approval');

    // API Routes
    Route::post('/store', [AgendaRoWsController::class, 'store'])->name('store');
    Route::get('/get-data', [AgendaRoWsController::class, 'getData'])->name('get-data');
    Route::get('/export', [AgendaRoWsController::class, 'export'])->name('export');
    Route::get('/get-collected', [AgendaRoWsController::class, 'getCollectedData'])->name('get-collected');
    Route::get('/get-approval-data', [AgendaRoWsController::class, 'getApprovalData'])->name('get-approval-data');
    Route::get('/show/{id}', [AgendaRoWsController::class, 'show'])->name('show');
    Route::get('/show-monthly/{id}', [AgendaRoWsController::class, 'showMonthlyDetails'])->name('show-monthly');
    Route::post('/update/{id}', [AgendaRoWsController::class, 'update'])->name('update');
    Route::post('/submit-monthly', [AgendaRoWsController::class, 'submitMonthly'])->name('submit-monthly');
    Route::delete('/destroy/{id}', [AgendaRoWsController::class, 'destroy'])->name('destroy');

    // Approval Actions
    Route::post('/approve-foreman/{id}', [AgendaRoWsController::class, 'approveForeman'])->name('approve-foreman');
    Route::post('/approve-supervisor/{id}', [AgendaRoWsController::class, 'approveSupervisor'])->name('approve-supervisor');
    Route::post('/reject/{id}', [AgendaRoWsController::class, 'reject'])->name('reject');
    Route::post('/bulk-approve', [AgendaRoWsController::class, 'bulkApprove'])->name('bulk-approve');
    Route::post('/bulk-reject', [AgendaRoWsController::class, 'bulkReject'])->name('bulk-reject');
});
