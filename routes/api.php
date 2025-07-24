<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\UserAuthController;
use \App\Http\Controllers\Api\AdminAuthController ;
use \App\Http\Controllers\Api\CompanyAuthController ;
use \App\Http\Controllers\Api\CompanyController ;
use \App\Http\Controllers\Api\CategoryController ;
use \App\Http\Controllers\Api\PlanController ;
use \App\Http\Controllers\Api\ServiceProviderController ;
use \App\Http\Controllers\Api\OfferController ;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('user')->group(function () {
    Route::post('register', [UserAuthController::class, 'register']);
    Route::post('login',    [UserAuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout',       [UserAuthController::class, 'logout']);
        Route::get('me',            [UserAuthController::class, 'me']);
        Route::put('profile',       [UserAuthController::class, 'updateProfile']);
    });
});

Route::prefix('admin')->group(function () {
    Route::post('register', [AdminAuthController::class, 'register']);
    Route::post('login',    [AdminAuthController::class, 'login']);
    Route::middleware('auth:admin-api')->group(function () {
        Route::post('logout',       [AdminAuthController::class, 'logout']);
        Route::get('me',            [AdminAuthController::class, 'me']);
        Route::put('profile',       [AdminAuthController::class, 'updateProfile']);

        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('companies',  CompanyController::class);
    });
});

Route::prefix('company')->group(function () {
    Route::post('register', [CompanyAuthController::class, 'register']);
    Route::post('login', [CompanyAuthController::class, 'login']);
    Route::middleware('auth:company-api')->group(function () {
        Route::post('logout', [CompanyAuthController::class, 'logout']);
        Route::get('me', [CompanyAuthController::class, 'me']);
        Route::put('profile', [CompanyAuthController::class, 'updateProfile']);

        Route::apiResource('plans', PlanController::class);
        Route::apiResource('service-providers', ServiceProviderController::class);
        Route::apiResource('offers', OfferController::class);
        Route::get('categories', [CategoryController::class, 'index']);
    });
});
