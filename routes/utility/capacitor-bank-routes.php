<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Utility\CapacitorBankController;

// ============================================================
// Capacitor Bank — Engineering Utility
// ============================================================

Route::prefix('utility/capacitor-bank')
    ->name('capacitor-bank.')
    ->middleware(['auth'])
    ->group(function () {

        // ── Views ────────────────────────────────────────────
        Route::get('/',         [CapacitorBankController::class, 'index'])->name('index');
        Route::get('/rekap',    [CapacitorBankController::class, 'rekapView'])->name('rekap');
        Route::get('/approval', [CapacitorBankController::class, 'approvalView'])->name('approval');

        // ── Data (JSON) ──────────────────────────────────────
        Route::get('/data',          [CapacitorBankController::class, 'getData'])->name('data');
        Route::get('/approval-list', [CapacitorBankController::class, 'getApprovalList'])->name('approval.list');

        // ── Operator ─────────────────────────────────────────
        Route::post('/store',        [CapacitorBankController::class, 'store'])->name('store');
        Route::post('/submit-bulan', [CapacitorBankController::class, 'submitBulan'])->name('submit');

        // ── Update Harian ────────────────────────────────────
        Route::put('/{tanggal}', [CapacitorBankController::class, 'update'])->name('update');

        // ── Approval ─────────────────────────────────────────
        Route::post('/approve/foreman/{id}',    [CapacitorBankController::class, 'approveForeman'])->name('approve.foreman');
        Route::post('/approve/supervisor/{id}', [CapacitorBankController::class, 'approveSupervisor'])->name('approve.supervisor');
    });
