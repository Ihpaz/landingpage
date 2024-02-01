<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentification Routes
|--------------------------------------------------------------------------
|
| AUTHENTIFICATIONS
| DO NOT MODIFIED THIS CODE UNLESS YOU KNOW WHAT'S YOUR DOING
|
*/

Route::group(['namespace' => 'Auth'], function () {
    Route::get('login', 'LoginController@index')->name('auth.login.index');
    Route::post('login', 'LoginController@login')->name('auth.login');
    Route::get('logout', 'LoginController@logout')->name('auth.logout');

    Route::get('register', 'RegisterController@index')->name('auth.register.index');
    Route::post('register', 'RegisterController@store')->name('auth.register');

    Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/reset', 'ResetPasswordController@reset')->name('password.update');

    Route::group(['prefix' => 'oauth'], function() {
        Route::get('google/redirect', 'OAuthController@redirectToGoogleProvider')->name('oauth.google.redirect');
        Route::get('google/callback', 'OAuthController@handleGoogleProviderCallback')->name('oauth.google.callback');
    });    
});
