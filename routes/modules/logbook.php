<?php

use Illuminate\Support\Facades\Route;

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
    Route::resource('inventory-kitchen', 'Logbook\MappingInventoryKitchenController')->except(['show']);
    Route::get('inventory-kitchen/dtble', 'Logbook\MappingInventoryKitchenController@dtble');
    Route::get('inventory-kitchen/select', 'Logbook\MappingInventoryKitchenController@select');

    Route::resource('inventory-cashier', 'Logbook\MappingInventoryCashierController')->except(['show']);
    Route::get('inventory-cashier/dtble', 'Logbook\MappingInventoryCashierController@dtble');
    Route::get('inventory-cashier/select', 'Logbook\MappingInventoryCashierController@select');

    Route::resource('inventory-warehouse', 'Logbook\MappingInventoryWarehouseController')->except(['show']);
    Route::get('inventory-warehouse/dtble', 'Logbook\MappingInventoryWarehouseController@dtble');
    Route::get('inventory-warehouse/select', 'Logbook\MappingInventoryWarehouseController@select');

    Route::resource('task-cleaning', 'Logbook\MappingTaskCleaningController')->except(['show']);
    Route::get('task-cleaning/dtble', 'Logbook\MappingTaskCleaningController@dtble');
    Route::get('task-cleaning/select', 'Logbook\MappingTaskCleaningController@select');

    Route::resource('task-duties-cashier', 'Logbook\MappingDutiesCashierController')->except(['show']);
    Route::get('task-duties-cashier/dtble', 'Logbook\MappingDutiesCashierController@dtble');
    Route::get('task-duties-cashier/select', 'Logbook\MappingDutiesCashierController@select');

    Route::resource('task-duties-lobby', 'Logbook\MappingDutiesLobbyController')->except(['show']);
    Route::get('task-duties-lobby/dtble', 'Logbook\MappingDutiesLobbyController@dtble');
    Route::get('task-duties-lobby/select', 'Logbook\MappingDutiesLobbyController@select');

    Route::resource('task-duties-kitchen', 'Logbook\MappingDutiesKitchenController')->except(['show']);
    Route::get('task-duties-kitchen/dtble', 'Logbook\MappingDutiesKitchenController@dtble');
    Route::get('task-duties-kitchen/select', 'Logbook\MappingDutiesKitchenController@select');

    Route::resource('task-clean-cashier', 'Logbook\MappingCleanCashierController')->except(['show']);
    Route::get('task-clean-cashier/dtble', 'Logbook\MappingCleanCashierController@dtble');
    Route::get('task-clean-cashier/select', 'Logbook\MappingCleanCashierController@select');

    Route::resource('task-clean-lobby', 'Logbook\MappingCleanLobbyController')->except(['show']);
    Route::get('task-clean-lobby/dtble', 'Logbook\MappingCleanLobbyController@dtble');
    Route::get('task-clean-lobby/select', 'Logbook\MappingCleanLobbyController@select');

    Route::resource('task-clean-kitchen', 'Logbook\MappingCleanKitchenController')->except(['show']);
    Route::get('task-clean-kitchen/dtble', 'Logbook\MappingCleanKitchenController@dtble');
    Route::get('task-clean-kitchen/select', 'Logbook\MappingCleanKitchenController@select');

    Route::resource('task-toilet', 'Logbook\MappingTaskToiletController')->except(['show']);
    Route::get('task-toilet/dtble', 'Logbook\MappingTaskToiletController@dtble');
    Route::get('task-toilet/select', 'Logbook\MappingTaskToiletController@select');

    Route::resource('storage-temp', 'Logbook\MappingStorageTempController')->except(['show']);
    Route::get('storage-temp/dtble', 'Logbook\MappingStorageTempController@dtble');
    Route::get('storage-temp/select', 'Logbook\MappingStorageTempController@select');

    Route::resource('payment-list', 'Logbook\MappingPaymentListController')->except(['show']);
    Route::get('payment-list/dtble', 'Logbook\MappingPaymentListController@dtble');
    Route::get('payment-list/select', 'Logbook\MappingPaymentListController@select');

    Route::resource('product-organoleptik', 'Logbook\MappingProdOrganoController')->except(['show']);
    Route::get('product-organoleptik/dtble', 'Logbook\MappingProdOrganoController@dtble');
    Route::get('product-organoleptik/select', 'Logbook\MappingProdOrganoController@select');
    Route::post('product-organoleptik/detail', 'Logbook\MappingProdOrganoController@detail');

    Route::resource('product-production-planning', 'Logbook\MappingProdPlanController')->except(['show']);
    Route::get('product-production-planning/dtble', 'Logbook\MappingProdPlanController@dtble');
    Route::get('product-production-planning/select', 'Logbook\MappingProdPlanController@select');
    Route::post('product-production-planning/detail', 'Logbook\MappingProdPlanController@detail');
});

