<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Master Routes
|--------------------------------------------------------------------------
|
*/

Route::group(['namespace' => 'Master', 'middleware' => 'auth', 'prefix' => 'master'], function () {
    // Location
    Route::group(['prefix' => 'location'], function () {
        Route::resource('country', 'CountryController', ['names' => 'master.location.country'])->except(['show']);
        Route::resource('currency', 'CurrencyController', ['names' => 'master.location.currency'])->except(['show']);
        Route::resource('province', 'ProvinceController', ['names' => 'master.location.province'])->except(['show']);
        Route::resource('regency', 'RegencyController', ['names' => 'master.location.regency'])->except(['show']);
        Route::resource('district', 'DistrictController', ['names' => 'master.location.district'])->except(['show']);
        Route::resource('village', 'VillageController', ['names' => 'master.location.village'])->except(['show']);
    });

    // Api
    Route::group(['prefix' => 'api'], function () {
        // Location
        Route::get('country', 'CountryController@getData')->name('master.api.country');
        Route::get('currency', 'CurrencyController@getData')->name('master.api.currency');
        Route::get('province', 'ProvinceController@getData')->name('master.api.province');
        Route::get('regency', 'RegencyController@getData')->name('master.api.regency');
        Route::get('district', 'DistrictController@getData')->name('master.api.district');
        Route::get('village', 'VillageController@getData')->name('master.api.village');
    });

    // Datatables ajax server side
    Route::group(['prefix' => 'datatable'], function () {
        // Location
        Route::get('country', 'CountryController@datatable')->name('master.datatable.country');
        Route::get('currency', 'CurrencyController@datatable')->name('master.datatable.currency');
        Route::get('province', 'ProvinceController@datatable')->name('master.datatable.province');
        Route::get('regency', 'RegencyController@datatable')->name('master.datatable.regency');
        Route::get('district', 'DistrictController@datatable')->name('master.datatable.district');
        Route::get('village', 'VillageController@datatable')->name('master.datatable.village');
    });
});
