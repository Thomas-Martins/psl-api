<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::get('/test', function () {
    return response()->json(['message' => 'Hello world!']);
});

// Authenticated routes
Route::group(['middleware' => ['auth:api']], function () {
    //ADMIN
    Route::group(['middleware' => 'roles:admin'], function () {
        Route::apiResource('/users', UsersController::class);
    });
});
