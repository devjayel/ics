<?php

use App\Http\Controllers\Api\IcsController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\PersonnelProfileController;
use App\Http\Controllers\Api\PersonnelTaskController;
use App\Http\Controllers\Api\RulProfileController;
use App\Http\Controllers\Api\PersonnelController;
use App\Http\Controllers\Api\CheckInDetailHistoriesController;


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
    //management of ics 211 forms
    Route::get('/ics', [IcsController::class, 'index']);
    Route::post('/ics/create', [IcsController::class, 'store']);
    Route::get('/ics/{id}/show', [IcsController::class, 'show']);
    Route::post('/ics/{id}/edit', [IcsController::class, 'update']);
    Route::post('/ics/{id}/delete', [IcsController::class, 'destroy']);
    Route::post('/ics/{id}/status/{status}', [IcsController::class, 'updateStatus']);

    //management of CheckInDetailHistories
    Route::get('/ics/checkin/{id}/history', [CheckInDetailHistoriesController::class, 'show']);
    Route::post('/ics/checkin/history/{id}/status/{status}', [CheckInDetailHistoriesController::class, 'updateStatus']);

    //management personnel accounts
    Route::get('/personnel', [PersonnelController::class, 'index']);
    Route::post('/personnel/create', [PersonnelController::class, 'store']);
    Route::get('/personnel/{id}/show', [PersonnelController::class, 'show']);
    Route::post('/personnel/{id}/edit', [PersonnelController::class, 'update']);
    Route::post('/personnel/{id}/delete', [PersonnelController::class, 'destroy']);

    //managing own profile
    Route::get('/profile', [RulProfileController::class, 'show']);
    Route::post('/profile/update', [RulProfileController::class, 'update']);
});

//personnel
Route::prefix('personnel')->middleware(['personnel.auth', 'throttle:60,1'])->group(function () {
    //manage tasks
    Route::get('/task', [PersonnelTaskController::class, 'index']);
    Route::get('/task/{id}/show', [PersonnelTaskController::class, 'show']);
    Route::post('/task/{id}/status/{status}', [PersonnelTaskController::class, 'updateStatus']);

    //CheckInDetailsHistories
    Route::get('/ics/checkin/{id}/history', [CheckInDetailHistoriesController::class, 'show']);
    Route::post('/ics/checkin/history/{id}/status/{status}', [CheckInDetailHistoriesController::class, 'updateStatus']);

    //manage own profile
    Route::get('/profile', [PersonnelProfileController::class, 'show']);
    Route::post('/profile/update', [PersonnelProfileController::class, 'update']);
});