<?php

use App\Http\Controllers\Utility\CapacitorBankMachineController;
use Illuminate\Support\Facades\Route;

Route::prefix('utility/capbank')->group(function () {
    // Terima data dari mesin (IoT / edge device)
    Route::post('/machine-data/store', [CapacitorBankMachineController::class, 'store']);

    // Power meter data
    Route::get('/machine-data/latest', [CapacitorBankMachineController::class, 'latest']);
    Route::get('/machine-data', [CapacitorBankMachineController::class, 'index']);

    // Cap ON/OFF history
    Route::get('/cap-history', [CapacitorBankMachineController::class, 'capHistory']);
    Route::get('/cap-history/summary', [CapacitorBankMachineController::class, 'capHistorySummary']);
});