// daily inventory
Route::group(['prefix' => 'daily-inventory'], function () {
    Route::resource('kitchen', 'Logbook\DailyInventoryKitchenController')->except(['show']);
    Route::get('kitchen/dtble', 'Logbook\DailyInventoryKitchenController@dtble');
    Route::get('kitchen/select', 'Logbook\DailyInventoryKitchenController@select');
    Route::get('kitchen/{lbAppReviewId}/preview', 'Logbook\DailyInventoryKitchenController@preview');
    Route::post('kitchen/update', 'Logbook\DailyInventoryKitchenController@update');

    Route::resource('cashier', 'Logbook\DailyInventoryCashierController')->except(['show']);
    Route::get('cashier/dtble', 'Logbook\DailyInventoryCashierController@dtble');
    Route::get('cashier/select', 'Logbook\DailyInventoryCashierController@select');
    Route::get('cashier/{lbAppReviewId}/preview', 'Logbook\DailyInventoryCashierController@preview');
    Route::post('cashier/update', 'Logbook\DailyInventoryCashierController@update');

    Route::resource('warehouse', 'Logbook\DailyInventoryWarehouseController')->except(['show']);
    Route::get('warehouse/dtble', 'Logbook\DailyInventoryWarehouseController@dtble');
    Route::get('warehouse/select', 'Logbook\DailyInventoryWarehouseController@select');
    Route::get('warehouse/{lbAppReviewId}/preview', 'Logbook\DailyInventoryWarehouseController@preview');
    Route::post('warehouse/update', 'Logbook\DailyInventoryWarehouseController@update');
});

// application review
Route::resource('application-review', 'Logbook\ApplicationReviewController')->except(['show']);
Route::get('application-review/approve/{id}', 'Logbook\ApplicationReviewController@approve');
Route::get('application-review/unapprove/{id}', 'Logbook\ApplicationReviewController@unapprove');
Route::get('application-review/dtble', 'Logbook\ApplicationReviewController@dtble');
Route::get('application-review/preview/dtble', 'Logbook\ApplicationReviewController@previewDtble');
Route::get('application-review/create', 'Logbook\ApplicationReviewController@create');

// stock card
Route::resource('stock-card', 'Logbook\StockCardController')->except(['show']);
Route::get('stock-card/dtble', 'Logbook\StockCardController@dtble');
Route::get('stock-card/select', 'Logbook\StockCardController@select');
Route::get('stock-card/{lbAppReviewId}/preview', 'Logbook\StockCardController@preview');

// daily wasted
Route::resource('daily-wasted', 'Logbook\DailyWastedController')->except(['show']);
Route::get('daily-wasted/dtble', 'Logbook\DailyWastedController@dtble');
Route::get('daily-wasted/select', 'Logbook\DailyWastedController@select');
Route::get('daily-wasted/{lbAppReviewId}/preview', 'Logbook\DailyWastedController@preview');

// reception of material / product
Route::resource('reception-material', 'Logbook\ReceptionMaterialController')->except(['show']);
Route::get('reception-material/dtble', 'Logbook\ReceptionMaterialController@dtble');
Route::get('reception-material/select', 'Logbook\ReceptionMaterialController@select');
Route::get('reception-material/{lbAppReviewId}/preview', 'Logbook\ReceptionMaterialController@preview');

// daily-cleaning
Route::resource('daily-cleaning', 'Logbook\DailyCleaningController')->except(['show']);
Route::get('daily-cleaning/dtble', 'Logbook\DailyCleaningController@dtble');
Route::post('daily-cleaning/update', 'Logbook\DailyCleaningController@update');
Route::get('daily-cleaning/select', 'Logbook\DailyCleaningController@select');
Route::get('daily-cleaning/{lbAppReviewId}/preview', 'Logbook\DailyCleaningController@preview');

