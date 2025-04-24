<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CarriersController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\SuppliersController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Authenticated routes
Route::group(['middleware' => ['auth:api']], function () {
    //PRODUCTS
    Route::apiResource('/products', ProductsController::class)->except(['store', 'update', 'destroy']);

    //CATEGORIES
    Route::apiResource('/categories', CategoriesController::class)->except(['store', 'update', 'destroy']);

    //COMMANDS
    Route::apiResource('/orders', OrdersController::class)->except(['update', 'destroy']);

    //CART
    Route::post('/carts', [CartController::class, 'store']);
    Route::delete('/carts/user/{user}', [CartController::class, 'deleteCartUser']);
    Route::get('/carts/user/{user}', [CartController::class, 'showCartUser']);

    //USERS
    Route::put('/users/{user}', [UsersController::class, 'update']);
    Route::get('/users/{user}', [UsersController::class, 'show']);

    //ADMIN ROUTES
    Route::group(['middleware' => ['roles:admin']], function () {
        //USERS
        Route::apiResource('/users', UsersController::class)->except(['update', 'show']);

        //ROLES
        Route::apiResource('/roles', RolesController::class);
    });

    //Admin and Gestionnaire routes
    Route::group(['middleware' => ['roles:admin,gestionnaire']], function () {
        //PRODUCTS
        Route::apiResource('/products', ProductsController::class)->except(['index', 'show']);

        //CATEGORIES
        Route::apiResource('/categories', CategoriesController::class)->except(['index', 'show']);

        //SUPPLIERS
        Route::apiResource('/suppliers', SuppliersController::class);

        //CARRIERS
        Route::apiResource('/carriers', CarriersController::class);

        //STORES
        Route::apiResource('/stores', StoreController::class);

    });
});
