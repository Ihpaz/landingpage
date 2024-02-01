<?php

use Illuminate\Support\Facades\Route;

// Verify token from outside
Route::group(['namespace' => 'Mfa', 'prefix' => 'api/mfa', 'middleware' => ['json','auth:api']], function () {
    Route::post('verify-otp', 'AuthenticatorController@verification')->name('mfa.otp.verification')->withoutMiddleware('web');
});

Route::group(['namespace' => 'Mfa', 'middleware' => 'auth', 'prefix' => 'mfa'], function () {
    Route::post('challenge', 'AuthenticatorController@challenge')->name('mfa.challenge')->middleware('mfa');
    Route::delete('otp/{id}', 'AuthenticatorController@destroy')->name('mfa.otp.destroy');
    // Datatables ajax server side
    Route::group(['prefix' => 'ajax', 'middleware' => 'json'], function () {
        Route::get('otp', 'AuthenticatorController@datatable')->name('mfa.ajax.otp');
    });
});
