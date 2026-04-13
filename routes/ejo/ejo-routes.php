<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Ejo\EjoTicketController;
use App\Http\Controllers\Ejo\EjoImportController;
use App\Http\Controllers\Ejo\EjoProgressController;
use App\Http\Controllers\Ejo\EjoNoteController;
use App\Http\Controllers\Ejo\EjoAttachmentController;
use App\Http\Controllers\Ejo\EjoClassificationController;
use App\Http\Controllers\Ejo\EjoDashboardController;
use App\Http\Controllers\Ejo\EjoTemplateController;

Route::prefix('api/ejo')->group(function () {
    Route::get('/template', [EjoTemplateController::class, 'download']);

    // ── Dashboard ───────────────────────────────────────
    Route::get('/dashboard', [EjoDashboardController::class, 'index']);

    // ── Klasifikasi & Menu Sidebar ──────────────────────
    Route::get('/classifications', [EjoClassificationController::class, 'index']);
    Route::get('/menu', [EjoClassificationController::class, 'menu']);

    // ── Import ──────────────────────────────────────────
    Route::post('/import', [EjoImportController::class, 'import']);

    // ── Delete sub-resources (HARUS sebelum /{id} agar tidak nabrak) ──
    // Tanpa ini, DELETE /api/ejo/progress/5 akan match /{id} dengan id="progress"
    Route::delete('/progress/{id}',   [EjoProgressController::class, 'destroy']);
    Route::delete('/note/{id}',       [EjoNoteController::class, 'destroy']);
    Route::delete('/attachment/{id}', [EjoAttachmentController::class, 'destroy']);

    // ── Tickets ─────────────────────────────────────────
    Route::get('/',        [EjoTicketController::class, 'index']);
    Route::post('/',       [EjoTicketController::class, 'store']);
    Route::get('/{id}',    [EjoTicketController::class, 'show']);
    Route::put('/{id}',    [EjoTicketController::class, 'update']);
    Route::delete('/{id}', [EjoTicketController::class, 'destroy']);

    Route::post('/{id}/assign-team', [EjoTicketController::class, 'assignTeam']);

    // ── Progress ─────────────────────────────────────────
    Route::post('/{id}/progress', [EjoProgressController::class, 'store']);
    Route::get('/{id}/progress',  [EjoProgressController::class, 'index']);

    // ── Notes ────────────────────────────────────────────
    Route::post('/{id}/note', [EjoNoteController::class, 'store']);
    Route::get('/{id}/note',  [EjoNoteController::class, 'index']);

    // ── Attachments ──────────────────────────────────────
    Route::post('/{id}/attachment', [EjoAttachmentController::class, 'store']);
    Route::get('/{id}/attachment',  [EjoAttachmentController::class, 'index']);
});
