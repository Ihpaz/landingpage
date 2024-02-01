<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Cms Routes
|--------------------------------------------------------------------------
|
| SUPERADMIN CMS
| DO NOT MODIFIED THIS CODE UNLESS YOU KNOW WHAT'S YOUR DOING
|
*/

Route::group(['namespace' => 'Cms', 'middleware' => 'auth'], function () {
    Route::get('info', 'SystemInformationController@index')->name('cms.info.index');
    Route::group(['prefix' => 'cms'], function () {
        Route::resource('backup-management', 'BackupManagementController', ['names' => 'cms.backup'])->only(['index', 'store']);
        Route::resource('activity-log', 'ActivityLogController', ['names' => 'cms.activity'])->only(['index', 'show']);
        Route::resource('scheduler', 'SchedulerManagementController', ['names' => 'cms.scheduler'])->only(['index']);
        Route::delete('backup-management', 'BackupManagementController@destroy')->name('cms.backup.destroy');

        Route::group(['prefix' => 'user-management'], function () {
            Route::resource('user', 'UserManagementController', ['names' => 'cms.user']);
            Route::resource('role', 'RoleManagementController', ['names' => 'cms.role']);
            Route::resource('permission', 'PermissionManagementController', ['names' => 'cms.permission']);
            Route::get('impersonate/user/{user}', 'UserManagementController@impersonate')->name('cms.user.impersonate');
            Route::get('impersonate/leave', 'UserManagementController@leaveImpersonate')->name('cms.user.impersonate.leave');
        });
        Route::resource('menu', 'MenuManagementController', ['names' => 'cms.menu']);
        Route::resource('module','ModuleManagementController', ['names' => 'cms.module']);
        Route::get('module/{id}/field', 'ModuleManagementController@indexField')->name('cms.module.field.index');
        Route::get('module-field/{id}/edit', 'ModuleManagementController@editField')->name('cms.module.field.edit');
        Route::post('module-field/{id}','ModuleManagementController@storeField')->name('cms.module.field.store');
        Route::put('module-field/{id}','ModuleManagementController@updateField')->name('cms.module.field.update');
        Route::delete('module-field/{id}','ModuleManagementController@destroyField')->name('cms.module.field.destroy');
    });

    // Import CMS 
    Route::group(['prefix' => 'cms/import'], function () {
        Route::post('user/excel', 'UserManagementController@importExcel')->name('cms.user.import.excel');
    });

    // CMS Api
    Route::group(['prefix' => 'cms/api'], function () {
        Route::post('scheduler/run', 'SchedulerManagementController@run')->name('cms.api.scheduler.run');
        Route::get('backup/download', 'BackupManagementController@download')->name('cms.api.backup.download');
        Route::post('menu/order','MenuManagementController@updateOrder')->name('cms.api.menu.update.order');
        Route::post('module-field/order','ModuleManagementController@updateOrder')->name('cms.api.module.update.order');
    });

    // CMS Datatables server side ajax
    Route::group(['prefix' => 'cms/datatable'], function () {
        Route::get('user', 'UserManagementController@datatable')->name('cms.datatable.user');
        Route::get('role', 'RoleManagementController@datatable')->name('cms.datatable.role');
        Route::get('permission', 'PermissionManagementController@datatable')->name('cms.datatable.permission');
        Route::get('activity', 'ActivityLogController@datatable')->name('cms.datatable.activity');
        Route::get('backup', 'BackupManagementController@datatable')->name('cms.datatable.backup');
        Route::get('scheduler', 'SchedulerManagementController@datatable')->name('cms.datatable.scheduler');
        Route::get('jobs-failed', 'SchedulerManagementController@datatableJobFailed')->name('cms.datatable.jobs.failed');
        Route::get('module', 'ModuleManagementController@datatable')->name('cms.datatable.module');
        Route::get('module-field', 'ModuleManagementController@datatableModuleField')->name('cms.datatable.module.field');
    });
});
/**
 * END SUPERADMIN CMS
 */
