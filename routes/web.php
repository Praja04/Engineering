<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController; 
use App\Http\Controllers\Auth\TokenAuthController;

Route::get('/token-login', [TokenAuthController::class, 'loginWithToken'])
    ->name('auth.token-login');
Route::get('/', [AuthController::class, 'showLoginForm'])->name('home');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


///////////   View Routes   ///////////
Route::view('/dashboard', 'dashboard')->name('dashboard');
Route::middleware('auth')->group(function () {

    Route::middleware(['auth', 'access'])->group(function () {
        Route::prefix('users')->as('users.')->group(function () {
            Route::get('/index', [AuthController::class, 'manage_user'])->name('users.index');
            Route::get('/data', [AuthController::class, 'getUsers'])->name('get'); // API untuk DataTables
            Route::post('/', [AuthController::class, 'store'])->name('store'); // Simpan user baru
            Route::get('/edit/{id}', [AuthController::class, 'edit'])->name('edit'); // Ambil data user untuk edit
            Route::post('/{id}', [AuthController::class, 'update'])->name('update'); // Update user
            Route::delete('/{id}', [AuthController::class, 'destroy'])->name('destroy'); // Hapus user
        });
    });

    Route::get('/notifications', [NotificationController::class, 'kalibrasiCertificate'])->name('notifications');


    // Kalibrasi Routes
    @include 'kalibrasi/kalibrasi.php';
    @include('boiler/boiler.php');
    @include('kpi/kpi.php');
    // End Kalibrasi Routes

    ////////// Scoring Mesin Routes ///////////
    @include 'scoring/scoring-routes.php';
    @include 'utility/listrik-routes.php';
    @include 'utility/air-routes.php';
    @include 'utility/wwtp-routes.php';
    @include 'utility/chemical-routes.php';
    @include 'utility/dashboard.php';


    //////////    End View Routes   ///////////

    // Boiler Routes
    @include('boiler/boiler.php');
    // End Boiler Routes

});
//////////   API Routes   ///////////