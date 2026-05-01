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

    // ── Export ───────────────────────────────────────────  ← NAIK KE SINI
    Route::get('/export', [CapacitorBankController::class, 'exportExcel'])->name('export');

    // ── Operator ─────────────────────────────────────────
    Route::post('/store',        [CapacitorBankController::class, 'store'])->name('store');

    // ── Foreman ──────────────────────────────────────────
    Route::post('/submit-bulan', [CapacitorBankController::class, 'submitBulan'])->name('submit');

    // ── Approval Supervisor (Final) ───────────────────────
    Route::post('/approve/supervisor/{id}', [CapacitorBankController::class, 'approveSupervisor'])->name('approve.supervisor');

    // ── Update Harian ────────────────────────────────────  ← TETAP PALING BAWAH
    Route::put('/{tanggal}', [CapacitorBankController::class, 'update'])->name('update');
});