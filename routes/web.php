<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\HomeController;

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

Route::post('relogin', [AuthenticatedSessionController::class, 'relogin']);
Route::view('unauthenticated', 'errors.front-unauthenticated');

Route::group(['middleware' => ['auth','verified']], function () {
    Route::get('/', [HomeController::class, 'index']); // main route / base web
    Route::get('/home', [App\Http\Controllers\Auth\HomeController::class, 'index']); // main route / base web
    Route::view('/error', 'errors.front-error'); // route for error
});
