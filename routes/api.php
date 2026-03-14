<?php

use App\Http\Controllers\Api\AnalyticController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\IcsController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\PersonnelIcsController;
use App\Http\Controllers\Api\PersonnelProfileController;
use App\Http\Controllers\Api\PersonnelTaskController;
use App\Http\Controllers\Api\RulProfileController;
use App\Http\Controllers\Api\PersonnelController;
use App\Http\Controllers\Api\CheckInDetailHistoriesController;
use App\Http\Controllers\Api\IcsLogController;
use App\Http\Controllers\IcsCheckInDetailController;


Route::get('/status', function () {
    return response()->json(['status' => 'API is running'], 200);
});

//Login
Route::middleware("throttle:60,1")->prefix("auth")->group(function () {
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/login/app', [LoginController::class, 'loginThroughApp']);
    Route::post('/logout/app', [LoginController::class, 'logoutThroughApp']);
    Route::post('/validate-token', [LoginController::class, 'validateToken']);
});

Route::middleware(["auth:sanctum", "throttle:60,1"])->group(function () {
    Route::post('auth/logout', [LoginController::class, 'logout']);
});

//rul
Route::prefix('rul')->middleware(['rul.auth', 'throttle:60,1'])->group(function () {
    //dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    //analytics
    Route::get('/analytics', [AnalyticController::class, 'index']);
    Route::get('/analytics/map', [AnalyticController::class, 'map']);
    Route::get('/analytics/regions', [AnalyticController::class, 'regions']);
    Route::get('/analytics/region/{region}', [AnalyticController::class, 'region']);
    Route::get('/analytics/ics/{uuid}', [AnalyticController::class, 'show']);

    //management of ics 211 forms
    Route::get('/ics', [IcsController::class, 'index']);
    Route::get('/ics/search', [IcsController::class, 'search']);
    Route::post('/ics/create', [IcsController::class, 'store']);
    Route::post('/ics/join', [IcsController::class, 'joinIcs']);
    Route::get('/ics/{id}/show', [IcsController::class, 'show']);
    Route::post('/ics/{id}/edit', [IcsController::class, 'update']);
    Route::post('/ics/{id}/delete', [IcsController::class, 'destroy']);
    Route::post('/ics/{id}/status/{status}', [IcsController::class, 'updateStatus']);
    Route::post('/ics/checkin/{uuid}/status', [IcsController::class, 'updateCheckinDetailStatus']);
    Route::get('/ics/{icsUuid}/logs', [IcsLogController::class, 'icsRecordLogs']);
    
    //management of check-in details
    Route::get('/ics/{icsUuid}/checkin', [IcsCheckInDetailController::class, 'index']);
    Route::get('/ics/checkin/{uuid}', [IcsCheckInDetailController::class, 'show']);
    Route::post('/ics/{icsUuid}/checkin', [IcsCheckInDetailController::class, 'store']);
    Route::post('/ics/checkin/{uuid}/edit', [IcsCheckInDetailController::class, 'update']);
    Route::post('/ics/checkin/{uuid}/delete', [IcsCheckInDetailController::class, 'destroy']);
    
    //management of CheckInDetailHistories
    Route::get('/ics/checkin/{id}/history', [CheckInDetailHistoriesController::class, 'show']);
    Route::post('/ics/checkin/history/{id}/status/{status}', [CheckInDetailHistoriesController::class, 'updateStatus']);

    //ICS Logs and Activity
    Route::get('/logs/my-logs', [IcsLogController::class, 'myLogs']);
    Route::get('/logs/my-logs/action/{action}', [IcsLogController::class, 'myLogsByAction']);
    Route::get('/logs/my-activity-summary', [IcsLogController::class, 'myActivitySummary']);
    Route::post('/logs/my-logs/date-range', [IcsLogController::class, 'myLogsByDateRange']);

    //management personnel accounts
    Route::get('/personnel', [PersonnelController::class, 'index']);
    Route::post('/personnel/create', [PersonnelController::class, 'store']);
    Route::get('/personnel/{id}/show', [PersonnelController::class, 'show']);
    Route::post('/personnel/{id}/edit', [PersonnelController::class, 'update']);
    Route::post('/personnel/{id}/delete', [PersonnelController::class, 'destroy']);

    //managing own profile
    Route::get('/profile', [RulProfileController::class, 'show']);
    Route::post('/profile/update', [RulProfileController::class, 'update']);
    Route::post('/profile/change-avatar', [RulProfileController::class, 'updateAvatar']);
});

//personnel
Route::prefix('personnel')->middleware(['personnel.auth', 'throttle:60,1'])->group(function () {
    //dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);
    //analytics
    Route::get('/analytics', [AnalyticController::class, 'index']);
    Route::get('/analytics/map', [AnalyticController::class, 'map']);
    Route::get('/analytics/regions', [AnalyticController::class, 'regions']);
    Route::get('/analytics/region/{region}', [AnalyticController::class, 'region']);
    Route::get('/analytics/ics/{uuid}', [AnalyticController::class, 'show']);

    //Check own ICS 211 records
    Route::get('/ics', [PersonnelIcsController::class, 'index']);
    Route::get('/ics/latest', [PersonnelIcsController::class, 'latest']);
    Route::get('/ics/{id}/show', [PersonnelIcsController::class, 'show']);
    Route::post('/ics/checkin/{uuid}/status', [PersonnelIcsController::class, 'updateCheckinDetailStatus']);

    //manage own profile
    Route::get('/profile', [PersonnelProfileController::class, 'show']);
    Route::post('/profile/update', [PersonnelProfileController::class, 'update']);
    Route::post('/profile/change-avatar', [PersonnelProfileController::class, 'updateAvatar']);
});