// operational
Route::group(['prefix' => 'operational'], function () {
    Route::resource('duty-roster', 'Logbook\DutyRosterController')->except(['show']);
    Route::get('duty-roster/dataview', 'Logbook\DutyRosterController@dataview');
    Route::get('duty-roster/dtble', 'Logbook\DutyRosterController@dtble');
    Route::get('duty-roster/select', 'Logbook\DutyRosterController@select');
    Route::get('duty-roster/{lbAppReviewId}/preview', 'Logbook\DutyRosterController@preview');
    Route::post('duty-roster/duty', 'Logbook\DutyRosterController@storeDuty');
    Route::put('duty-roster/duty/{id}', 'Logbook\DutyRosterController@updateDuty');

    // daily duties
    Route::resource('daily-duties', 'Logbook\DailyDutiesController')->except(['show']);
    Route::get('daily-duties/dtble', 'Logbook\DailyDutiesController@dtble');
    Route::post('daily-duties/update', 'Logbook\DailyDutiesController@update');
    Route::put('daily-duties/note/{id}', 'Logbook\DailyDutiesController@updateNote');
    Route::get('daily-duties/select', 'Logbook\DailyDutiesController@select');
    Route::get('daily-duties/{lbAppReviewId}/preview', 'Logbook\DailyDutiesController@preview');

    // cleaning duties
    Route::resource('cleaning-duties', 'Logbook\CleaningDutiesController')->except(['show']);
    Route::get('cleaning-duties/dataview', 'Logbook\CleaningDutiesController@dataview');
    Route::get('cleaning-duties/daily/dtble', 'Logbook\CleaningDutiesController@dailyDtble');
    Route::post('cleaning-duties/daily/update', 'Logbook\CleaningDutiesController@dailyUpdate');
    Route::get('cleaning-duties/weekly/dtble', 'Logbook\CleaningDutiesController@weeklyDtble');
    Route::post('cleaning-duties/weekly/update', 'Logbook\CleaningDutiesController@weeklyUpdate');
    Route::put('cleaning-duties/weekly/{id}', 'Logbook\CleaningDutiesController@weeklyUpdate');
    Route::put('cleaning-duties/note/{id}', 'Logbook\CleaningDutiesController@updateNote');
    Route::get('cleaning-duties/select', 'Logbook\CleaningDutiesController@select');
    Route::get('cleaning-duties/{lbAppReviewId}/preview', 'Logbook\CleaningDutiesController@preview');

    // water-meter
    Route::resource('water-meter', 'Logbook\WaterMeterController')->except(['show']);
    Route::get('water-meter/dtble', 'Logbook\WaterMeterController@dtble');
    Route::get('water-meter/select', 'Logbook\WaterMeterController@select');
    Route::get('water-meter/{lbAppReviewId}/preview', 'Logbook\WaterMeterController@preview');

    // electric-meter
    Route::resource('electric-meter', 'Logbook\ElectricMeterController')->except(['show']);
    Route::get('electric-meter/dtble', 'Logbook\ElectricMeterController@dtble');
    Route::get('electric-meter/select', 'Logbook\ElectricMeterController@select');
    Route::get('electric-meter/{lbAppReviewId}/preview', 'Logbook\ElectricMeterController@preview');

    // gas-meter
    Route::resource('gas-meter', 'Logbook\GasMeterController')->except(['show']);
    Route::get('gas-meter/dtble', 'Logbook\GasMeterController@dtble');
    Route::get('gas-meter/select', 'Logbook\GasMeterController@select');
    Route::get('gas-meter/{lbAppReviewId}/preview', 'Logbook\GasMeterController@preview');

    // env-propump
    Route::resource('env-propump', 'Logbook\EnvPropumpController')->except(['show']);
    Route::get('env-propump/dtble', 'Logbook\EnvPropumpController@dtble');
    Route::get('env-propump/select', 'Logbook\EnvPropumpController@select');
    Route::get('env-propump/{lbAppReviewId}/preview', 'Logbook\EnvPropumpController@preview');

    // env-wastewater
    Route::resource('env-wastewater', 'Logbook\EnvWastewaterController')->except(['show']);
    Route::get('env-wastewater/dtble', 'Logbook\EnvWastewaterController@dtble');
    Route::get('env-wastewater/select', 'Logbook\EnvWastewaterController@select');
    Route::get('env-wastewater/{lbAppReviewId}/preview', 'Logbook\EnvWastewaterController@preview');

    // env-solidwaste
    Route::resource('env-solidwaste', 'Logbook\EnvSolidwasteController')->except(['show']);
    Route::get('env-solidwaste/dtble', 'Logbook\EnvSolidwasteController@dtble');
    Route::get('env-solidwaste/select', 'Logbook\EnvSolidwasteController@select');
    Route::get('env-solidwaste/{lbAppReviewId}/preview', 'Logbook\EnvSolidwasteController@preview');

    // toilet checklist
    Route::resource('toilet', 'Logbook\ToiletController')->except(['show']);
    Route::get('toilet/dtble', 'Logbook\ToiletController@dtble');
    Route::post('toilet/update', 'Logbook\ToiletController@update');
    Route::get('toilet/select', 'Logbook\ToiletController@select');
    Route::get('toilet/{lbAppReviewId}/preview', 'Logbook\ToiletController@preview');

    // organoleptik
    Route::resource('organoleptik', 'Logbook\OrganoleptikController')->except(['show']);
    Route::get('organoleptik/dtble', 'Logbook\OrganoleptikController@dtble');
    Route::get('organoleptik/select', 'Logbook\OrganoleptikController@select');
    Route::get('organoleptik/{lbAppReviewId}/preview', 'Logbook\OrganoleptikController@preview');

    // temperature
    Route::resource('temperature', 'Logbook\TemperatureController')->except(['show']);
    Route::get('temperature/dtble', 'Logbook\TemperatureController@dtble');
    Route::get('temperature/select', 'Logbook\TemperatureController@select');
    Route::get('temperature/{lbAppReviewId}/preview', 'Logbook\TemperatureController@preview');
    Route::post('temperature/update', 'Logbook\TemperatureController@update');
});

