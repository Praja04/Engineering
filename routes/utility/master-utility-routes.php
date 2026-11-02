<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Utility\MasterUtilityController;

Route::prefix('utility/master')->middleware('auth')->group(function () {
    Route::get('/', [MasterUtilityController::class, 'index'])->name('utility.master.index');

    // Master Listrik (Panel Types)
    Route::get('/listrik', [MasterUtilityController::class, 'getListrikPanels'])->name('utility.master.listrik.list');
    Route::post('/listrik', [MasterUtilityController::class, 'storeListrikPanel'])->name('utility.master.listrik.store');
    Route::post('/listrik/{id}/update', [MasterUtilityController::class, 'updateListrikPanel'])->name('utility.master.listrik.update');
    Route::post('/listrik/{id}/toggle', [MasterUtilityController::class, 'toggleListrikPanel'])->name('utility.master.listrik.toggle');
    Route::delete('/listrik/{id}', [MasterUtilityController::class, 'destroyListrikPanel'])->name('utility.master.listrik.destroy');

    // Master Air (Jenis Pemakaian)
    Route::get('/air', [MasterUtilityController::class, 'getAirAreas'])->name('utility.master.air.list');
    Route::post('/air', [MasterUtilityController::class, 'storeAirArea'])->name('utility.master.air.store');
    Route::post('/air/{id}/update', [MasterUtilityController::class, 'updateAirArea'])->name('utility.master.air.update');
    Route::post('/air/{id}/toggle', [MasterUtilityController::class, 'toggleAirArea'])->name('utility.master.air.toggle');
    Route::delete('/air/{id}', [MasterUtilityController::class, 'destroyAirArea'])->name('utility.master.air.destroy');

    // Master Chemical (Areas)
    Route::get('/chemical-areas', [MasterUtilityController::class, 'getChemicalAreas'])->name('utility.master.chemical-areas.list');
    Route::post('/chemical-areas', [MasterUtilityController::class, 'storeChemicalArea'])->name('utility.master.chemical-areas.store');
    Route::post('/chemical-areas/{id}/update', [MasterUtilityController::class, 'updateChemicalArea'])->name('utility.master.chemical-areas.update');
    Route::post('/chemical-areas/{id}/toggle', [MasterUtilityController::class, 'toggleChemicalArea'])->name('utility.master.chemical-areas.toggle');
    Route::delete('/chemical-areas/{id}', [MasterUtilityController::class, 'destroyChemicalArea'])->name('utility.master.chemical-areas.destroy');

    // Master Chemical (Jenis Pemakaian / Types)
    Route::get('/chemical-types', [MasterUtilityController::class, 'getChemicalTypes'])->name('utility.master.chemical-types.list');
    Route::post('/chemical-types', [MasterUtilityController::class, 'storeChemicalType'])->name('utility.master.chemical-types.store');
    Route::post('/chemical-types/test-formula', [MasterUtilityController::class, 'testFormula'])->name('utility.master.chemical-types.test-formula');
    Route::post('/chemical-types/{id}/update', [MasterUtilityController::class, 'updateChemicalType'])->name('utility.master.chemical-types.update');
    Route::post('/chemical-types/{id}/toggle', [MasterUtilityController::class, 'toggleChemicalType'])->name('utility.master.chemical-types.toggle');
    Route::delete('/chemical-types/{id}', [MasterUtilityController::class, 'destroyChemicalType'])->name('utility.master.chemical-types.destroy');
});
