<?php

use App\Models\Menu\Modules;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Module Routes
|--------------------------------------------------------------------------
|
| MODULE
| DO NOT MODIFIED THIS CODE UNLESS YOU KNOW WHAT'S YOUR DOING
|
*/


Route::group(['namespace' => 'Module', 'middleware' => 'auth'], function () {
    Route::get('module/{slug}', 'GenerateModuleController@index')->name('module.index');
    Route::get('module/{slug}/create', 'GenerateModuleController@create')->name('module.create');
    Route::post('module/{slug}','GenerateModuleController@store')->name('module.store');
    Route::get('module/{slug}/{id}','GenerateModuleController@show')->name('module.show');
    Route::get('module/{slug}/{id}/edit','GenerateModuleController@edit')->name('module.edit');
    Route::put('module/{slug}/{id}','GenerateModuleController@update')->name('module.update');
    Route::delete('module/{slug}/{id}','GenerateModuleController@destroy')->name('module.destroy');

    Route::group(['prefix' => 'datatable'], function () {
        Route::get('module/{slug}', 'GenerateModuleController@datatable')->name('module.datatable');
    });
});