<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Logbook\LogbookConfigurationController;
use App\Http\Controllers\Logbook\MappingInventoryKitchenController;
use App\Http\Controllers\Logbook\MappingInventoryCashierController;
use App\Http\Controllers\Logbook\MappingInventoryWarehouseController;
use App\Http\Controllers\Logbook\MappingTaskCleaningController;
use App\Http\Controllers\Logbook\MappingDutiesCashierController;
use App\Http\Controllers\Logbook\MappingDutiesLobbyController;
use App\Http\Controllers\Logbook\MappingDutiesKitchenController;
use App\Http\Controllers\Logbook\MappingCleanCashierController;
use App\Http\Controllers\Logbook\MappingCleanLobbyController;
use App\Http\Controllers\Logbook\MappingCleanKitchenController;
use App\Http\Controllers\Logbook\MappingTaskToiletController;
use App\Http\Controllers\Logbook\MappingStorageTempController;
use App\Http\Controllers\Logbook\MappingPaymentListController;
use App\Http\Controllers\Logbook\MappingProdOrganoController;
use App\Http\Controllers\Logbook\MappingProdPlanController;

use App\Http\Controllers\Logbook\ApplicationReviewController;

use App\Http\Controllers\Logbook\DailyInventoryCashierController;
use App\Http\Controllers\Logbook\DailyInventoryKitchenController;
use App\Http\Controllers\Logbook\DailyInventoryWarehouseController;
use App\Http\Controllers\Logbook\DailyCleaningController;
use App\Http\Controllers\Logbook\DailyDutiesController;
use App\Http\Controllers\Logbook\DailyWastedController;
use App\Http\Controllers\Logbook\DutyRosterController;
use App\Http\Controllers\Logbook\CleaningDutiesController;
use App\Http\Controllers\Logbook\ElectricMeterController;
use App\Http\Controllers\Logbook\EnvPropumpController;
use App\Http\Controllers\Logbook\EnvSolidwasteController;
use App\Http\Controllers\Logbook\EnvWastewaterController;
use App\Http\Controllers\Logbook\ProductionPlanningController;
use App\Http\Controllers\Logbook\MoneySalesController;
use App\Http\Controllers\Logbook\TemperatureController;
use App\Http\Controllers\Logbook\ToiletController;
use App\Http\Controllers\Logbook\OrganoleptikController;
use App\Http\Controllers\Logbook\GasMeterController;
use App\Http\Controllers\Logbook\WaterMeterController;
use App\Http\Controllers\Logbook\StockCardController;
use App\Http\Controllers\Logbook\ReceptionMaterialController;

