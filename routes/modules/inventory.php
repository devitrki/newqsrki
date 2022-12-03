<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventory\WasteController;
use App\Http\Controllers\Inventory\OpnameController;
use App\Http\Controllers\Inventory\GrVendorController;
use App\Http\Controllers\Inventory\GrPlantController;
use App\Http\Controllers\Inventory\GiPlantController;
use App\Http\Controllers\Inventory\ConfigurationController;

use App\Http\Controllers\Inventory\Usedoil\UoMaterialController;
use App\Http\Controllers\Inventory\Usedoil\UoPriceCategoryController;
use App\Http\Controllers\Inventory\Usedoil\UoVendorController;
use App\Http\Controllers\Inventory\Usedoil\UoDepositController;
use App\Http\Controllers\Inventory\Usedoil\UoMutationSaldoController;
use App\Http\Controllers\Inventory\Usedoil\UoGoodReceiptController;
use App\Http\Controllers\Inventory\Usedoil\UoStockController;
use App\Http\Controllers\Inventory\Usedoil\UoGiTransferController;
use App\Http\Controllers\Inventory\Usedoil\UoGrTransferController;
use App\Http\Controllers\Inventory\Usedoil\UoSalesController;
use App\Http\Controllers\Inventory\Usedoil\UoAdjustmentController;
use App\Http\Controllers\Inventory\Usedoil\UoImportController;

/*
|--------------------------------------------------------------------------
| Web Inventory Routes
|--------------------------------------------------------------------------
|
| \inventory\...
|
*/

Route::group(['prefix' => 'usedoil'], function () {
    Route::resource('uo-material', UoMaterialController::class)->except(['show']);
    Route::get('uo-material/dtble', [UoMaterialController::class, 'dtble']);
    Route::get('uo-material/select', [UoMaterialController::class, 'select']);
    Route::get('uo-material/dtble/qty/{input_name}/{plant_id}', [UoMaterialController::class, 'dtbleQty']);

    Route::resource('uo-price-category', UoPriceCategoryController::class)->except(['show']);
    Route::get('uo-price-category/dtble', [UoPriceCategoryController::class, 'dtble']);
    Route::get('uo-price-category/select', [UoPriceCategoryController::class, 'select']);

    Route::get('uo-price-category/dtble/detail/{id}', [UoPriceCategoryController::class, 'dtbleDetail']);
    Route::post('uo-price-category/detail', [UoPriceCategoryController::class, 'storeDetail']);
    Route::put('uo-price-category/detail/{id}', [UoPriceCategoryController::class, 'updateDetail']);
    Route::delete('uo-price-category/detail/{id}', [UoPriceCategoryController::class, 'destroyDetail']);

    Route::resource('uo-vendor', UoVendorController::class)->except(['show']);
    Route::get('uo-vendor/dtble', [UoVendorController::class, 'dtble']);
    Route::get('uo-vendor/select', [UoVendorController::class, 'select']);
    Route::get('uo-vendor/plant/{id}', [UoVendorController::class, 'getPlantVendor']);
    Route::get('uo-vendor/vendor/{id}', [UoVendorController::class, 'getVendorPlant']);
    Route::get('uo-vendor/dtble/price/{input_name}/{vendor_id}/{plant_id}', [UoVendorController::class, 'dtblePrice']);

    Route::resource('uo-deposit', UoDepositController::class)->except(['show']);
    Route::get('uo-deposit/dtble', [UoDepositController::class, 'dtble']);
    Route::get('uo-deposit/select', [UoDepositController::class, 'select']);
    Route::get('uo-deposit/submit/{id}', [UoDepositController::class, 'submit']);
    Route::get('uo-deposit/approve/{id}', [UoDepositController::class, 'approve']);
    Route::post('uo-deposit/reject/{id}', [UoDepositController::class, 'reject']);
    Route::post('uo-deposit/{id}', [UoDepositController::class, 'update']);

    Route::resource('uo-mutation-saldo', UoMutationSaldoController::class)->except(['show']);
    Route::get('uo-mutation-saldo/dtble', [UoMutationSaldoController::class, 'dtble']);

    Route::resource('uo-good-receipt', UoGoodReceiptController::class)->except(['show']);
    Route::get('uo-good-receipt/dtble', [UoGoodReceiptController::class, 'dtble']);
    Route::get('uo-good-receipt/dtble/view/{id}', [UoGoodReceiptController::class, 'dtbleView']);
    Route::post('uo-good-receipt/cancel/{id}', [UoGoodReceiptController::class, 'cancel']);

    Route::get('uo-stock/dtble/current/{plant_id}', [UoStockController::class, 'dtbleCurrent']);

    Route::resource('uo-gitransfer', UoGiTransferController::class)->except(['show']);
    Route::get('uo-gitransfer/dtble', [UoGiTransferController::class, 'dtble']);
    Route::get('uo-gitransfer/dtble/view/{id}', [UoGiTransferController::class, 'dtbleView']);
    Route::post('uo-gitransfer/cancel/{id}', [UoGiTransferController::class, 'cancel']);
    Route::get('uo-gitransfer/print/{id}', [UoGiTransferController::class, 'print']);

    Route::resource('uo-grtransfer',  UoGrTransferController::class)->except(['show']);
    Route::get('uo-grtransfer/dtble', [UoGrTransferController::class, 'dtble']);
    Route::get('uo-grtransfer/dtble/view/{id}', [UoGrTransferController::class, 'dtbleView']);
    Route::post('uo-grtransfer/cancel/{id}', [UoGrTransferController::class, 'cancel']);
    Route::get('uo-grtransfer/outstanding/{plant_id}', [UoGrTransferController::class, 'getOutstandingByPlantId']);
    Route::get('uo-grtransfer/outstanding/item/{id}', [UoGrTransferController::class, 'dtbleOutstandingItem']);
    Route::get('uo-grtransfer/print/{id}', [UoGrTransferController::class, 'print']);

    Route::resource('uo-sales', UoSalesController::class)->except(['show']);
    Route::get('uo-sales/dtble', [UoSalesController::class, 'dtble']);
    Route::get('uo-sales/delivery-order/{id}', [UoSalesController::class, 'printDeliveryOrder']);
    Route::get('uo-sales/invoice/{id}', [UoSalesController::class, 'printInvoice']);
    Route::get('uo-sales/invoice-copy/{id}', [UoSalesController::class, 'printInvoiceCopy']);

    Route::resource('uo-stock-adjustment', UoAdjustmentController::class)->except(['show']);
    Route::get('uo-stock-adjustment/dtble', [UoAdjustmentController::class, 'dtble']);
    Route::get('uo-stock-adjustment/dtble/view/{id}', [UoAdjustmentController::class, 'dtbleView']);
    Route::post('uo-stock-adjustment/cancel/{id}', [UoAdjustmentController::class, 'cancel']);

    Route::get('import', [UoImportController::class, 'import']);

});

