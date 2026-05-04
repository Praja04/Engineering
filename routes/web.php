<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Auth\TokenAuthController;

Route::get('/auth/sso/callback', [TokenAuthController::class, 'callback'])
    ->name('auth.token-login');

Route::middleware('web')->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('home');
    Route::get('/signin', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});


///////////   View Routes   ///////////
Route::view('/dashboard', 'dashboard')->name('dashboard');
Route::view('/home', 'home')->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/ejo', function () {
        return view('ejo.index');
    });
    Route::get('/ejo/dashboard', function () {
        return view('dashboard.ejo.dashboard');
    })->name('ejo.dashboard');
    Route::get('/ejo/create', function () {
        return view('ejo.create');
    });

    Route::get('/ejo/{id}', function ($id) {
        return view('ejo.detail', compact('id'));
    });
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

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::post('/notif/refresh', [NotificationController::class, 'refresh'])->name('notif.refresh');
    Route::delete('/notif/delete/{id}', [NotificationController::class, 'destroy'])->name('notif.delete');
    Route::delete('/notif/delete-all', [NotificationController::class, 'destroyAll'])->name('notif.delete-all');


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
    @include 'utility/esp-routes.php';
    @include 'utility/water-softener-routes.php';
    @include 'utility/capacitor-bank-routes.php';
    @include 'utility/warming-up-genset-routes.php';
    @include 'utility/mdp-routes.php';
    @include 'utility/compressor-routes.php';
    @include 'utility/ahu-routes.php';
    @include 'utility/boiler-routes.php';
    @include 'ejo/ejo-routes.php';
    @include 'project/project_routes.php';

    //////////    End View Routes   ///////////

    // Boiler Routes
    @include('boiler/boiler.php');

    @include('maintenance/maintenance.php');
});
