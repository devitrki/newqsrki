<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Master\CompanyController;
use App\Http\Controllers\Master\CountryController;
use App\Http\Controllers\Master\DepartmentController;
use App\Http\Controllers\Master\PositionController;
use App\Http\Controllers\Master\ConfigurationController;
use App\Http\Controllers\Master\AreaController;
use App\Http\Controllers\Master\PlantController;
use App\Http\Controllers\Master\MaterialController;
use App\Http\Controllers\Master\MaterialOutletController;
use App\Http\Controllers\Master\AreaPlantController;
use App\Http\Controllers\Master\RegionalPlantController;
use App\Http\Controllers\Master\GlController;
use App\Http\Controllers\Master\SpecialGlController;
use App\Http\Controllers\Master\BankGLController;
use App\Http\Controllers\Master\BankChargeGLController;
use App\Http\Controllers\Master\PosController;
use App\Http\Controllers\Master\PettycashGlCcController;
use App\Http\Controllers\Master\OpnameMaterialFormulaController;
use App\Http\Controllers\Master\PaymentPosController;
use App\Http\Controllers\Auth\LanguageController;

/*
|--------------------------------------------------------------------------
| Web Application Routes
|--------------------------------------------------------------------------
|
| \master\...
|
*/

Route::resource('company', CompanyController::class)->except(['show']);
Route::get('company/dtble', [CompanyController::class, 'dtble']);
Route::get('company/select', [CompanyController::class, 'select']);
Route::get('company/{id}/configuration/dtble', [CompanyController::class, 'dtbleConf']);
Route::post('company/{id}/configuration', [CompanyController::class, 'storeConf']);
Route::post('company/{id}/configuration/delete', [CompanyController::class, 'destroyConf']);

Route::resource('country', CountryController::class)->except(['show']);
Route::get('country/dtble', [CountryController::class, 'dtble']);
Route::get('country/select', [CountryController::class, 'select']);

Route::resource('department', DepartmentController::class)->except(['show']);
Route::get('department/dtble', [DepartmentController::class, 'dtble']);
Route::get('department/select', [DepartmentController::class, 'select']);

Route::resource('position', PositionController::class)->except(['show']);
Route::get('position/dtble', [PositionController::class, 'dtble']);
Route::get('position/select', [PositionController::class, 'select']);

Route::resource('language', LanguageController::class)->except(['show']);
Route::get('language/dtble', [LanguageController::class, 'dtble']);
Route::get('language/select', [LanguageController::class, 'select']);

Route::resource('area', AreaController::class)->except(['show']);
Route::get('area/dtble', [AreaController::class, 'dtble']);
Route::get('area/select', [AreaController::class, 'select']);

Route::resource('plant', PlantController::class)->except(['show']);
Route::get('plant/dtble', [PlantController::class, 'dtble']);
Route::get('plant/select', [PlantController::class, 'select']);
Route::post('plant/sync', [PlantController::class, 'sync']);

Route::resource('material', MaterialController::class)->except(['show']);
Route::get('material/dtble', [MaterialController::class, 'dtble']);
Route::get('material/select', [MaterialController::class, 'select']);
Route::get('material/autocomplete', [MaterialController::class, 'autocomplete']);
Route::get('material/sync', [MaterialController::class, 'sync']);
Route::get('material/uom/{id}', [MaterialController::class, 'selectAlternativeUom']);
Route::get('material/data/{id}', [MaterialController::class, 'getDataMaterial']);

// Route::resource('material-logbook', 'Master\MaterialLogbookController')->except(['show']);
// Route::get('material-logbook/dtble', 'Master\MaterialLogbookController@dtble');
// Route::get('material-logbook/select', 'Master\MaterialLogbookController@select');
// Route::get('material-logbook/sync', 'Master\MaterialLogbookController@sync');

