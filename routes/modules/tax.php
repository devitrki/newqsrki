<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Application Routes
|--------------------------------------------------------------------------
|
| \tax\...
|
*/

Route::resource('ftp-government', 'Tax\FtpGovernmentController')->except(['show']);
Route::get('ftp-government/dtble', 'Tax\FtpGovernmentController@dtble');
Route::get('ftp-government/select', 'Tax\FtpGovernmentController@select');

Route::resource('send-tax', 'Tax\SendTaxController')->except(['show']);
Route::get('send-tax/dtble', 'Tax\SendTaxController@dtble');
Route::get('send-tax/select', 'Tax\SendTaxController@select');
Route::post('send-tax/send', 'Tax\SendTaxController@send');
Route::get('send-tax/download', 'Tax\SendTaxController@download');
