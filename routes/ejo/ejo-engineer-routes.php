<?php

use App\Http\Controllers\Api\EjoEngineerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| EJO Engineer Web & API Routes (Integrated Module)
|--------------------------------------------------------------------------
*/

// Web Portal View
Route::get('/ejo-engineer', function () {
    return view('ejo-engineer.index');
})->name('ejo-engineer.index');

// Direct Legacy Redirects to New Portal
Route::get('/ejo', function () {
    return redirect()->to('/ejo-engineer');
})->name('ejo.index');

Route::get('/ejo/dashboard', function () {
    return redirect()->to('/ejo-engineer');
})->name('ejo.dashboard');

// REST API Endpoints for EJO Engineer Portal
Route::prefix('api/ejo-engineer')->group(function () {
    // Auth & TOTP
    Route::post('/login', [EjoEngineerController::class, 'login']);
    Route::post('/totp/setup', [EjoEngineerController::class, 'setupTotp']);
    Route::post('/totp/enable', [EjoEngineerController::class, 'enableTotp']);
    Route::post('/totp/disable', [EjoEngineerController::class, 'disableTotp']);
    Route::post('/heartbeat', [EjoEngineerController::class, 'heartbeat']);
    Route::post('/logout', [EjoEngineerController::class, 'logout']);

    // EJO (Standard)
    Route::get('/ejos', [EjoEngineerController::class, 'getEjos']);
    Route::post('/ejos', [EjoEngineerController::class, 'createEjo']);
    Route::put('/ejos/{id}', [EjoEngineerController::class, 'updateEjo']);
    Route::delete('/ejos/{id}', [EjoEngineerController::class, 'deleteEjo']);

    // General EJO
    Route::get('/general-ejos', [EjoEngineerController::class, 'getGeneralEjos']);
    Route::post('/general-ejos', [EjoEngineerController::class, 'createGeneralEjo']);
    Route::put('/general-ejos/{id}', [EjoEngineerController::class, 'updateGeneralEjo']);
    Route::delete('/general-ejos/{id}', [EjoEngineerController::class, 'deleteGeneralEjo']);

    // Drawings
    Route::get('/drawings', [EjoEngineerController::class, 'getDrawings']);
    Route::post('/drawings', [EjoEngineerController::class, 'uploadDrawing']);
    Route::match(['put', 'post'], '/drawings/{id}', [EjoEngineerController::class, 'updateDrawing']);
    Route::delete('/drawings/{id}', [EjoEngineerController::class, 'deleteDrawing']);

    // Projects
    Route::get('/projects', [EjoEngineerController::class, 'getProjects']);
    Route::post('/projects', [EjoEngineerController::class, 'createProject']);
    Route::put('/projects/{id}', [EjoEngineerController::class, 'updateProject']);
    Route::delete('/projects/{id}', [EjoEngineerController::class, 'deleteProject']);
    Route::delete('/projects/{id}/handover-doc', [EjoEngineerController::class, 'deleteProjectHandoverDoc']);
    Route::post('/projects/upload-doc', [EjoEngineerController::class, 'uploadProjectDoc']);

    // Repair Parts
    Route::get('/repair-parts', [EjoEngineerController::class, 'getRepairParts']);
    Route::post('/repair-parts', [EjoEngineerController::class, 'createRepairPart']);
    Route::delete('/repair-parts/{id}', [EjoEngineerController::class, 'deleteRepairPart']);

    // WSP Materials
    Route::get('/wsp-materials', [EjoEngineerController::class, 'getWspMaterials']);
    Route::post('/wsp-materials/import', [EjoEngineerController::class, 'importWspMaterials']);

    // Users & Permissions
    Route::get('/users', [EjoEngineerController::class, 'getUsers']);
    Route::post('/users', [EjoEngineerController::class, 'createUser']);
    Route::post('/users/force-logout', [EjoEngineerController::class, 'forceLogoutUser']);
    Route::put('/users/bulk-reset-access', [EjoEngineerController::class, 'bulkResetUserAccess']);
    Route::put('/users/{username}', [EjoEngineerController::class, 'updateUser']);
    Route::put('/users/{username}/layout-settings', [EjoEngineerController::class, 'updateUserLayoutSettings']);
    Route::put('/users/{username}/access', [EjoEngineerController::class, 'updateUserAccess']);
    Route::delete('/users/{username}', [EjoEngineerController::class, 'deleteUser']);
    Route::post('/upload-avatar', [EjoEngineerController::class, 'uploadAvatar']);
    Route::put('/roles/access', [EjoEngineerController::class, 'updateRoleAccess']);

    // Settings
    Route::get('/settings', [EjoEngineerController::class, 'getSettings']);
    Route::put('/settings', [EjoEngineerController::class, 'updateSettings']);

    // Daily Engineer Activity Logs (Manual Input by Admin/Foreman + Date Filter)
    Route::get('/daily-activity-logs', [EjoEngineerController::class, 'getDailyActivityLogs']);
    Route::post('/daily-activity-logs', [EjoEngineerController::class, 'createDailyActivityLog']);
    Route::put('/daily-activity-logs/{id}', [EjoEngineerController::class, 'updateDailyActivityLog']);
    Route::delete('/daily-activity-logs/{id}', [EjoEngineerController::class, 'deleteDailyActivityLog']);

    // Notifications
    Route::get('/notifications', [EjoEngineerController::class, 'getNotifications']);
    Route::put('/notifications/read-all', [EjoEngineerController::class, 'markAllNotificationsRead']);
    Route::delete('/notifications', [EjoEngineerController::class, 'deleteNotifications']);

    // File Upload
    Route::post('/upload', [EjoEngineerController::class, 'uploadFile']);

    // Nuclear & Modular Database Reset (Khusus Role Server - EJO Engineer Modul Saja)
    Route::post('/nuclear', [EjoEngineerController::class, 'nuclearDatabase']);
    Route::post('/database/reset-module', [EjoEngineerController::class, 'resetModuleDatabase']);
});

