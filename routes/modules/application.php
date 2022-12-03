<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Application\NotificationSystemController;
use App\Http\Controllers\Application\WebConfigurationController;
use App\Http\Controllers\Application\DownloadController;
use App\Http\Controllers\Application\Authentication\RoleController;
use App\Http\Controllers\Application\Authentication\PermissionController;
use App\Http\Controllers\Application\Authentication\MenuController;
use App\Http\Controllers\Application\Authentication\PermissionmenuController;
use App\Http\Controllers\Application\GeneralConfiguration\GroupConfigurationController;
use App\Http\Controllers\Application\GeneralConfiguration\ConfigurationController;

/*
|--------------------------------------------------------------------------
| Web Application Routes
|--------------------------------------------------------------------------
|
| \application\...
|
*/

Route::group(['prefix' => 'authentication'], function () {
    Route::resource('menu', MenuController::class);
    Route::resource('permission', PermissionController::class)->except(['show']);
    Route::get('permission/dtble', [PermissionController::class, 'dtble']);

    Route::resource('role', RoleController::class)->except(['show']);
    Route::get('role/dtble', [RoleController::class, 'dtble']);
    Route::get('role/select', [RoleController::class, 'select']);

    Route::resource('permissionmenu', PermissionmenuController::class)->except(['show']);
    Route::get('permissionmenu/dtble/{role_id}', [PermissionmenuController::class, 'dtble']);
    Route::post('permissionmenu/copy', [PermissionmenuController::class, 'copy']);

    Route::resource('user', UserController::class)->except(['show']);
    Route::get('user/dtble', [UserController::class, 'dtble']);
    Route::get('user/select', [UserController::class, 'select']);
});

Route::group(['prefix' => 'general-configuration'], function () {
    Route::resource('configuration-group', GroupConfigurationController::class)->except(['show']);
    Route::get('configuration-group/dtble', [GroupConfigurationController::class, 'dtble']);
    Route::get('configuration-group/select', [GroupConfigurationController::class, 'select']);

    Route::resource('configuration', ConfigurationController::class)->except(['show']);
    Route::get('configuration/dtble', [ConfigurationController::class, 'dtble']);
    Route::post('configuration/copy', [ConfigurationController::class, 'copy']);
});

Route::resource('web-configuration', WebConfigurationController::class)->except(['show']);

Route::resource('download', DownloadController::class)->except(['show']);
Route::get('download/dtble', [DownloadController::class, 'dtble']);
Route::get('download/{id}', [DownloadController::class, 'download']);

Route::resource('notification-system', NotificationSystemController::class)->except(['show']);
Route::get('notification-system/dtble', [NotificationSystemController::class, 'dtble']);
Route::get('notification-system/dtble/content/{id}', [NotificationSystemController::class, 'dtbleContent']);
Route::post('notification-system/content', [NotificationSystemController::class, 'storeContent']);
Route::put('notification-system/content/{id}', [NotificationSystemController::class, 'updateContent']);
Route::delete('notification-system/content/{id}', [NotificationSystemController::class, 'destroyContent']);
Route::post('notification-system/send', [NotificationSystemController::class, 'send']);
Route::get('notification-system/user', [NotificationSystemController::class, 'getNotificationUser']);
Route::get('notification-system/user/read/{id}', [NotificationSystemController::class, 'readNotificationUser']);