/*
|--------------------------------------------------------------------------
| Web Application Routes
|--------------------------------------------------------------------------
|
| \logbook\...
|
*/
// mapping logbook
Route::group(['prefix' => 'mapping'], function () {
    Route::resource('inventory-kitchen', MappingInventoryKitchenController::class)->except(['show']);
    Route::get('inventory-kitchen/dtble', [MappingInventoryKitchenController::class, 'dtble']);
    Route::get('inventory-kitchen/select', [MappingInventoryKitchenController::class, 'select']);

    Route::resource('inventory-cashier', MappingInventoryCashierController::class)->except(['show']);
    Route::get('inventory-cashier/dtble', [MappingInventoryCashierController::class, 'dtble']);
    Route::get('inventory-cashier/select', [MappingInventoryCashierController::class, 'select']);

    Route::resource('inventory-warehouse', MappingInventoryWarehouseController::class)->except(['show']);
    Route::get('inventory-warehouse/dtble', [MappingInventoryWarehouseController::class, 'dtble']);
    Route::get('inventory-warehouse/select', [MappingInventoryWarehouseController::class, 'select']);

    Route::resource('task-cleaning', MappingTaskCleaningController::class)->except(['show']);
    Route::get('task-cleaning/dtble', [MappingTaskCleaningController::class, 'dtble']);
    Route::get('task-cleaning/select', [MappingTaskCleaningController::class, 'select']);

    Route::resource('task-duties-cashier', MappingDutiesCashierController::class)->except(['show']);
    Route::get('task-duties-cashier/dtble', [MappingDutiesCashierController::class, 'dtble']);
    Route::get('task-duties-cashier/select', [MappingDutiesCashierController::class, 'select']);

    Route::resource('task-duties-lobby', MappingDutiesLobbyController::class)->except(['show']);
    Route::get('task-duties-lobby/dtble', [MappingDutiesLobbyController::class, 'dtble']);
    Route::get('task-duties-lobby/select', [MappingDutiesLobbyController::class, 'select']);

    Route::resource('task-duties-kitchen', MappingDutiesKitchenController::class)->except(['show']);
    Route::get('task-duties-kitchen/dtble', [MappingDutiesKitchenController::class, 'dtble']);
    Route::get('task-duties-kitchen/select', [MappingDutiesKitchenController::class, 'select']);

    Route::resource('task-clean-cashier', MappingCleanCashierController::class)->except(['show']);
    Route::get('task-clean-cashier/dtble', [MappingCleanCashierController::class, 'dtble']);
    Route::get('task-clean-cashier/select', [MappingCleanCashierController::class, 'select']);

    Route::resource('task-clean-lobby', MappingCleanLobbyController::class)->except(['show']);
    Route::get('task-clean-lobby/dtble', [MappingCleanLobbyController::class, 'dtble']);
    Route::get('task-clean-lobby/select', [MappingCleanLobbyController::class, 'select']);

    Route::resource('task-clean-kitchen', MappingCleanKitchenController::class)->except(['show']);
    Route::get('task-clean-kitchen/dtble', [MappingCleanKitchenController::class, 'dtble']);
    Route::get('task-clean-kitchen/select', [MappingCleanKitchenController::class, 'select']);

    Route::resource('task-toilet', MappingTaskToiletController::class)->except(['show']);
    Route::get('task-toilet/dtble', [MappingTaskToiletController::class, 'dtble']);
    Route::get('task-toilet/select', [MappingTaskToiletController::class, 'select']);

    Route::resource('storage-temp', MappingStorageTempController::class)->except(['show']);
    Route::get('storage-temp/dtble', [MappingStorageTempController::class, 'dtble']);
    Route::get('storage-temp/select', [MappingStorageTempController::class, 'select']);

    Route::resource('payment-list', MappingPaymentListController::class)->except(['show']);
    Route::get('payment-list/dtble', [MappingPaymentListController::class, 'dtble']);
    Route::get('payment-list/select', [MappingPaymentListController::class, 'select']);

    Route::resource('product-organoleptik', MappingProdOrganoController::class)->except(['show']);
    Route::get('product-organoleptik/dtble', [MappingProdOrganoController::class, 'dtble']);
    Route::get('product-organoleptik/select', [MappingProdOrganoController::class, 'select']);
    Route::post('product-organoleptik/detail', [MappingProdOrganoController::class, 'detail']);

    Route::resource('product-production-planning', MappingProdPlanController::class)->except(['show']);
    Route::get('product-production-planning/dtble', [MappingProdPlanController::class, 'dtble']);
    Route::get('product-production-planning/select', [MappingProdPlanController::class, 'select']);
    Route::post('product-production-planning/detail', [MappingProdPlanController::class, 'detail']);
});

// application review
Route::resource('application-review', ApplicationReviewController::class)->except(['show']);
Route::get('application-review/approve/{id}', [ApplicationReviewController::class, 'approve']);
Route::get('application-review/unapprove/{id}', [ApplicationReviewController::class, 'unapprove']);
Route::get('application-review/dtble', [ApplicationReviewController::class, 'dtble']);
Route::get('application-review/preview/dtble', [ApplicationReviewController::class, 'previewDtble']);
Route::get('application-review/create', [ApplicationReviewController::class, 'create']);

