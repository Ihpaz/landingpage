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

Route::group(['middleware' => 'auth:api'], function () {
    Route::group(['namespace' => 'Api\Cms'], function () {
        Route::apiResource('users', 'UserManagementController');
        Route::apiResource('roles', 'RoleManagementController')->only(['index', 'show']);
        Route::apiResource('permissions', 'PermissionManagementController')->only(['index', 'show']);
    });

    Route::group(['namespace' => 'Backend'], function () {
        Route::post('notifications/send-message', 'NotificationController@notificationMessage');
    });
});
