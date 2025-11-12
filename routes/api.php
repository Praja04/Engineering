<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScoringMesin\MachineScoringController;
use App\Http\Controllers\Auth\TokenAuthController;

Route::post('/auth/validate-token', [TokenAuthController::class, 'receiveToken']);
// Api Kalibrasi Routes
@include 'kalibrasi/api_kalibrasi.php';

Route::get('scoring/mesin', [MachineScoringController::class, 'api_scoring_mesin'])->name('api_scoring.mesin');
////// api utility listrik routes ///////
@include 'utility/api-listrik-routes.php';
