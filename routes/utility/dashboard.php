<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Utility\DashboardUtilityController;

Route::get('utility/dashboard', [DashboardUtilityController::class, 'utility'])->name('utility.dashboard');
Route::get('wwtp/dashboard', [DashboardUtilityController::class, 'wwtp'])->name('wwtp.dashboard');
