<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScoringMesin\MachineController;
use App\Http\Controllers\ScoringMesin\ProcessParameterController;
use App\Http\Controllers\ScoringMesin\SectionController;
use App\Http\Controllers\ScoringMesin\PartController;
use App\Http\Controllers\ScoringMesin\StandardStateController;
use App\Http\Controllers\ScoringMesin\MachineScoringController;
use App\Http\Controllers\ScoringMesin\MachineProcessController;
use App\Http\Controllers\ScoringMesin\DashboardController;
////////// Scoring Mesin Routes ///////////
Route::get('/dashboard/mesin/retail', [DashboardController::class, 'index'])->name('dashboard.mesin');
Route::prefix('scoring-mesin')->name('scoring-mesin.')->group(function () {
    // Machine routes
    Route::get('machines/statistics', [MachineController::class, 'statistics'])->name('machines.statistics');
    Route::resource('machines', MachineController::class);

    // Other resources
    Route::resource('process-parameters', ProcessParameterController::class);
    Route::resource('sections', SectionController::class);
    Route::resource('parts', PartController::class);
    Route::resource('standard-states', StandardStateController::class);
});

Route::prefix('scoring')->name('scoring.')->middleware(['auth'])->group(function () {

    // Machine Selection for Scoring
    Route::get('/', [MachineScoringController::class, 'index'])->name('index');

    // Machine Processes for Scoring
    Route::get('/machine/{machine}', [MachineScoringController::class, 'showMachineProcesses'])->name('machine.processes');

    // Scoring Form
    Route::get('/process/{machineProcess}', [MachineScoringController::class, 'showScoringForm'])->name('form');

    // Store Scoring
    Route::post('/process/{machineProcess}', [MachineScoringController::class, 'store'])->name('store');

    // Scoring History
    Route::get('/history', [MachineScoringController::class, 'history'])->name('history');

    // Scoring Detail
    Route::get('/detail/{scoring}', [MachineScoringController::class, 'show'])->name('show');

    // Delete Scoring
    Route::delete('/detail/{scoring}', [MachineScoringController::class, 'destroy'])->name('destroy');

    // Statistics
    Route::get('/statistics', [MachineScoringController::class, 'statistics'])->name('statistics');
    Route::get('/api/mesin', [MachineScoringController::class, 'api_scoring_mesin'])->name('api.mesin');
});
Route::prefix('machine-processes')->group(function () {

    // GET: Display main index page / Get all machine processes (AJAX)
    Route::get('/', [MachineProcessController::class, 'index'])
        ->name('machine-processes.index');

    // POST: Create new machine process
    Route::post('/', [MachineProcessController::class, 'store'])
        ->name('machine-processes.store');

    // GET: Get tree view for specific machine (must be before {machineProcess})
    Route::get('/tree/{machineId}', [MachineProcessController::class, 'tree'])
        ->name('machine-processes.tree');

    // POST: Bulk assign process parameters to a machine
    Route::post('/bulk-assign/{machineId}', [MachineProcessController::class, 'bulkAssign'])
        ->name('machine-processes.bulk-assign');

    // GET: Get all processes by machine ID
    Route::get('/by-machine/{machineId}', [MachineProcessController::class, 'byMachine'])
        ->name('machine-processes.by-machine');

    // GET: Get single machine process detail
    Route::get('/{machineProcess}', [MachineProcessController::class, 'show'])
        ->name('machine-processes.show');

    // PUT: Update machine process
    Route::put('/{machineProcess}', [MachineProcessController::class, 'update'])
        ->name('machine-processes.update');

    // DELETE: Delete machine process
    Route::delete('/{machineProcess}', [MachineProcessController::class, 'destroy'])
        ->name('machine-processes.destroy');
});



////////// End Scoring Mesin Routes ///////////