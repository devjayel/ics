<?php

use App\Http\Controllers\Api\IcsController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\PersonnelProfileController;
use App\Http\Controllers\Api\PersonnelTaskController;
use App\Http\Controllers\Api\RulProfileController;
use App\Http\Controllers\Api\PersonnelController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Middleware\PersonnelMiddleware;
use App\Http\Middleware\RulMiddleware;


Route::get('/status', function () {
    return response()->json(['status' => 'API is running'], 200);
});

//Login
Route::middleware("throttle:60,1")->prefix("auth")->group(function(){
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/login/app', [LoginController::class, 'loginThroughApp']);
    Route::post('/logout/app', [LoginController::class, 'logoutThroughApp']);
});

Route::middleware(["auth:sanctum","throttle:60,1"])->group(function(){
    Route::post('auth/logout', [LoginController::class, 'logout']);
});

//rul
Route::prefix('rul')->middleware(['rul.auth', 'throttle:60,1'])->group(function(){
    //management of ics 211 forms
    Route::get('/ics', [IcsController::class, 'index']);
    Route::post('/ics/create', [IcsController::class, 'store']);
    Route::get('/ics/{id}/show', [IcsController::class, 'show']);
    Route::post('/ics/{id}/edit', [IcsController::class, 'update']);
    Route::post('/ics/{id}/delete', [IcsController::class, 'destroy']);

    //management personnel accounts
    Route::get('/personnel', [PersonnelController::class, 'index']);
    Route::post('/personnel/create', [PersonnelController::class, 'store']);
    Route::get('/personnel/{id}/show', [PersonnelController::class, 'show']);
    Route::post('/personnel/{id}/edit', [PersonnelController::class, 'update']);
    Route::post('/personnel/{id}/delete', [PersonnelController::class, 'destroy']);

    //management of personnel task
    Route::get('/task', [TaskController::class, 'index']);
    Route::post('/task/create', [TaskController::class, 'store']);
    Route::get('/task/{id}/show', [TaskController::class, 'show']);
    Route::post('/task/{id}/edit', [TaskController::class, 'update']);
    Route::post('/task/{id}/delete', [TaskController::class, 'destroy']);
    Route::post('/task/{id}/complete', [TaskController::class, 'complete']);
    //managing own profile
    Route::get('/profile/{id}', [RulProfileController::class, 'show']);
    Route::post('/profile/{id}/edit', [RulProfileController::class, 'update']);
});

//personnel
Route::prefix('personnel')->middleware(['personnel.auth', 'throttle:60,1'])->group(function(){
    //manage tasks
    Route::resource('task', PersonnelTaskController::class);
    //manage own profile
    Route::resource('profile', PersonnelProfileController::class);
});