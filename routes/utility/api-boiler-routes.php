<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Utility\BoilerLogController;

Route::prefix('utility/boiler-logs')->group(function () {
    Route::any('/sync', [BoilerLogController::class, 'syncFromSensor'])->name('api.boiler-logs.sync');
});
