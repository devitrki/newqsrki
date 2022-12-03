<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Application Routes
|--------------------------------------------------------------------------
|
| \interfaces\...
|
*/

// interface api accurate gion
Route::group(['prefix' => 'gion'], function () {
  Route::group(['prefix' => 'accurate'], function () {
    Route::get('/authorize-token', 'Interfaces\Gion\AccurateController@authorizeToken');

    Route::resource('mapping-item', 'Interfaces\Gion\MappingItemController')->except(['show']);
    Route::get('mapping-item/dtble', 'Interfaces\Gion\MappingItemController@dtble');

    Route::resource('mapping-payment', 'Interfaces\Gion\MappingPaymentController')->except(['show']);
    Route::get('mapping-payment/dtble', 'Interfaces\Gion\MappingPaymentController@dtble');

    Route::resource('mapping-unit', 'Interfaces\Gion\MappingUnitController')->except(['show']);
    Route::get('mapping-unit/dtble', 'Interfaces\Gion\MappingUnitController@dtble');

    Route::resource('mapping-branch', 'Interfaces\Gion\MappingBranchController')->except(['show']);
    Route::get('mapping-branch/dtble', 'Interfaces\Gion\MappingBranchController@dtble');
    Route::get('mapping-branch/select', 'Interfaces\Gion\MappingBranchController@select');

    Route::resource('upload-sales', 'Interfaces\Gion\UploadSalesController')->except(['show']);
    Route::get('upload', 'Interfaces\Gion\UploadSalesController@upload');
    Route::get('posted', 'Interfaces\Gion\UploadSalesController@postedSalesAccurate');
    Route::get('selisih', 'Interfaces\Gion\UploadSalesController@cariSelisih');
  });
});

// interface vtec
Route::group(['prefix' => 'vtec'], function () {
    Route::resource('sort-payment-vtec', 'Interfaces\Vtec\SortPaymentVtecController')->except(['show']);
    Route::get('sort-payment-vtec/dtble', 'Interfaces\Vtec\SortPaymentVtecController@dtble');

    Route::resource('mapping-database-store-vtec', 'Interfaces\Vtec\MappingDatabaseStoreVtecController')->except(['show']);
    Route::get('mapping-database-store-vtec/dtble', 'Interfaces\Vtec\MappingDatabaseStoreVtecController@dtble');
    Route::get('get-configuration-database/{store}', 'Interfaces\Vtec\MappingDatabaseStoreVtecController@getConfigurationDatabase');

    Route::resource('send-manual-vtec', 'Interfaces\Vtec\SendManualVtecController')->except(['show']);
    Route::get('send-manual-vtec/dtble', 'Interfaces\Vtec\SendManualVtecController@dtble');
    Route::get('send-manual-vtec/view', 'Interfaces\Vtec\SendManualVtecController@view');

    Route::resource('upload-sales-vtec', 'Interfaces\Vtec\UploadSalesVtecController')->except(['show']);
    Route::get('upload-sales-vtec/view', 'Interfaces\Vtec\UploadSalesVtecController@view');

    Route::post('upload-sales-vtec/confirmation', 'Interfaces\Vtec\UploadSalesVtecController@confirmation');

    Route::get('upload-sales-vtec/test', 'Interfaces\Vtec\UploadSalesVtecController@test');
});

// interface aloha
Route::group(['prefix' => 'aloha'], function () {
    Route::resource('send-manual-aloha', 'Interfaces\Aloha\SendManualAlohaController')->except(['show']);
    Route::get('send-manual-aloha/dtble', 'Interfaces\Aloha\SendManualAlohaController@dtble');
    Route::get('send-manual-aloha/view', 'Interfaces\Aloha\SendManualAlohaController@view');
});

Route::resource('configuration-interface', 'Interfaces\ConfigurationController')->except(['show']);
