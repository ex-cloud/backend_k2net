<?php

use App\Http\Controllers\Api\V1\Admin\CategoryController;
use App\Http\Controllers\Api\V1\Admin\LoginController;
use App\Http\Controllers\Api\V1\Admin\PostController;
use App\Http\Controllers\Api\V1\Admin\TagController;
use Illuminate\Support\Facades\Route;

// Prefix Api version 1
Route::prefix('v1')->group(function () {
    // Prefix untuk admin routes
    Route::prefix('admin')->group(function () {

        // Public routes (tanpa auth)
        Route::post('/login', [LoginController::class, 'index'])->middleware('throttle:5,1');

        // Protected routes (dengan middleware auth:api)
        Route::middleware('auth:api')->group(function () {
            // data user
            Route::get('/get-user', [LoginController::class, 'getUser']);

            // refresh token dan logout
            Route::post('/refresh-token', [LoginController::class, 'refreshToken']);
            Route::post('/logout', [LoginController::class, 'logout']);

            //Tags
            Route::apiResource('/tags', TagController::class);

            //Category
            Route::apiResource('/categories', CategoryController::class);

            //Posts
            Route::apiResource('/posts', PostController::class);
        });
    });

});
