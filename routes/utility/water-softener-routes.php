<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Utility\WaterSoftenerController;

// ============================================================
// Water Softener — Engineering Utility
// ============================================================

Route::prefix('utility/water-softener')
    ->name('water-softener.')
    ->middleware(['auth'])
    ->group(function () {

        // ── Views ────────────────────────────────────────────
        Route::get('/',         [WaterSoftenerController::class, 'index'])->name('index');
        Route::get('/rekap',    [WaterSoftenerController::class, 'rekapView'])->name('rekap');
        Route::get('/approval', [WaterSoftenerController::class, 'approvalView'])->name('approval');

        // ── Data (JSON) ──────────────────────────────────────
        Route::get('/data',          [WaterSoftenerController::class, 'getData'])->name('data');
        Route::get('/approval-list', [WaterSoftenerController::class, 'getApprovalList'])->name('approval.list');
        Route::get('/export',         [WaterSoftenerController::class, 'export'])->name('export');

        // ── Operator ─────────────────────────────────────────
        Route::post('/store',        [WaterSoftenerController::class, 'store'])->name('store');
        Route::post('/submit-bulan', [WaterSoftenerController::class, 'submitBulan'])->name('submit');

        // ── Approval ─────────────────────────────────────────
        Route::put('/{tanggal}', [WaterSoftenerController::class, 'update'])->name('update');
        Route::post('/approve/foreman/{id}',    [WaterSoftenerController::class, 'approveForeman'])->name('approve.foreman');
        Route::post('/approve/supervisor/{id}', [WaterSoftenerController::class, 'approveSupervisor'])->name('approve.supervisor');
    });
