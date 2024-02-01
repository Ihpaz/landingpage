<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Media Routes
|--------------------------------------------------------------------------
|
| MEDIA CMS
| DO NOT MODIFIED THIS CODE UNLESS YOU KNOW WHAT'S YOUR DOING
|
*/

Route::group(['namespace' => 'Media', 'prefix' => 'media'], function () {
    Route::get('document/{id}', 'AttachmentController@downloadPublic')->name('attachment.public.download');

    Route::group(['middleware' => 'auth'], function () {
        Route::get('attachment/{id}', 'AttachmentController@stream')->name('attachment.stream');
        Route::get('attachment/{id}/download', 'AttachmentController@download')->name('attachment.download');
        Route::delete('attachment/{id}/destroy', 'AttachmentController@destroy')->name('attachment.destroy');
    });
});
