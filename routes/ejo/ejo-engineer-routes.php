<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EjoEngineerController;

// Web View Route
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/ejo-engineer', function () {
        return view('ejo-engineer.index');
    })->name('ejo-engineer.index');
});

// API Routes
Route::prefix('api/ejo-engineer')->group(function () {
    // Auth & User Sessions
    Route::post('/login', [EjoEngineerController::class, 'login']);
    Route::post('/logout', [EjoEngineerController::class, 'logout']);
    Route::post('/heartbeat', [EjoEngineerController::class, 'heartbeat']);

    // Users
    Route::get('/users', [EjoEngineerController::class, 'getUsers']);
    Route::post('/users', [EjoEngineerController::class, 'createUser']);
    Route::post('/users/force-logout', [EjoEngineerController::class, 'forceLogoutUser']);
    Route::post('/users/seed-based-accounts', [EjoEngineerController::class, 'seedBasedAccounts']);
    Route::put('/users/bulk-reset-access', [EjoEngineerController::class, 'bulkResetUserAccess']);
    Route::put('/users/{username}', [EjoEngineerController::class, 'updateUser']);
    Route::put('/users/{username}/layout-settings', [EjoEngineerController::class, 'updateUserLayoutSettings']);
    Route::put('/users/{username}/access', [EjoEngineerController::class, 'updateUserAccess']);
    Route::delete('/users/{username}', [EjoEngineerController::class, 'deleteUser']);
    Route::post('/upload-avatar', [EjoEngineerController::class, 'uploadAvatar']);
    Route::put('/roles/access', [EjoEngineerController::class, 'updateRoleAccess']);

    // Standard EJO
    Route::get('/ejos', [EjoEngineerController::class, 'getEjos']);
    Route::post('/ejos', [EjoEngineerController::class, 'createEjo']);
    Route::put('/ejos/{id}', [EjoEngineerController::class, 'updateEjo']);
    Route::delete('/ejos/{id}', [EjoEngineerController::class, 'deleteEjo']);

    // General EJO
    Route::get('/general-ejos', [EjoEngineerController::class, 'getGeneralEjos']);
    Route::post('/general-ejos', [EjoEngineerController::class, 'createGeneralEjo']);
    Route::put('/general-ejos/{id}', [EjoEngineerController::class, 'updateGeneralEjo']);
    Route::delete('/general-ejos/{id}', [EjoEngineerController::class, 'deleteGeneralEjo']);

    // Projects
    Route::get('/projects', [EjoEngineerController::class, 'getProjects']);
    Route::post('/projects', [EjoEngineerController::class, 'createProject']);
    Route::put('/projects/{id}', [EjoEngineerController::class, 'updateProject']);
    Route::delete('/projects/{id}', [EjoEngineerController::class, 'deleteProject']);
    Route::delete('/projects/{id}/handover-doc', [EjoEngineerController::class, 'deleteProjectHandoverDoc']);
    Route::post('/projects/upload-doc', [EjoEngineerController::class, 'uploadProjectDoc']);

    // Drawings
    Route::get('/drawings', [EjoEngineerController::class, 'getDrawings']);
    Route::post('/drawings', [EjoEngineerController::class, 'uploadDrawing']);
    Route::put('/drawings/{id}', [EjoEngineerController::class, 'updateDrawing']);
    Route::delete('/drawings/{id}', [EjoEngineerController::class, 'deleteDrawing']);

    // Repair Parts
    Route::get('/repair-parts', [EjoEngineerController::class, 'getRepairParts']);
    Route::post('/repair-parts', [EjoEngineerController::class, 'createRepairPart']);
    Route::delete('/repair-parts/{id}', [EjoEngineerController::class, 'deleteRepairPart']);

    // Wsp Materials
    Route::get('/wsp-materials', [EjoEngineerController::class, 'getWspMaterials']);
    Route::post('/wsp-materials/import', [EjoEngineerController::class, 'importWspMaterials']);

    // Settings
    Route::get('/settings', [EjoEngineerController::class, 'getSettings']);
    Route::put('/settings', [EjoEngineerController::class, 'updateSettings']);

    // Notifications
    Route::get('/notifications', [EjoEngineerController::class, 'getNotifications']);
    Route::put('/notifications/read-all', [EjoEngineerController::class, 'markAllNotificationsRead']);
    Route::delete('/notifications', [EjoEngineerController::class, 'deleteNotifications']);

    // File Upload
    Route::post('/upload', [EjoEngineerController::class, 'uploadFile']);
});
