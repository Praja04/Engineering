<?php

use App\Http\Controllers\Utility\DashboardUtilityController;
use App\Http\Controllers\Utility\WarmingUpGenset;
use Illuminate\Support\Facades\Route;

// Route::middleware('auth')->group(function () {
Route::prefix('utility/warming-up-genset')->name('warming-up-genset.')->group(function () {
    Route::get('/', [WarmingUpGenset::class, 'index'])->name('index');     // form input
    Route::get('/data', [WarmingUpGenset::class, 'dataView'])->name('data');   // rekap & export
    Route::get('/json', [WarmingUpGenset::class, 'getData'])->name('json');    // API JSON
    Route::get('/json/{id}', [WarmingUpGenset::class, 'show'])->name('show');  // Detail JSON
    Route::post('/json/{id}', [WarmingUpGenset::class, 'update'])->name('update-json'); // Update JSON
    Route::post('/reject/{id}', [WarmingUpGenset::class, 'reject'])->name('reject'); // Reject laporan
    Route::post('/store', [WarmingUpGenset::class, 'store'])->name('store');
    Route::get('/export', [WarmingUpGenset::class, 'export'])->name('export');
    Route::get('/approval', [WarmingUpGenset::class, 'approvalView'])->name('approval');
    Route::post('/approve-foreman/{id}', [WarmingUpGenset::class, 'approveForeman'])->name('approve-foreman');
    Route::post('/approve-supervisor/{id}', [WarmingUpGenset::class, 'approveSupervisor'])->name('approve-supervisor');
});
// });
