<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Application Routes
|--------------------------------------------------------------------------
|
| \report\...
|
*/

// module logbook application
Route::get('logbook/{menu}', 'Logbook\ReportLogbookController@index');
Route::get('logbook/{menu}/report', 'Logbook\ReportLogbookController@report');
Route::post('logbook/{menu}/export', 'Logbook\ReportLogbookController@export');

// module inventory
Route::get('inventory/{menu}', 'Inventory\ReportInventoryController@index');
Route::get('inventory/{menu}/report', 'Inventory\ReportInventoryController@report');
Route::post('inventory/{menu}/export', 'Inventory\ReportInventoryController@export');

// module interface
Route::get('interfaces/{menu}', 'Interfaces\ReportInterfacesController@index');
Route::get('interfaces/{menu}/report', 'Interfaces\ReportInterfacesController@report');
Route::post('interfaces/{menu}/export', 'Interfaces\ReportInterfacesController@export');

// module tax
Route::get('tax/{menu}', 'Tax\ReportTaxController@index');
Route::get('tax/{menu}/report', 'Tax\ReportTaxController@report');
Route::post('tax/{menu}/export', 'Tax\ReportTaxController@export');

// module pos
Route::get('pos/{menu}', 'Pos\ReportPosController@index');
Route::get('pos/{menu}/report', 'Pos\ReportPosController@report');
Route::post('pos/{menu}/export', 'Pos\ReportPosController@export');

// module financeacc
Route::get('financeacc/{menu}', 'Financeacc\ReportFinanceaccController@index');
Route::get('financeacc/{menu}/report', 'Financeacc\ReportFinanceaccController@report');
Route::post('financeacc/{menu}/export', 'Financeacc\ReportFinanceaccController@export');
