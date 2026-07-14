<?php

use App\Http\Controllers\Utility\AgendaAhuController;
use Illuminate\Support\Facades\Route;

Route::prefix('utility/agenda-ahu')->as('agenda-ahu.')->group(function () {
    Route::get('/', [AgendaAhuController::class, 'index'])->name('index');
    Route::get('/data', [AgendaAhuController::class, 'dataView'])->name('data');
    Route::get('/approval', [AgendaAhuController::class, 'approvalView'])->name('approval');

    // API Routes
    Route::post('/store', [AgendaAhuController::class, 'store'])->name('store');
    Route::get('/get-data', [AgendaAhuController::class, 'getData'])->name('get-data');
    Route::get('/export', [AgendaAhuController::class, 'export'])->name('export');
    Route::get('/get-collected', [AgendaAhuController::class, 'getCollectedData'])->name('get-collected');
    Route::get('/get-approval-data', [AgendaAhuController::class, 'getApprovalData'])->name('get-approval-data');
    Route::get('/show/{id}', [AgendaAhuController::class, 'show'])->name('show');
    Route::get('/show-monthly/{id}', [AgendaAhuController::class, 'showMonthlyDetails'])->name('show-monthly');
    Route::post('/update/{id}', [AgendaAhuController::class, 'update'])->name('update');
    Route::delete('/destroy/{id}', [AgendaAhuController::class, 'destroy'])->name('destroy');
    Route::post('/submit-monthly', [AgendaAhuController::class, 'submitMonthly'])->name('submit-monthly');

    // Approval Actions
    Route::post('/approve-foreman/{id}', [AgendaAhuController::class, 'approveForeman'])->name('approve-foreman');
    Route::post('/approve-supervisor/{id}', [AgendaAhuController::class, 'approveSupervisor'])->name('approve-supervisor');
    Route::post('/reject/{id}', [AgendaAhuController::class, 'reject'])->name('reject');
});
