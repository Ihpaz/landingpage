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

Route::group(['middleware' => 'auth:api'], function () {
    Route::group(['namespace' => 'Master', 'prefix' => 'master'], function () {
        Route::group(['prefix' => 'location'], function () {
            Route::get('country', 'CountryController@getData');
            Route::get('province', 'ProvinceController@getData');
            Route::get('district', 'DistrictController@getData');
            Route::get('village', 'VillageController@getData');
            Route::get('wilayah', 'ProvinceController@getWilayah');
        });
    });
});