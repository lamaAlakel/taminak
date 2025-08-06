<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\User\UserAuthController;
use \App\Http\Controllers\Api\Admin\AdminAuthController ;
use \App\Http\Controllers\Api\Company\CompanyAuthController ;
use \App\Http\Controllers\Api\Admin\CompanyController ;
use \App\Http\Controllers\Api\Admin\CategoryController ;
use \App\Http\Controllers\Api\Company\PlanController ;
use \App\Http\Controllers\Api\Company\ServiceProviderController ;
use \App\Http\Controllers\Api\Company\OfferController ;
use \App\Http\Controllers\Api\User\RateController ;
use \App\Http\Controllers\Api\Admin\UserController ;
use \App\Http\Controllers\Api\Admin\StatisticsController ;
use \App\Http\Controllers\Api\Admin\AdController;
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

        Route::get('offers', [\App\Http\Controllers\Api\User\OfferController::class, 'index']);
        Route::get('plans', [\App\Http\Controllers\Api\User\PlanController::class, 'index']);

        Route::get('companies', [CompanyController::class, 'index']);
        Route::get('companies/{company}/plans', [\App\Http\Controllers\Api\User\PlanController::class, 'plansByCompany']);

        Route::get('categories', [CategoryController::class, 'index']);
        Route::get('ads', [AdController::class,'index']);

        Route::get('categories/{category}/plans', [\App\Http\Controllers\Api\User\PlanController::class, 'plansByCategory']);

        Route::apiResource('rates', RateController::class)
            ->only(['index','store','update','destroy']);
    });
});

Route::prefix('admin')->group(function () {
    Route::post('register', [AdminAuthController::class, 'register']);
    Route::post('login',    [AdminAuthController::class, 'login']);

    Route::middleware('auth:admin-api')->group(function () {

        Route::controller(UserController::class)->group(function() {
            Route::post('logout','logout');
            Route::get('me','me');
            Route::put('profile','updateProfile');
        });

        Route::get('statistics',            [StatisticsController::class, 'index']);

        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('companies',  CompanyController::class);
        Route::apiResource('ads', AdController::class);

        Route::prefix('users')->controller(UserController::class)->group(function(){
            Route::get('index',         'index'  );
            Route::post('{id}/block',   'block'  );
            Route::post('{id}/unblock', 'unblock');
            Route::delete('{id}',       'destroy');
        });
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
