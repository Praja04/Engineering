<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ScoringMesin\DashboardController;


Route::get('/', [AuthController::class, 'showLoginForm'])->name('home');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


///////////   View Routes   ///////////
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dept_head.dashboard');

});
//////////    End View Routes   ///////////