// daily inventory
Route::group(['prefix' => 'daily-inventory'], function () {
    Route::resource('kitchen', DailyInventoryKitchenController::class)->except(['show']);
    Route::get('kitchen/dtble', [DailyInventoryKitchenController::class, 'dtble']);
    Route::get('kitchen/select', [DailyInventoryKitchenController::class, 'select']);
    Route::get('kitchen/{lbAppReviewId}/preview', [DailyInventoryKitchenController::class, 'preview']);
    Route::post('kitchen/update', [DailyInventoryKitchenController::class, 'update']);

    Route::resource('cashier', DailyInventoryCashierController::class)->except(['show']);
    Route::get('cashier/dtble', [DailyInventoryCashierController::class, 'dtble']);
    Route::get('cashier/select', [DailyInventoryCashierController::class, 'select']);
    Route::get('cashier/{lbAppReviewId}/preview', [DailyInventoryCashierController::class, 'preview']);
    Route::post('cashier/update', [DailyInventoryCashierController::class, 'update']);

    Route::resource('warehouse', DailyInventoryWarehouseController::class)->except(['show']);
    Route::get('warehouse/dtble', [DailyInventoryWarehouseController::class, 'dtble']);
    Route::get('warehouse/select', [DailyInventoryWarehouseController::class, 'select']);
    Route::get('warehouse/{lbAppReviewId}/preview', [DailyInventoryWarehouseController::class, 'preview']);
    Route::post('warehouse/update', [DailyInventoryWarehouseController::class, 'update']);
});

