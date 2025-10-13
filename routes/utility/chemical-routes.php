<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Utility\ChemicalController;

Route::prefix('utility')->middleware('auth')->group(function () {

    // CRUD air
   Route::get('/trend-pemakaian-chemical', [ChemicalController::class, 'getTrendPemakaianChemical']);
   Route::get('/top5/chemical', [ChemicalController::class, 'getTopJenisPemakaianChemical']);
    Route::get('/top5/operator/chemical', [ChemicalController::class, 'getTopOperatorPemakaianChemical']);

    // CRUD chemical
    Route::post('/store/chemical', [ChemicalController::class, 'store_chemical'])->name('chemical.store'); // Simpan data baru
    Route::get('/chemical-types/{area_id}', [ChemicalController::class, 'getTypesByArea']);
    Route::get('/chemical-area', [ChemicalController::class, 'getChemicalAreas']);
    Route::get('/data/chemical', [ChemicalController::class, 'getPemakaianChemicalData']);
   Route::post('/update-pemakaian-chemical', [ChemicalController::class, 'updateChemical']);

    Route::get('/export-pemakaian-chemical', [ChemicalController::class, 'exportPemakaianChemicalSpreadsheet']);
  
});