Route::resource('giplant', GiPlantController::class)->except(['show']);
Route::get('giplant/dtble', [GiPlantController::class, 'dtble']);
Route::get('giplant/item/{id}', [GiPlantController::class, 'getItemsById']);
Route::get('giplant/upload/sap/{id}', [GiPlantController::class, 'uploadSap']);
Route::get('giplant/preview/{id}', [GiPlantController::class, 'preview']);

Route::resource('grplant', GrPlantController::class)->except(['show']);
Route::get('grplant/dtble', [GrPlantController::class, 'dtble']);
Route::get('grplant/dtble/outstanding/{plant_id}', [GrPlantController::class, 'dtbleOutstandingByPlantId']);
Route::get('grplant/outstanding/{plant_id}', [GrPlantController::class, 'getOutstandingByPlantId']);
Route::get('grplant/outstanding/detail/{plant_code}/{doc_number}', [GrPlantController::class, 'getOutstandingDetailByDocNumber']);
Route::get('grplant/preview/{id}', [GrPlantController::class, 'preview']);

Route::resource('grvendor', GrVendorController::class)->except(['show']);
Route::get('grvendor/dtble', [GrVendorController::class, 'dtble']);
Route::get('grvendor/preview/{id}', [GrVendorController::class, 'preview']);
Route::get('grvendor/outstanding/{plant_id}', [GrVendorController::class, 'getOutstandingByPlantId']);
Route::get('grvendor/fix-material-code', [GrVendorController::class, 'fixMaterialCode']);

Route::resource('opname', OpnameController::class)->except(['show']);
Route::get('opname/dtble', [OpnameController::class, 'dtble']);
Route::get('opname/dtble/qty/{input_name}/{id}/{plant}', [OpnameController::class, 'dtbleQty']);
Route::get('opname/preview/{id}', [OpnameController::class, 'preview']);
Route::get('opname/download/{id}', [OpnameController::class, 'download']);
Route::get('opname/submit/{id}', [OpnameController::class, 'submit']);
Route::get('opname/open-lock/{id}', [OpnameController::class, 'openLock']);
Route::post('opname/update', [OpnameController::class, 'updateDocNumber']);

Route::resource('waste', WasteController::class)->except(['show']);
Route::get('waste/dtble', [WasteController::class, 'dtble']);
Route::get('waste/dtble/qty/{input_name}/{id}/{plant}', [WasteController::class, 'dtbleQty']);
Route::get('waste/preview/{id}', [WasteController::class, 'preview']);
Route::get('waste/download/{id}', [WasteController::class, 'download']);
Route::get('waste/submit/{id}', [WasteController::class, 'submit']);
Route::get('waste/items/{id}', [WasteController::class, 'getItemsWaste']);
Route::post('waste/update', [WasteController::class, 'updateDocNumber']);

Route::resource('configuration', ConfigurationController::class)->except(['show']);
