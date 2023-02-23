<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Interfaces\Aloha\SendManualAlohaController;
use App\Http\Controllers\Interfaces\Aloha\OrderModeAlohaController;

/*
|--------------------------------------------------------------------------
| Web Application Routes
|--------------------------------------------------------------------------
|
| \interfaces\...
|
*/

// interface aloha
Route::group(['prefix' => 'aloha'], function () {
    Route::resource('send-manual-aloha', SendManualAlohaController::class)->except(['show']);
    Route::get('send-manual-aloha/dtble', [SendManualAlohaController::class, 'dtble']);
    Route::get('send-manual-aloha/view', [SendManualAlohaController::class, 'view']);
});
