<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Utility\DashboardUtilityController;

Route::get('utility/dashboard', [DashboardUtilityController::class, 'utility'])->name('utility.dashboard');
Route::get('utility/dashboard/listrik', [DashboardUtilityController::class, 'listrik'])->name('utility.dashboardlistrik');
Route::get('utility/dashboard/air', [DashboardUtilityController::class, 'air'])->name('utility.dashboardair');
Route::get('utility/dashboard/chemical', [DashboardUtilityController::class, 'chemical'])->name('utility.dashboardchemical');
Route::get('dashboard/wwtp/proses', [DashboardUtilityController::class, 'wwtp_proses'])->name('wwtp.dashboard_proses');
Route::get('dashboard/wwtp/performance', [DashboardUtilityController::class, 'wwtp_performance'])->name('wwtp.dashboard_performance');
Route::get('dashboard/wwtp/sludge', [DashboardUtilityController::class, 'wwtp_sludge'])->name('wwtp.dashboard_sludge');
Route::get('dashboard/wwtp/visualisasi', [DashboardUtilityController::class, 'wwtp_visualisasi'])->name('wwtp.dashboard_visualisasi');
Route::get('dashboard/wwtp/visualisasi-data', [DashboardUtilityController::class, 'wwtp_visualisasi_data'])->name('wwtp.dashboard_visualisasi_data');
