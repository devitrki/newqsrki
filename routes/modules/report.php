<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Financeacc\ReportFinanceaccController;
use App\Http\Controllers\Inventory\ReportInventoryController;
use App\Http\Controllers\Tax\ReportTaxController;
use App\Http\Controllers\Pos\ReportPosController;

/*
|--------------------------------------------------------------------------
| Web Application Routes
|--------------------------------------------------------------------------
|
| \report\...
|
*/

// // module logbook application
// Route::get('logbook/{menu}', 'Logbook\ReportLogbookController@index');
// Route::get('logbook/{menu}/report', 'Logbook\ReportLogbookController@report');
// Route::post('logbook/{menu}/export', 'Logbook\ReportLogbookController@export');

// // module inventory
Route::get('inventory/{menu}', [ReportInventoryController::class, 'index']);
Route::get('inventory/{menu}/report', [ReportInventoryController::class, 'report']);
Route::post('inventory/{menu}/export', [ReportInventoryController::class, 'export']);

// // module interface
// Route::get('interfaces/{menu}', 'Interfaces\ReportInterfacesController@index');
// Route::get('interfaces/{menu}/report', 'Interfaces\ReportInterfacesController@report');
// Route::post('interfaces/{menu}/export', 'Interfaces\ReportInterfacesController@export');

// // module tax
Route::get('tax/{menu}', [ReportTaxController::class, 'index']);
Route::get('tax/{menu}/report', [ReportTaxController::class, 'report']);
Route::post('tax/{menu}/export', [ReportTaxController::class, 'export']);

// // module pos
Route::get('pos/{menu}', [ReportPosController::class, 'index']);
Route::get('pos/{menu}/report', [ReportPosController::class, 'report']);
Route::post('pos/{menu}/export', [ReportPosController::class, 'export']);

// module financeacc
Route::get('financeacc/{menu}', [ReportFinanceaccController::class, 'index']);
Route::get('financeacc/{menu}/report', [ReportFinanceaccController::class, 'report']);
Route::post('financeacc/{menu}/export', [ReportFinanceaccController::class, 'export']);
