<?php

use App\Modules\Users\Controllers\AuthController;
use App\Modules\Users\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth'], function (){
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('users', UserController::class);

    Route::prefix('users')->group(function () {
        Route::patch('{id}/restore', [UserController::class, 'restore']);
    });
});
