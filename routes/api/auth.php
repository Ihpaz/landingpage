<?php

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

Route::group(['namespace' => 'Api\Auth'], function () {
    Route::get('auth/me', 'AuthenticationController@me')->middleware(['auth:api']);
    Route::post('auth/login', 'AuthenticationController@createToken');
    Route::post('auth/logout', 'AuthenticationController@revokeToken')->middleware(['auth:api']);
    Route::post('auth/revoke', 'AuthenticationController@revokeAllToken')->middleware(['auth:api']);
    Route::post('auth/forgot-password', 'AuthenticationController@sendResetLinkEmail');
    Route::post('auth/change-password', 'AuthenticationController@changePassword')->middleware(['auth:api']);

    Route::get('oauth/google/redirect','SocialiteController@redirectToGoogleProvider')->middleware(['web']);
    Route::get('oauth/google/callback','SocialiteController@handleGoogleProviderCallback')->middleware(['web']);
});