Route::group(['prefix' => 'operational'], function () {
    Route::resource('duty-roster', DutyRosterController::class)->except(['show']);
    Route::get('duty-roster/dataview', [DutyRosterController::class, 'dataview']);
    Route::get('duty-roster/dtble', [DutyRosterController::class, 'dtble']);
    Route::get('duty-roster/select', [DutyRosterController::class, 'select']);
    Route::get('duty-roster/{lbAppReviewId}/preview', [DutyRosterController::class, 'preview']);
    Route::post('duty-roster/duty', [DutyRosterController::class, 'storeDuty']);
    Route::put('duty-roster/duty/{id}', [DutyRosterController::class, 'updateDuty']);

    // daily duties
    Route::resource('daily-duties', DailyDutiesController::class)->except(['show']);
    Route::get('daily-duties/dtble', [DailyDutiesController::class, 'dtble']);
    Route::post('daily-duties/update', [DailyDutiesController::class, 'update']);
    Route::put('daily-duties/note/{id}', [DailyDutiesController::class, 'updateNote']);
    Route::get('daily-duties/select', [DailyDutiesController::class, 'select']);
    Route::get('daily-duties/{lbAppReviewId}/preview', [DailyDutiesController::class, 'preview']);

    // cleaning duties
    Route::resource('cleaning-duties', CleaningDutiesController::class)->except(['show']);
    Route::get('cleaning-duties/dataview', [CleaningDutiesController::class, 'dataview']);
    Route::get('cleaning-duties/daily/dtble', [CleaningDutiesController::class, 'dailyDtble']);
    Route::post('cleaning-duties/daily/update', [CleaningDutiesController::class, 'dailyUpdate']);
    Route::get('cleaning-duties/weekly/dtble', [CleaningDutiesController::class, 'weeklyDtble']);
    Route::post('cleaning-duties/weekly/update', [CleaningDutiesController::class, 'weeklyUpdate']);
    Route::put('cleaning-duties/weekly/{id}', [CleaningDutiesController::class, 'weeklyUpdate']);
    Route::put('cleaning-duties/note/{id}', [CleaningDutiesController::class, 'updateNote']);
    Route::get('cleaning-duties/select', [CleaningDutiesController::class, 'select']);
    Route::get('cleaning-duties/{lbAppReviewId}/preview', [CleaningDutiesController::class, 'preview']);

    // water-meter
    Route::resource('water-meter', WaterMeterController::class)->except(['show']);
    Route::get('water-meter/dtble', [WaterMeterController::class, 'dtble']);
    Route::get('water-meter/select', [WaterMeterController::class, 'select']);
    Route::get('water-meter/{lbAppReviewId}/preview', [WaterMeterController::class, 'preview']);

    // electric-meter
    Route::resource('electric-meter', ElectricMeterController::class)->except(['show']);
    Route::get('electric-meter/dtble', [ElectricMeterController::class, 'dtble']);
    Route::get('electric-meter/select', [ElectricMeterController::class, 'select']);
    Route::get('electric-meter/{lbAppReviewId}/preview', [ElectricMeterController::class, 'preview']);

    // gas-meter
    Route::resource('gas-meter', GasMeterController::class)->except(['show']);
    Route::get('gas-meter/dtble', [GasMeterController::class, 'dtble']);
    Route::get('gas-meter/select', [GasMeterController::class, 'select']);
    Route::get('gas-meter/{lbAppReviewId}/preview', [GasMeterController::class, 'preview']);

    // env-propump
    Route::resource('env-propump', EnvPropumpController::class)->except(['show']);
    Route::get('env-propump/dtble', [EnvPropumpController::class, 'dtble']);
    Route::get('env-propump/select', [EnvPropumpController::class, 'select']);
    Route::get('env-propump/{lbAppReviewId}/preview', [EnvPropumpController::class, 'preview']);

    // env-wastewater
    Route::resource('env-wastewater', EnvWastewaterController::class)->except(['show']);
    Route::get('env-wastewater/dtble', [EnvWastewaterController::class, 'dtble']);
    Route::get('env-wastewater/select', [EnvWastewaterController::class, 'select']);
    Route::get('env-wastewater/{lbAppReviewId}/preview', [EnvWastewaterController::class, 'preview']);

    // env-solidwaste
    Route::resource('env-solidwaste', EnvSolidwasteController::class)->except(['show']);
    Route::get('env-solidwaste/dtble', [EnvSolidwasteController::class, 'dtble']);
    Route::get('env-solidwaste/select', [EnvSolidwasteController::class, 'select']);
    Route::get('env-solidwaste/{lbAppReviewId}/preview', [EnvSolidwasteController::class, 'preview']);

    // toilet checklist
    Route::resource('toilet', ToiletController::class)->except(['show']);
    Route::get('toilet/dtble', [ToiletController::class, 'dtble']);
    Route::post('toilet/update', [ToiletController::class, 'update']);
    Route::get('toilet/select', [ToiletController::class, 'select']);
    Route::get('toilet/{lbAppReviewId}/preview', [ToiletController::class, 'preview']);

    // organoleptik
    Route::resource('organoleptik', OrganoleptikController::class)->except(['show']);
    Route::get('organoleptik/dtble', [OrganoleptikController::class, 'dtble']);
    Route::get('organoleptik/select', [OrganoleptikController::class, 'select']);
    Route::get('organoleptik/{lbAppReviewId}/preview', [OrganoleptikController::class, 'preview']);

    // temperature
    Route::resource('temperature', TemperatureController::class)->except(['show']);
    Route::get('temperature/dtble', [TemperatureController::class, 'dtble']);
    Route::get('temperature/select', [TemperatureController::class, 'select']);
    Route::get('temperature/{lbAppReviewId}/preview', [TemperatureController::class, 'preview']);
    Route::post('temperature/update', [TemperatureController::class, 'update']);
});

// stock card
Route::resource('stock-card', StockCardController::class)->except(['show']);
Route::get('stock-card/dtble', [StockCardController::class, 'dtble']);
Route::get('stock-card/select', [StockCardController::class, 'select']);
Route::get('stock-card/{lbAppReviewId}/preview', [StockCardController::class, 'preview']);

// daily wasted
Route::resource('daily-wasted', DailyWastedController::class)->except(['show']);
Route::get('daily-wasted/dtble', [DailyWastedController::class, 'dtble']);
Route::get('daily-wasted/select', [DailyWastedController::class, 'select']);
Route::get('daily-wasted/{lbAppReviewId}/preview', [DailyWastedController::class, 'preview']);

