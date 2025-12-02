<?php

use App\Http\Controllers\Api\LoginController;
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
    
    //admin
    Route::prefix('admin')->group(function(){
        //management of rul accounts
        //management of personnel accounts
        //management of admin accounts
    });
});
//rul
Route::prefix('rul')->middleware([RulMiddleware::class, 'throttle:60,1'])->group(function(){
    //management personnel accounts
    //managing of own account
    //management of personnel task
    //management of ics 211 forms
});

//personnel
Route::prefix('personnel')->middleware([PersonnelMiddleware::class, 'throttle:60,1'])->group(function(){
    //personnel-protected endpoints
});