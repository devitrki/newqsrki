<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ExternalVendor\TemplateSalesController;
use App\Http\Controllers\ExternalVendor\TargetVendorController;
use App\Http\Controllers\ExternalVendor\SendVendorController;

/*
|--------------------------------------------------------------------------
| Web Application Routes
|--------------------------------------------------------------------------
|
| \external-vendor\...
|
*/

Route::resource('template-sales', TemplateSalesController::class)->except(['show']);
Route::get('template-sales/dtble', [TemplateSalesController::class, 'dtble']);
Route::get('template-sales/select', [TemplateSalesController::class, 'select']);
Route::get('template-sales/{id}/configuration/dtble', [TemplateSalesController::class, 'dtbleConf']);
Route::post('template-sales/{id}/configuration', [TemplateSalesController::class, 'storeConf']);
Route::post('template-sales/{id}/configuration/delete', [TemplateSalesController::class, 'destroyConf']);

Route::resource('target-vendor', TargetVendorController::class)->except(['show']);
Route::get('target-vendor/dtble', [TargetVendorController::class, 'dtble']);
Route::get('target-vendor/select', [TargetVendorController::class, 'select']);
Route::get('target-vendor/{id}/configuration/dtble', [TargetVendorController::class, 'dtbleConf']);
Route::post('target-vendor/{id}/configuration', [TargetVendorController::class, 'storeConf']);
Route::post('target-vendor/{id}/configuration/delete', [TargetVendorController::class, 'destroyConf']);

Route::resource('send-vendor', SendVendorController::class)->except(['show']);
Route::get('send-vendor/dtble', [SendVendorController::class, 'dtble']);
Route::get('send-vendor/select', [SendVendorController::class, 'select']);
Route::post('send-vendor/send', [SendVendorController::class, 'send']);
Route::get('send-vendor/download', [SendVendorController::class, 'download']);
Route::get('send-vendor/receive/{dateFrom}/{dateUntil}', [SendVendorController::class, 'receive']);
Route::get('send-vendor/clear-test', [SendVendorController::class, 'clearTest']);
