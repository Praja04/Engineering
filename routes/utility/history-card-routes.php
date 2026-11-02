<?php

use App\Http\Controllers\Utility\HistoryCardController;
use Illuminate\Support\Facades\Route;

Route::prefix('utility/history-card')->as('history-card.')->group(function () {
    Route::get('/', [HistoryCardController::class, 'index'])->name('index');
    Route::get('/data', [HistoryCardController::class, 'dataView'])->name('data');
    Route::post('/store', [HistoryCardController::class, 'store'])->name('store');
    Route::get('/get-data', [HistoryCardController::class, 'getData'])->name('get-data');
    Route::get('/show/{id}', [HistoryCardController::class, 'show'])->name('show');
    Route::post('/update/{id}', [HistoryCardController::class, 'update'])->name('update');
    Route::delete('/destroy/{id}', [HistoryCardController::class, 'destroy'])->name('destroy');
    Route::get('/print', [HistoryCardController::class, 'printCard'])->name('print');
    Route::get('/export', [HistoryCardController::class, 'export'])->name('export');
});
