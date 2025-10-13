<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Utility\ListrikController;

Route::prefix('utility')->group(function () {
    Route::get('/trend-pemakaian-listrik', [ListrikController::class, 'getTrendPemakaianListrik']);
    Route::get('/top5/listrik', [ListrikController::class, 'getTopJenisPemakaianListrik']);
    Route::get('/top5/operator/listrik', [ListrikController::class, 'getTopOperatorPemakaianListrik']);
    Route::get('/data/listrik', [ListrikController::class, 'getPemakaianListrikData']);
    Route::get('/export-pemakaian-listrik', [ListrikController::class, 'exportPemakaianListrikSpreadsheet']);
});