Route::resource('material-outlet', MaterialOutletController::class)->except(['show']);
Route::get('material-outlet/dtble', [MaterialOutletController::class, 'dtble']);
Route::get('material-outlet/select', [MaterialOutletController::class, 'select']);
Route::get('material-outlet/data/{code}', [MaterialOutletController::class, 'getDataMaterial']);
Route::get('material-outlet/waste/{plant}', [MaterialOutletController::class, 'getWasteMaterial']);
Route::post('material-outlet/import', [MaterialOutletController::class, 'import']);

Route::resource('area-plant', AreaPlantController::class)->except(['show']);
Route::get('area-plant/dtble', [AreaPlantController::class, 'dtble']);
Route::get('area-plant/select', [AreaPlantController::class, 'select']);
Route::get('area-plant/list/{id}/dtble', [AreaPlantController::class, 'dtbleList']);
Route::post('area-plant/list/{id}', [AreaPlantController::class, 'storeList']);
Route::delete('area-plant/list/{id}', [AreaPlantController::class, 'destroyList']);

Route::resource('regional-plant', RegionalPlantController::class)->except(['show']);
Route::get('regional-plant/dtble', [RegionalPlantController::class, 'dtble']);
Route::get('regional-plant/select', [RegionalPlantController::class, 'select']);
Route::get('regional-plant/list/{id}/dtble', [RegionalPlantController::class, 'dtbleList']);
Route::post('regional-plant/list/{id}', [RegionalPlantController::class, 'storeList']);
Route::delete('regional-plant/list/{id}', [RegionalPlantController::class, 'destroyList']);

Route::resource('gl', GlController::class)->except(['show']);
Route::get('gl/dtble', [GlController::class, 'dtble']);
Route::get('gl/select', [GlController::class, 'select']);

Route::resource('special-gl', SpecialGlController::class)->except(['show']);
Route::get('special-gl/dtble', [SpecialGlController::class, 'dtble']);
Route::get('special-gl/select', [SpecialGlController::class, 'select']);

Route::resource('bank-gl', BankGLController::class)->except(['show']);
Route::get('bank-gl/dtble', [BankGLController::class, 'dtble']);
Route::get('bank-gl/select', [BankGLController::class, 'select']);

Route::resource('bank-charge-gl', BankChargeGLController::class)->except(['show']);
Route::get('bank-charge-gl/dtble', [BankChargeGLController::class, 'dtble']);
Route::get('bank-charge-gl/select', [BankChargeGLController::class, 'select']);

Route::resource('pos', PosController::class)->except(['show']);
Route::get('pos/dtble', [PosController::class, 'dtble']);
Route::get('pos/select', [PosController::class, 'select']);
Route::get('pos/{id}/configuration/dtble', [PosController::class, 'dtbleConf']);
Route::post('pos/{id}/configuration', [PosController::class, 'storeConf']);
Route::post('pos/{id}/configuration/delete', [PosController::class, 'destroyConf']);

Route::resource('pettycash-glcc', PettycashGlCcController::class)->except(['show']);
Route::get('pettycash-glcc/dtble', [PettycashGlCcController::class, 'dtble']);

Route::resource('opname-material-formula', OpnameMaterialFormulaController::class)->except(['show']);
Route::get('opname-material-formula/dtble', [OpnameMaterialFormulaController::class, 'dtble']);
Route::get('opname-material-formula/{opname_material_formula_id}/item/dtble', [OpnameMaterialFormulaController::class, 'dtbleItem']);
Route::post('opname-material-formula/{opname_material_formula_id}/item', [OpnameMaterialFormulaController::class, 'storeItem']);
Route::put('opname-material-formula/{opname_material_formula_id}/item', [OpnameMaterialFormulaController::class, 'updateItem']);
Route::delete('opname-material-formula/{opname_material_formula_id}/item/delete/{id}', [OpnameMaterialFormulaController::class, 'destroyItem']);

Route::resource('payment-pos', PaymentPosController::class)->except(['show']);
Route::get('payment-pos/dtble', [PaymentPosController::class, 'dtble']);

Route::resource('master-configuration', ConfigurationController::class)->except(['show']);
