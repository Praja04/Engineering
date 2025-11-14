<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Utility\ChemicalController;

Route::prefix('utility')->middleware('auth')->group(function () {

    // CRUD air
    Route::get('/trend-pemakaian-chemical', [ChemicalController::class, 'getTrendPemakaianChemical']);
    Route::get('/top5/chemical', [ChemicalController::class, 'getTopJenisPemakaianChemical']);
    Route::get('/top5/operator/chemical', [ChemicalController::class, 'getTopOperatorPemakaianChemical']);
    Route::get('/data/chemical', [ChemicalController::class, 'getPemakaianChemicalData']);
    Route::get('/export-pemakaian-chemical', [ChemicalController::class, 'exportPemakaianChemicalSpreadsheet']);
});
