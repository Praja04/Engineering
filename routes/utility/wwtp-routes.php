<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Utility\WWTPControllerProses;
use App\Http\Controllers\Utility\WWTPControllerPerformance;
use App\Http\Controllers\Utility\WWTPControllerSludge;
use App\Http\Controllers\Utility\WWTPControllerAnalisa;
use App\Http\Controllers\Utility\WWTPController;
use App\Http\Controllers\Utility\WWTPControllerApproval;
use App\Http\Controllers\Utility\WWTPControllerKoloni;
use App\Http\Controllers\Utility\WWTPControllerBiayaChemical;

Route::middleware('auth')->group(function () {
  Route::get('/wwtp/proses', [WWTPControllerProses::class, 'proses'])->name('wwtp.proses');
  Route::get('/wwtp/form_proses', [WWTPControllerProses::class, 'form_proses'])->name('wwtp.form_proses');
  Route::get('/wwtp/data_proses', [WWTPControllerProses::class, 'data_proses'])->name('wwtp.data_proses');
  Route::put('/wwtp/influent-harian/{id}', [WWTPControllerProses::class, 'updateHarian'])->name('wwtp.influent-harian.update');
  Route::delete('/wwtp/influent-harian/{id}', [WWTPControllerProses::class, 'destroyHarian'])->name('wwtp.influent-harian.destroy');
  Route::get('/wwtp/performance', [WWTPControllerPerformance::class, 'performance'])->name('wwtp.performance');
  Route::get('wwtp/form_performance', [WWTPControllerPerformance::class, 'form_performance'])->name('wwtp.form_performance');
  Route::get('wwtp/data_performance', [WWTPControllerPerformance::class, 'data_performance'])->name('wwtp.data_performance');
  Route::get('/wwtp/form_sludge', [WWTPControllerSludge::class, 'form_sludge'])->name('wwtp.form_sludge');
  Route::get('/wwtp/data_sludge', [WWTPControllerSludge::class, 'data_sludge'])->name('wwtp.data_sludge');
  Route::get('/wwtp/form_analisa', [WWTPControllerAnalisa::class, 'form_analisa'])->name('wwtp.form_analisa');
  Route::get('/wwtp/data_analisa', [WWTPControllerAnalisa::class, 'data_analisa'])->name('wwtp.data_analisa');
  Route::get('/wwtp/data_analisa/{id}/pdf', [WWTPControllerAnalisa::class, 'downloadPdf'])->name('wwtp.data_analisa.pdf');
  Route::get('/wwtp/analisa/export', [WWTPControllerAnalisa::class, 'exportExcel'])->name('wwtp.analisa.export');
  Route::get('/wwtp/manage_standar_analisa', [WWTPControllerAnalisa::class, 'manage_standar'])->name('wwtp.manage_standar_analisa')->middleware('access');
  Route::get('/wwtp/analisa/approval', [WWTPControllerAnalisa::class, 'approvalView'])->name('wwtp.analisa.approval');
  Route::get('/wwtp/approval', [WWTPControllerApproval::class, 'approvalView'])->name('wwtp.approval');
  Route::get('/wwtp/export', [WWTPController::class, 'export'])->name('wwtp.export');
  Route::get('/wwtp/export-monthly', [WWTPController::class, 'exportMonthly'])->name('wwtp.export-monthly');


  Route::post('wwtp/performance/ph-harian', [WWTPControllerPerformance::class, 'storePHHarian']);
  Route::post('wwtp/proses/influent-harian', [WWTPControllerProses::class, 'storeinfluentHarian'])->name('wwtp.influent-harian.store');
  Route::post('wwtp-sludge', [WWTPControllerSludge::class, 'store']);

  Route::prefix('wwtp-analisa')->group(function () {
    Route::post('/', [WWTPControllerAnalisa::class, 'store'])->name('wwtp-analisa.store');
  });

  // WWTP Approval Harian Routes
  Route::prefix('wwtp-approval')->middleware('auth')->group(function () {
    Route::get('/check', [WWTPControllerApproval::class, 'checkApproval']);
    Route::get('/list', [WWTPControllerApproval::class, 'getApprovalList']);
    Route::post('/mass-approve', [WWTPControllerApproval::class, 'massApprove']);
    Route::post('/mass-reject', [WWTPControllerApproval::class, 'massReject']);
    Route::get('/{id}', [WWTPControllerApproval::class, 'show']);
    Route::post('/{id}/approve', [WWTPControllerApproval::class, 'approve']);
    Route::post('/{id}/reject', [WWTPControllerApproval::class, 'reject']);
  });

  Route::prefix('api/wwtp-analisa')->group(function () {
    Route::get('/users/approvers', [WWTPControllerAnalisa::class, 'getUsersForApproval']);
    Route::get('/approval-list', [WWTPControllerAnalisa::class, 'getApprovalList']);
    Route::post('/mass-approve', [WWTPControllerAnalisa::class, 'massApprove']);
    Route::post('/mass-reject', [WWTPControllerAnalisa::class, 'massReject']);
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

  // WWTP Koloni Routes
  Route::get('/wwtp/form_koloni', [WWTPControllerKoloni::class, 'form_koloni'])->name('wwtp.form_koloni');
  Route::get('/wwtp/data_koloni', [WWTPControllerKoloni::class, 'data_koloni'])->name('wwtp.data_koloni');
  Route::get('/wwtp/master_koloni', [WWTPControllerKoloni::class, 'master_koloni'])->name('wwtp.master_koloni')->middleware('access');
  Route::post('/wwtp/koloni', [WWTPControllerKoloni::class, 'store'])->name('wwtp.koloni.store');
  Route::put('/wwtp/koloni/{id}', [WWTPControllerKoloni::class, 'update'])->name('wwtp.koloni.update');
  Route::post('/wwtp/koloni-master/store', [WWTPControllerKoloni::class, 'storeMaster'])->name('wwtp.master.store');
  Route::put('/wwtp/koloni-master/update/{id}', [WWTPControllerKoloni::class, 'updateMaster'])->name('wwtp.master.update');

  // WWTP Biaya Chemical Routes
  Route::get('/wwtp/form_biaya_chemical', [WWTPControllerBiayaChemical::class, 'form_biaya_chemical'])->name('wwtp.form_biaya_chemical');
  Route::get('/wwtp/data_biaya_chemical', [WWTPControllerBiayaChemical::class, 'data_biaya_chemical'])->name('wwtp.data_biaya_chemical');
  Route::get('/wwtp/master_biaya_chemical', [WWTPControllerBiayaChemical::class, 'master_biaya_chemical'])->name('wwtp.master_biaya_chemical')->middleware('access');
  Route::post('/wwtp/biaya-chemical', [WWTPControllerBiayaChemical::class, 'store'])->name('wwtp.biaya-chemical.store');
  Route::put('/wwtp/biaya-chemical/{id}', [WWTPControllerBiayaChemical::class, 'update'])->name('wwtp.biaya-chemical.update');
  Route::delete('/wwtp/biaya-chemical/{id}', [WWTPControllerBiayaChemical::class, 'destroy'])->name('wwtp.biaya-chemical.destroy');
  
  Route::get('/api/wwtp-biaya-chemical', [WWTPControllerBiayaChemical::class, 'index'])->name('api.wwtp-biaya-chemical.index');
  Route::get('/api/wwtp-biaya-chemical/check-filled', [WWTPControllerBiayaChemical::class, 'checkFilled'])->name('api.wwtp-biaya-chemical.check-filled');
  Route::get('/api/wwtp-biaya-chemical/standards', [WWTPControllerBiayaChemical::class, 'indexStandards'])->name('api.wwtp-biaya-chemical.standards.index');
  Route::post('/api/wwtp-biaya-chemical/standards', [WWTPControllerBiayaChemical::class, 'storeStandard'])->name('api.wwtp-biaya-chemical.standards.store');
  Route::put('/api/wwtp-biaya-chemical/standards/{id}', [WWTPControllerBiayaChemical::class, 'updateStandard'])->name('api.wwtp-biaya-chemical.standards.update');
  Route::delete('/api/wwtp-biaya-chemical/standards/{id}', [WWTPControllerBiayaChemical::class, 'destroyStandard'])->name('api.wwtp-biaya-chemical.standards.destroy');
});
