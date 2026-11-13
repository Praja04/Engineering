<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Utility\DashboardUtilityController;
use App\Http\Controllers\Utility\DashboardUtilityMachineController;
use App\Http\Controllers\Utility\WWTPController;

// Utility Dashboard
Route::get('utility/dashboard', [DashboardUtilityController::class, 'utility'])->name('utility.dashboard');
Route::get('utility/dashboard/listrik', [DashboardUtilityController::class, 'listrik'])->name('utility.dashboardlistrik');
Route::get('utility/dashboard/air', [DashboardUtilityController::class, 'air'])->name('utility.dashboardair');
Route::get('utility/dashboard/chemical', [DashboardUtilityController::class, 'chemical'])->name('utility.dashboardchemical');

// WWTP Dashboard
Route::middleware(['permission:wwtp_dashboard'])->group(function () {
    Route::get('dashboard/wwtp/proses', [DashboardUtilityController::class, 'wwtp_proses'])
        ->middleware('permission:wwtp_proses_dashboard')
        ->name('wwtp.dashboard_proses');
    Route::get('dashboard/wwtp/performance', [DashboardUtilityController::class, 'wwtp_performance'])
        ->middleware('permission:wwtp_performance_dashboard')
        ->name('wwtp.dashboard_performance');
    Route::get('dashboard/wwtp/sludge', [DashboardUtilityController::class, 'wwtp_sludge'])
        ->middleware('permission:wwtp_sludge_dashboard')
        ->name('wwtp.dashboard_sludge');
    Route::middleware('permission:wwtp_flow_proses_dashboard')->group(function () {
        Route::get('dashboard/wwtp/visualisasi', [DashboardUtilityController::class, 'wwtp_visualisasi'])
            ->name('wwtp.dashboard_visualisasi');
        Route::get('dashboard/wwtp/visualisasi-data', [WWTPController::class, 'wwtp_visualisasi_data'])
            ->name('wwtp.dashboard_visualisasi_data');
    });
    Route::middleware('permission:wwtp_wco_she_dashboard')->group(function () {
        Route::get('dashboard/wwtp/wco', [DashboardUtilityController::class, 'wwtp_wco'])->name('wwtp.dashboard_wco');
        Route::get('dashboard/wwtp/wco-data', [WWTPController::class, 'wwtp_wco_data'])->name('wwtp.dashboard_wco_data');
    });
});

// Machine Dashboard
Route::get('dashboard/engineering-utility', [DashboardUtilityMachineController::class, 'machine'])->name('utility.dashboard_machine');
Route::get('utility/dashboard/machine-data', [DashboardUtilityMachineController::class, 'machine_data'])->name('utility.dashboard_machine_data');
Route::match(['get', 'post'], 'dashboard/engineering-utility/export-pptx', [DashboardUtilityMachineController::class, 'export_pptx'])->name('utility.dashboard_machine_export_pptx');
Route::get('dashboard/utility/pptx-weekly', [DashboardUtilityMachineController::class, 'export_pptx'])->name('utility.pptx_weekly');
Route::match(['get', 'post'], 'dashboard/wwtp/wco-export-pptx', [WWTPController::class, 'export_pptx'])->name('wwtp.dashboard_wco_export_pptx');