// money-sales
Route::resource('money-sales', 'Logbook\MoneySalesController')->except(['show']);
Route::get('money-sales/dataview', 'Logbook\MoneySalesController@dataview');
Route::get('money-sales/cashier/dtble', 'Logbook\MoneySalesController@dtbleCashier');
Route::get('money-sales/detail/dtble', 'Logbook\MoneySalesController@dtbleDetail');
Route::put('money-sales/cashier/det/{id}', 'Logbook\MoneySalesController@cashierDetUpdate');
Route::put('money-sales/cashier/{id}', 'Logbook\MoneySalesController@cashierUpdate');
Route::post('money-sales/detail', 'Logbook\MoneySalesController@storeDetail');
Route::put('money-sales/detail/{id}', 'Logbook\MoneySalesController@updateDetail');
Route::delete('money-sales/detail/{id}', 'Logbook\MoneySalesController@destroyDetail');
Route::get('money-sales/{lbAppReviewId}/preview', 'Logbook\MoneySalesController@preview');

// production-planning
Route::resource('production-planning', 'Logbook\ProductionPlanningController')->except(['show']);
Route::get('production-planning/dataview', 'Logbook\ProductionPlanningController@dataview');
Route::get('production-planning/dtble', 'Logbook\ProductionPlanningController@dtble');
Route::post('production-planning/prodplan/update', 'Logbook\ProductionPlanningController@prodPlanUpdate');
Route::get('production-planning/prodtime/dtble', 'Logbook\ProductionPlanningController@prodTimedtble');
Route::post('production-planning/prodtime/update', 'Logbook\ProductionPlanningController@prodTimeUpdate');
Route::post('production-planning/prodtime/detail/update', 'Logbook\ProductionPlanningController@prodTimeDetailUpdate');
Route::get('production-planning/prodtime/detail', 'Logbook\ProductionPlanningController@prodTimedetail');
Route::post('production-planning/update', 'Logbook\ProductionPlanningController@update');

Route::get('production-planning/prodtemp/dtble', 'Logbook\ProductionPlanningController@prodTempdtble');
Route::post('production-planning/prodtemp', 'Logbook\ProductionPlanningController@prodTempStore');
Route::put('production-planning/prodtemp/{id}', 'Logbook\ProductionPlanningController@prodTempUpdate');
Route::delete('production-planning/prodtemp/{id}', 'Logbook\ProductionPlanningController@prodTempDestroy');

Route::get('production-planning/prodtempverify/dtble', 'Logbook\ProductionPlanningController@prodTempVerifydtble');
Route::post('production-planning/prodtempverify/update', 'Logbook\ProductionPlanningController@prodTempVerifyUpdate');

Route::get('production-planning/prodquality/dtble', 'Logbook\ProductionPlanningController@prodQualitydtble');
Route::post('production-planning/prodquality/update', 'Logbook\ProductionPlanningController@prodQualityUpdate');

Route::get('production-planning/produsedoil/dtble', 'Logbook\ProductionPlanningController@prodUsedoildtble');
Route::post('production-planning/produsedoil/update', 'Logbook\ProductionPlanningController@prodUsedoilUpdate');

Route::get('production-planning/select', 'Logbook\ProductionPlanningController@select');
Route::get('production-planning/{lbAppReviewId}/preview', 'Logbook\ProductionPlanningController@preview');

Route::resource('configuration-logbook', 'Logbook\ConfigurationController')->except(['show']);
