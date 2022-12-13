<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Tax\FtpGovernmentController;
use App\Http\Controllers\Tax\SendTaxController;

/*
|--------------------------------------------------------------------------
| Web Application Routes
|--------------------------------------------------------------------------
|
| \tax\...
|
*/

Route::resource('ftp-government', FtpGovernmentController::class)->except(['show']);
Route::get('ftp-government/dtble', [FtpGovernmentController::class, 'dtble']);
Route::get('ftp-government/select', [FtpGovernmentController::class, 'select']);

Route::resource('send-tax', SendTaxController::class)->except(['show']);
Route::get('send-tax/dtble', [SendTaxController::class, 'dtble']);
Route::get('send-tax/select', [SendTaxController::class, 'select']);
Route::post('send-tax/send', [SendTaxController::class, 'send']);
Route::get('send-tax/download', [SendTaxController::class, 'download']);