// reception of material / product
Route::resource('reception-material', ReceptionMaterialController::class)->except(['show']);
Route::get('reception-material/dtble', [ReceptionMaterialController::class, 'dtble']);
Route::get('reception-material/select', [ReceptionMaterialController::class, 'select']);
Route::get('reception-material/{lbAppReviewId}/preview', [ReceptionMaterialController::class, 'preview']);

// daily-cleaning
Route::resource('daily-cleaning', DailyCleaningController::class)->except(['show']);
Route::get('daily-cleaning/dtble', [DailyCleaningController::class, 'dtble']);
Route::post('daily-cleaning/update', [DailyCleaningController::class, 'update']);
Route::get('daily-cleaning/select', [DailyCleaningController::class, 'select']);
Route::get('daily-cleaning/{lbAppReviewId}/preview', [DailyCleaningController::class, 'preview']);

// money-sales
Route::resource('money-sales', MoneySalesController::class)->except(['show']);
Route::get('money-sales/dataview', [MoneySalesController::class, 'dataview']);
Route::get('money-sales/cashier/dtble', [MoneySalesController::class, 'dtbleCashier']);
Route::get('money-sales/detail/dtble', [MoneySalesController::class, 'dtbleDetail']);
Route::put('money-sales/cashier/det/{id}', [MoneySalesController::class, 'cashierDetUpdate']);
Route::put('money-sales/cashier/{id}', [MoneySalesController::class, 'cashierUpdate']);
Route::post('money-sales/detail', [MoneySalesController::class, 'storeDetail']);
Route::put('money-sales/detail/{id}', [MoneySalesController::class, 'updateDetail']);
Route::delete('money-sales/detail/{id}', [MoneySalesController::class, 'destroyDetail']);
Route::get('money-sales/{lbAppReviewId}/preview', [MoneySalesController::class, 'preview']);

// production-planning
Route::resource('production-planning', ProductionPlanningController::class)->except(['show']);
Route::get('production-planning/dataview', [ProductionPlanningController::class, 'dataview']);
Route::get('production-planning/dtble', [ProductionPlanningController::class, 'dtble']);
Route::post('production-planning/prodplan/update', [ProductionPlanningController::class, 'prodPlanUpdate']);
Route::get('production-planning/prodtime/dtble', [ProductionPlanningController::class, 'prodTimedtble']);
Route::post('production-planning/prodtime/update', [ProductionPlanningController::class, 'prodTimeUpdate']);
Route::post('production-planning/prodtime/detail/update', [ProductionPlanningController::class, 'prodTimeDetailUpdate']);
Route::get('production-planning/prodtime/detail', [ProductionPlanningController::class, 'prodTimedetail']);
Route::post('production-planning/update', [ProductionPlanningController::class, 'update']);

Route::get('production-planning/prodtemp/dtble', [ProductionPlanningController::class, 'prodTempdtble']);
Route::post('production-planning/prodtemp', [ProductionPlanningController::class, 'prodTempStore']);
Route::put('production-planning/prodtemp/{id}', [ProductionPlanningController::class, 'prodTempUpdate']);
Route::delete('production-planning/prodtemp/{id}', [ProductionPlanningController::class, 'prodTempDestroy']);

Route::get('production-planning/prodtempverify/dtble', [ProductionPlanningController::class, 'prodTempVerifydtble']);
Route::post('production-planning/prodtempverify/update', [ProductionPlanningController::class, 'prodTempVerifyUpdate']);

Route::get('production-planning/prodquality/dtble', [ProductionPlanningController::class, 'prodQualitydtble']);
Route::post('production-planning/prodquality/update', [ProductionPlanningController::class, 'prodQualityUpdate']);

Route::get('production-planning/produsedoil/dtble', [ProductionPlanningController::class, 'prodUsedoildtble']);
Route::post('production-planning/produsedoil/update', [ProductionPlanningController::class, 'prodUsedoilUpdate']);

Route::get('production-planning/select', [ProductionPlanningController::class, 'select']);
Route::get('production-planning/{lbAppReviewId}/preview', [ProductionPlanningController::class, 'preview']);

Route::resource('configuration-logbook', LogbookConfigurationController::class)->except(['show']);
