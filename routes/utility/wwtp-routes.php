<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Utility\WWTPControllerProses;
use App\Http\Controllers\Utility\WWTPControllerPerformance;
use App\Http\Controllers\Utility\WWTPControllerSludge;
use App\Http\Controllers\Utility\WWTPControllerAnalisa;
use App\Http\Controllers\Utility\WWTPController;

Route::middleware('auth')->group(function () {
  Route::get('/wwtp/proses', [WWTPControllerProses::class, 'proses'])->name('wwtp.proses');
  Route::get('/wwtp/form_proses', [WWTPControllerProses::class, 'form_proses'])->name('wwtp.form_proses');
  Route::get('/wwtp/data_proses', [WWTPControllerProses::class, 'data_proses'])->name('wwtp.data_proses');
  Route::get('/wwtp/performance', [WWTPControllerPerformance::class, 'performance'])->name('wwtp.performance');
  Route::get('wwtp/form_performance', [WWTPControllerPerformance::class, 'form_performance'])->name('wwtp.form_performance');
  Route::get('wwtp/data_performance', [WWTPControllerPerformance::class, 'data_performance'])->name('wwtp.data_performance');
  Route::get('/wwtp/form_sludge', [WWTPControllerSludge::class, 'form_sludge'])->name('wwtp.form_sludge');
  Route::get('/wwtp/data_sludge', [WWTPControllerSludge::class, 'data_sludge'])->name('wwtp.data_sludge');
  Route::get('/wwtp/form_analisa', [WWTPControllerAnalisa::class, 'form_analisa'])->name('wwtp.form_analisa');
  Route::get('/wwtp/data_analisa', [WWTPControllerAnalisa::class, 'data_analisa'])->name('wwtp.data_analisa');
  Route::get('/wwtp/data_analisa/{id}/pdf', [WWTPControllerAnalisa::class, 'downloadPdf'])->name('wwtp.data_analisa.pdf');
  Route::get('/wwtp/manage_standar_analisa', [WWTPControllerAnalisa::class, 'manage_standar'])->name('wwtp.manage_standar_analisa')->middleware('access');
  Route::get('/wwtp/export', [WWTPController::class, 'export'])->name('wwtp.export');

  Route::prefix('wwtp-analisa')->group(function () {
    Route::post('/', [WWTPControllerAnalisa::class, 'store'])->name('wwtp-analisa.store');
  });
});
