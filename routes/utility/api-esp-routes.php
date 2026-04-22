<?php

use Illuminate\Support\Facades\Route;
 
use App\Http\Controllers\Utility\DashboardUtilityController; 

Route::prefix('utility')->group(function () {
  
    Route::get('/users/approvers', [DashboardUtilityController::class, 'getApprovers']);
});
