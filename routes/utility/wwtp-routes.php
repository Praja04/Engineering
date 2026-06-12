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
  Route::get('/wwtp/analisa/approval', [WWTPControllerAnalisa::class, 'approvalView'])->name('wwtp.analisa.approval');
  Route::get('/wwtp/export', [WWTPController::class, 'export'])->name('wwtp.export');

  Route::prefix('wwtp-analisa')->group(function () {
    Route::post('/', [WWTPControllerAnalisa::class, 'store'])->name('wwtp-analisa.store');
  });

  Route::prefix('api/wwtp-analisa')->group(function () {
      Route::get('/users/approvers', [WWTPControllerAnalisa::class, 'getUsersForApproval']);
      Route::get('/approval-list', [WWTPControllerAnalisa::class, 'getApprovalList']);
      Route::post('/{id}/approve', [WWTPControllerAnalisa::class, 'approve']);
      Route::post('/{id}/reject', [WWTPControllerAnalisa::class, 'reject']);
      Route::get('/check-filled', [WWTPControllerAnalisa::class, 'checkFilledParameters']);
      Route::get('/parameters', [WWTPControllerAnalisa::class, 'indexParameter']);
      Route::post('/parameters', [WWTPControllerAnalisa::class, 'storeParameter']);
      Route::get('/parameters/{id}', [WWTPControllerAnalisa::class, 'showParameter']);
      Route::put('/parameters/{id}', [WWTPControllerAnalisa::class, 'updateParameter']);
      Route::delete('/parameters/{id}', [WWTPControllerAnalisa::class, 'destroyParameter']);
      Route::get('/points', [WWTPControllerAnalisa::class, 'indexPoint']);
      Route::post('/points', [WWTPControllerAnalisa::class, 'storePoint']);
      Route::get('/points/{id}', [WWTPControllerAnalisa::class, 'showPoint']);
      Route::put('/points/{id}', [WWTPControllerAnalisa::class, 'updatePoint']);
      Route::delete('/points/{id}', [WWTPControllerAnalisa::class, 'destroyPoint']);
      Route::get('/standards', [WWTPControllerAnalisa::class, 'indexStandar']);
      Route::post('/standards', [WWTPControllerAnalisa::class, 'storeStandar']);
      Route::get('/standards/{id}', [WWTPControllerAnalisa::class, 'showStandar']);
      Route::put('/standards/{id}', [WWTPControllerAnalisa::class, 'updateStandar']);
      Route::delete('/standards/{id}', [WWTPControllerAnalisa::class, 'destroyStandar']);
      Route::post('/', [WWTPControllerAnalisa::class, 'store']);
      Route::post('/{id}/parameters/{parameterId}', [WWTPControllerAnalisa::class, 'updateParameterResults']);
      Route::delete('/{id}/parameters/{parameterId}', [WWTPControllerAnalisa::class, 'destroyParameterResults']);
      Route::delete('/{id}', [WWTPControllerAnalisa::class, 'destroy']);
      Route::post('/{id}', [WWTPControllerAnalisa::class, 'update']);
      Route::get('/', [WWTPControllerAnalisa::class, 'index']);
      Route::get('/{id}', [WWTPControllerAnalisa::class, 'show']);
  });
});
