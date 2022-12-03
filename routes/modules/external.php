<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Application Routes
|--------------------------------------------------------------------------
|
| \external\...
|
*/

// interface api accurate gion
Route::group(['prefix' => 'gion'], function () {
  Route::group(['prefix' => 'accurate'], function () {
    Route::get('callback', 'Interfaces\Gion\AccurateController@callback');
  });
});
