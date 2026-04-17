<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Project\ProjectController;
use App\Http\Controllers\Project\ProjectFaseSatuController;
use App\Http\Controllers\Project\ProjectFaseDuaController;
use App\Http\Controllers\Project\ProjectFaseTigaController;
use App\Http\Controllers\Project\ProjectDokumentasiController;

Route::middleware(['auth'])->prefix('project')->name('project.')->group(function () {

    // ── Dashboard ────────────────────────────────────────────────
    Route::get('/', [ProjectController::class, 'index'])->name('index');

    // ── Fase 1 (tanpa {project} dulu) ───────────────────────────
    Route::get('/fase1/create',  [ProjectFaseSatuController::class, 'create'])->name('fase1.create');
    Route::post('/fase1',        [ProjectFaseSatuController::class, 'store'])->name('fase1.store');

    // ── Detail & route dengan {project} — SETELAH route statis ──
    Route::get('/{project}',              [ProjectController::class, 'show'])->name('show');
    Route::get('/{project}/fase1/edit',   [ProjectFaseSatuController::class, 'edit'])->name('fase1.edit');
    Route::put('/{project}/fase1',        [ProjectFaseSatuController::class, 'update'])->name('fase1.update');

    Route::get('/{project}/fase2/create', [ProjectFaseDuaController::class, 'create'])->name('fase2.create');
    Route::post('/{project}/fase2',       [ProjectFaseDuaController::class, 'store'])->name('fase2.store');
    Route::get('/{project}/fase2/edit',   [ProjectFaseDuaController::class, 'edit'])->name('fase2.edit');
    Route::put('/{project}/fase2',        [ProjectFaseDuaController::class, 'update'])->name('fase2.update');

    Route::get('/{project}/fase3/create', [ProjectFaseTigaController::class, 'create'])->name('fase3.create');
    Route::post('/{project}/fase3',       [ProjectFaseTigaController::class, 'store'])->name('fase3.store');
    Route::get('/{project}/fase3/edit',   [ProjectFaseTigaController::class, 'edit'])->name('fase3.edit');
    Route::put('/{project}/fase3',        [ProjectFaseTigaController::class, 'update'])->name('fase3.update');

    Route::delete('/dokumentasi/{dokumentasi}', [ProjectDokumentasiController::class, 'destroy'])->name('dokumentasi.destroy');
});
