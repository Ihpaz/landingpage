<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['namespace' => 'Backend', 'middleware' => ['auth', 'mfa']], function () {
    Route::get('/', function () {
        return redirect()->route('backend.dashboard.index');
    });
    Route::get('dashboard', 'DashboardController@index')->name('backend.dashboard.index');
    Route::get('profile', 'ProfileController@index')->name('backend.profile.index');
    Route::get('profile/edit', 'ProfileController@edit')->name('backend.profile.edit');
    Route::post('profile/edit', 'ProfileController@update')->name('backend.profile.update');

    Route::resource('notifications', 'NotificationController', ['names' => 'backend.notification'])->only(['index', 'show']);

    Route::group(['prefix' => 'personal'], function () {
        Route::post('email', 'PersonalController@storeEmail')->name('backend.personal.email.store');
        Route::get('email/{id}/verify', 'PersonalController@sendEmailVerification')->name('backend.personal.email.send');
        Route::delete('email/{id}', 'PersonalController@destroyEmail')->name('backend.personal.email.destroy');

        Route::post('address', 'PersonalController@storeAddress')->name('backend.personal.address.store');
        Route::get('address/{id}/edit', 'PersonalController@editAddress')->name('backend.personal.address.edit');
        Route::put('address/{id}', 'PersonalController@updateAddress')->name('backend.personal.address.update');
        Route::delete('address/{id}', 'PersonalController@destoryAddress')->name('backend.personal.address.destroy');

        Route::post('document', 'DocumentController@store')->name('backend.personal.document.store');
        Route::delete('document/{id}', 'DocumentController@destroy')->name('backend.personal.document.destroy');
    });

    Route::get('geo-ip/{ip}', "IpGeolocationController@show")->name('backend.geo.ip');
    Route::get('device/toggle-status/{id}', 'UserDeviceController@toggle')->name('backend.device.status.toggle');

    // Admin Api
    Route::group(['prefix' => 'api'], function () {
        Route::get('notifications', 'NotificationController@notifications')->name('backend.api.notification');
        Route::get('notifications/redirect/{id}', 'NotificationController@redirectUrlNotification')->name('backend.api.notification.redirect');
        Route::get('notifications/count', 'NotificationController@alertCountUserNotification')->name('backend.api.notification.count');
    });

    Route::delete('token/{id}', 'AccessTokenController@destroy')->name('backend.access.token.destroy');

    // Datatables ajax server side
    Route::group(['prefix' => 'ajax'], function () {
        Route::get('token', 'AccessTokenController@datatable')->name('backend.ajax.access.token');
        Route::get('notifications', 'NotificationController@datatable')->name('backend.ajax.notification');
        Route::get('personal/email', 'PersonalController@datatableEmail')->name('backend.ajax.personal.email');
        Route::get('personal/address', 'PersonalController@datatableAddress')->name('backend.ajax.personal.address');
        Route::get('personal/document', 'DocumentController@datatable')->name('backend.ajax.personal.document');
        Route::get('user/device', 'UserDeviceController@datatable')->name('backend.ajax.device.user');
        Route::get('user/login', 'RecentLoginController@datatable')->name('backend.ajax.recent.login');
    });
});
