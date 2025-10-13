<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Utility\ListrikController;

Route::prefix('utility')->middleware('auth')->group(function () {
    Route::get('/form', [ListrikController::class, 'formUtility']);
    Route::get('/data', [ListrikController::class, 'DataUtility']);
    Route::post('/data/listrik/store', [ListrikController::class, 'storeListrik'])->name('listrik.store');
    Route::post('/update-panel-listrik', [ListrikController::class, 'updateListrik']);
    Route::get('/export-pemakaian-listrik', [ListrikController::class, 'exportPemakaianListrikSpreadsheet']);
    
});
