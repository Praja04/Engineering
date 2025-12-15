<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Utility\WWTPControllerProses;
use App\Http\Controllers\Utility\WWTPControllerPerformance;

Route::middleware('auth')->group(function () {
  Route::get('/wwtp/proses', [WWTPControllerProses::class, 'proses'])->name('wwtp.proses');
  Route::get('/wwtp/performance', [WWTPControllerPerformance::class, 'performance'])->name('wwtp.performance');
});
