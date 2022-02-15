<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'auth', 'middleware' => 'api'], function () {
    // Auth
    Route::post('register', [\App\Http\Controllers\AuthController::class, 'register']);
    Route::get('401', [\App\Http\Controllers\AuthController::class, 'index']);
    Route::post('otp', [\App\Http\Controllers\AuthController::class, 'otp']);
    Route::post('otp/resend', [\App\Http\Controllers\AuthController::class, 'resendOtp'])->middleware('throttle:1:1');
    Route::post('token', [\App\Http\Controllers\AuthController::class, 'token']);
    Route::post('token/refresh', [\App\Http\Controllers\AuthController::class, 'refreshToken']);
});

Route::group(['middleware' => 'auth:api'], function () {
    // Auth
    Route::group(['prefix' => 'auth'], function () {
        Route::post('token/revoke', [\App\Http\Controllers\AuthController::class, 'revokeToken']);
        Route::get('token/check', [\App\Http\Controllers\AuthController::class, 'checkToken']);
    });

    //Product
    Route::resource('product', \App\Http\Controllers\ProductController::class);

});
