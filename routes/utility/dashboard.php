<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Utility\DashboardUtilityController;

Route::get('utility/dashboard', [DashboardUtilityController::class, 'utility'])->name('utility.dashboard');
Route::get('utility/dashboard/listrik', [DashboardUtilityController::class, 'listrik'])->name('utility.dashboardlistrik');
Route::get('utility/dashboard/air', [DashboardUtilityController::class, 'air'])->name('utility.dashboardair');
Route::get('utility/dashboard/chemical', [DashboardUtilityController::class, 'chemical'])->name('utility.dashboardchemical');
Route::get('wwtp/dashboard/proses', [DashboardUtilityController::class, 'wwtp_proses'])->name('wwtp.dashboard_proses');
Route::get('wwtp/dashboard/performance', [DashboardUtilityController::class, 'wwtp_performance'])->name('wwtp.dashboard_performance');
Route::get('wwtp/dashboard/sludge', [DashboardUtilityController::class, 'wwtp_sludge'])->name('wwtp.dashboard_sludge');
