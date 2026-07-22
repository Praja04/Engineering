<?php

use App\Http\Controllers\Epr\PredictiveMaintenanceController;
use App\Http\Controllers\Epr\CorrectiveMaintenanceController;
use App\Http\Controllers\Epr\JenisDtController;
use App\Http\Controllers\Epr\WorkOrderController;
use App\Http\Controllers\Epr\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    Route::get('epr/dashboard', [DashboardController::class, 'index'])->name('epr.dashboard');

    // ── Predictive Maintenance (Operator & Foreman) ──
    Route::prefix('epr/predictive-maintenance')->as('epr.pm.')->group(function () {
        Route::get('/', function () {
            return redirect()->route('epr.pm.form');
        });
        Route::get('/form', [PredictiveMaintenanceController::class, 'form'])->name('form');
        Route::get('/data', [PredictiveMaintenanceController::class, 'data'])->name('data');
        Route::get('/get-reports', [PredictiveMaintenanceController::class, 'getReports'])->name('get-reports');
        Route::post('/store', [PredictiveMaintenanceController::class, 'store'])->name('store');
    });

    // ── Corrective Maintenance ──
    Route::prefix('epr/corrective-maintenance')->as('epr.cm.')->group(function () {
        Route::get('/', function () {
            return redirect()->route('epr.cm.form');
        });
        Route::get('/form', [CorrectiveMaintenanceController::class, 'form'])->name('form');
        Route::get('/data', [CorrectiveMaintenanceController::class, 'data'])->name('data');
        Route::get('/get-reports', [CorrectiveMaintenanceController::class, 'getReports'])->name('get-reports');
        Route::post('/store', [CorrectiveMaintenanceController::class, 'store'])->name('store');
        Route::delete('/{id}', [CorrectiveMaintenanceController::class, 'destroy'])->name('destroy');
        Route::get('/export', [CorrectiveMaintenanceController::class, 'export'])->name('export');
        Route::post('/import', [CorrectiveMaintenanceController::class, 'import'])->name('import');
    });

    // ── Master Data Jenis DT ──
    Route::prefix('epr/master/jenis-dt')->as('epr.master.jenis-dt.')->group(function () {
        Route::get('/', [JenisDtController::class, 'index'])->name('index');
        Route::get('/json', [JenisDtController::class, 'json'])->name('json');
        Route::post('/store', [JenisDtController::class, 'store'])->name('store');
        Route::delete('/{id}', [JenisDtController::class, 'destroy'])->name('destroy');
    });

    // ── Work Orders (Foreman Management) ──
    Route::prefix('epr/work-orders')->as('epr.wo.')->group(function () {
        Route::get('/', [WorkOrderController::class, 'index'])->name('index');
        Route::get('/json', [WorkOrderController::class, 'getWorkOrders'])->name('json');
        Route::post('/store', [WorkOrderController::class, 'store'])->name('store');
        Route::delete('/{id}', [WorkOrderController::class, 'destroy'])->name('destroy');
        Route::get('/users', [WorkOrderController::class, 'getAssignableUsers'])->name('users');
        Route::get('/my-wo', [WorkOrderController::class, 'getMyWorkOrders'])->name('my-wo');

        // Approval (Supervisor)
        Route::get('/approval', [WorkOrderController::class, 'approvalIndex'])->name('approval');
        Route::post('/{id}/approve', [WorkOrderController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [WorkOrderController::class, 'reject'])->name('reject');
    });
});
