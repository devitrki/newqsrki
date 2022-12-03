<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\MenuController;
use App\Http\Controllers\Auth\LanguageController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Auth\CompanyController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');
});

Route::group(['prefix' => 'auth'], function () {
    Route::get('/check', [MenuController::class, 'check']);
    Route::get('/menu/search/json', [MenuController::class, 'getJSONSearchMenu']);
    Route::post('/languange/change', [LanguageController::class, 'changeLanguageUser']);
    Route::post('/password/change', [UserController::class, 'changePassword']);
    Route::post('/password/monthly/change', [UserController::class, 'changePasswordMonthly']);
    Route::post('/profile/change', [UserController::class, 'changeProfile']);
    Route::post('/company/change', [CompanyController::class, 'changeCompanySelectedUser']);

    Route::group(['prefix' => '/menu'], function () {
        Route::get('/treeview/json', [MenuController::class, 'getMenuTreeviewJson']);
        Route::get('/dtble', [MenuController::class, 'getMenuDtble']);
        Route::post('/sort/change', [MenuController::class, 'changeSort']);
        Route::post('/create', [MenuController::class, 'createMenu']);
        Route::post('/folder/create', [MenuController::class, 'createFolder']);
        Route::delete('/delete/{id}', [MenuController::class, 'destroy']);
        Route::post('/edit', [MenuController::class, 'update']);
    });
});
