<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Utility\EngUtilityProjectController;

Route::middleware(['auth'])->prefix('utility/project')->name('utility.project.')->group(function () {
    // ── 1. Dashboard (Dipisah sesuai request user) ──
    Route::get('/dashboard', [EngUtilityProjectController::class, 'dashboard'])->name('dashboard');

    // ── 2. Data Project (Tabel & Tracking) ──
    Route::get('/data', [EngUtilityProjectController::class, 'data'])->name('data');
    Route::get('/', [EngUtilityProjectController::class, 'data'])->name('index');

    // ── 3. Form Project (Input & Edit), Export & Import ──
    Route::get('/form', [EngUtilityProjectController::class, 'create'])->name('form');
    Route::get('/create', [EngUtilityProjectController::class, 'create'])->name('create');
    Route::post('/', [EngUtilityProjectController::class, 'store'])->name('store');
    Route::get('/template', [EngUtilityProjectController::class, 'downloadTemplate'])->name('template');
    Route::get('/export', [EngUtilityProjectController::class, 'exportExcel'])->name('export');
    Route::post('/import', [EngUtilityProjectController::class, 'import'])->name('import');
    Route::get('/{id}/edit', [EngUtilityProjectController::class, 'edit'])->name('edit');
    Route::put('/{id}', [EngUtilityProjectController::class, 'update'])->name('update');
    Route::delete('/{id}', [EngUtilityProjectController::class, 'destroy'])->name('destroy');

    // ── 4. Progress Update & History Logs ──
    Route::post('/{id}/progress', [EngUtilityProjectController::class, 'updateProgress'])->name('progress.update');
    Route::get('/{id}/history', [EngUtilityProjectController::class, 'history'])->name('history');

    // ── 5. Master Data (Area & Kategori) ──
    Route::get('/master', [EngUtilityProjectController::class, 'master'])->name('master');
    Route::post('/master/area', [EngUtilityProjectController::class, 'storeArea'])->name('master.area.store');
    Route::put('/master/area/{id}', [EngUtilityProjectController::class, 'updateArea'])->name('master.area.update');
    Route::delete('/master/area/{id}', [EngUtilityProjectController::class, 'destroyArea'])->name('master.area.destroy');

    Route::post('/master/category', [EngUtilityProjectController::class, 'storeCategory'])->name('master.category.store');
    Route::put('/master/category/{id}', [EngUtilityProjectController::class, 'updateCategory'])->name('master.category.update');
    Route::delete('/master/category/{id}', [EngUtilityProjectController::class, 'destroyCategory'])->name('master.category.destroy');
});
