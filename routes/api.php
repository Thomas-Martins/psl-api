<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Authenticated routes
Route::group(['middleware' => ['auth:api']], function () {
    //ADMIN ROUTES
    Route::group(['middleware' => 'roles:admin'], function () {
        //USERS
        Route::apiResource('/users', UsersController::class);

        //ROLES
        Route::apiResource('/roles', RolesController::class);
    });
});
