<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Financeacc\PettycashController;
use App\Http\Controllers\Financeacc\MassClearingController;
use App\Http\Controllers\Financeacc\ConfigurationController;

/*
|--------------------------------------------------------------------------
| Web Finance Accounting Routes
|--------------------------------------------------------------------------
|
| \financeacc\...
|
*/

// Route::group(['prefix' => 'asset'], function () {
//     Route::resource('list', 'Financeacc\AssetController')->except(['show']);
//     Route::get('list/dtble', 'Financeacc\AssetController@dtble');
//     Route::post('list/sync', 'Financeacc\AssetController@sync');
//     Route::get('list/dtble', 'Financeacc\AssetController@dtble');
//     Route::post('list/check', 'Financeacc\AssetController@checkAssetMutation');
//     Route::post('list/check/request', 'Financeacc\AssetController@checkRequestAssetMutation');
//     Route::post('list/cancel', 'Financeacc\AssetController@cancelAssetMutation');
//     Route::post('list/cancel/request', 'Financeacc\AssetController@cancelRequestAssetMutation');
//     Route::get('costcenter/{plant_id}', 'Financeacc\AssetController@selectCostCenter');
//     Route::post('list/request', 'Financeacc\AssetController@storeRequest');

//     // check
//     Route::get('check/{plant}', 'Financeacc\AssetController@check');

//     Route::resource('mutation', 'Financeacc\AssetMutationController')->except(['show']);
//     Route::get('mutation/dtble', 'Financeacc\AssetMutationController@dtble');
//     Route::post('mutation/approve/{id}', 'Financeacc\AssetMutationController@approveAssetMutation');
//     Route::post('mutation/unapprove/{id}', 'Financeacc\AssetMutationController@unapproveAssetMutation');
//     Route::post('mutation/approve-request', 'Financeacc\AssetMutationController@approveAssetRequest');
//     Route::post('mutation/unapprove-request', 'Financeacc\AssetMutationController@unapproveAssetRequest');
//     Route::post('mutation/assign', 'Financeacc\AssetMutationController@assignValidator');
//     Route::post('mutation/confirmation', 'Financeacc\AssetMutationController@confirmationValidator');
//     Route::post('mutation/reject-validator', 'Financeacc\AssetMutationController@rejectValidator');
//     Route::post('mutation/approve-am', 'Financeacc\AssetMutationController@approveAMAssetMutation');
//     Route::post('mutation/unapprove-am', 'Financeacc\AssetMutationController@unapproveAMAssetMutation');
//     Route::post('mutation/send', 'Financeacc\AssetMutationController@send');
//     Route::post('mutation/reject-send', 'Financeacc\AssetMutationController@rejectSend');
//     Route::post('mutation/accept', 'Financeacc\AssetMutationController@accept');
//     Route::post('mutation/reject-accept', 'Financeacc\AssetMutationController@rejectAccept');
//     Route::get('mutation/preview', 'Financeacc\AssetMutationController@preview');

//     Route::resource('mutationmanual', 'Financeacc\AssetMutationManualController')->except(['show']);
//     Route::get('mutationmanual/dtble', 'Financeacc\AssetMutationManualController@dtble');
//     Route::get('mutationmanual/confirm/{id}', 'Financeacc\AssetMutationManualController@confirm');

//     Route::resource('so', 'Financeacc\AssetSoController')->except(['show']);
//     Route::get('so/select/costcenter/{plant_id}', 'Financeacc\AssetSoController@selectCostCenter');
//     Route::get('so/download', 'Financeacc\AssetSoController@download');
//     Route::get('so/preview', 'Financeacc\AssetSoController@preview');
//     Route::post('so/upload', 'Financeacc\AssetSoController@upload');
//     Route::get('so/select/periode', 'Financeacc\AssetSoController@selectPeriode');
//     Route::get('so/generate/{type}', 'Financeacc\AssetSoController@manualGenerateSO');
//     Route::get('so/submit/{type}', 'Financeacc\AssetSoController@manualSubmitSO');
//     Route::get('so/check', 'Financeacc\AssetSoController@checkCountAssetSo');
//     Route::get('so/fix', 'Financeacc\AssetSoController@fixQtyWeb');

//     // mapping validator
//     Route::resource('validator', 'Financeacc\ValidatorController')->except(['show']);
//     Route::get('validator/dtble', 'Financeacc\ValidatorController@dtble');
//     Route::get('validator/select', 'Financeacc\ValidatorController@select');

//     Route::get('validator/dtble/pic/{id}', 'Financeacc\ValidatorController@dtblePic');
//     Route::post('validator/pic', 'Financeacc\ValidatorController@storePic');
//     Route::put('validator/pic/{id}', 'Financeacc\ValidatorController@updatePic');
//     Route::delete('validator/pic/{id}', 'Financeacc\ValidatorController@destroyPic');

//     // asset request mutation
//     Route::resource('request', 'Financeacc\AssetRequestMutationController')->except(['show']);
//     Route::get('request/dtble', 'Financeacc\AssetRequestMutationController@dtble');
//     Route::get('request/select', 'Financeacc\AssetRequestMutationController@select');
//     Route::post('request/assign', 'Financeacc\AssetRequestMutationController@assignValidator');
//     Route::post('request/confirmation', 'Financeacc\AssetRequestMutationController@confirmationValidator');
//     Route::post('request/send', 'Financeacc\AssetRequestMutationController@sendAssetRequest');
//     Route::post('request/reject-validator', 'Financeacc\AssetRequestMutationController@rejectValidator');
//     Route::post('request/approve', 'Financeacc\AssetRequestMutationController@approveAssetRequest');
//     Route::post('request/unapprove', 'Financeacc\AssetRequestMutationController@unapproveAssetRequest');

//     // asset mapping user admin department
//     Route::resource('admin-depart', 'Financeacc\AdminDepartController')->except(['show']);
//     Route::get('admin-depart/dtble', 'Financeacc\AdminDepartController@dtble');
//     Route::get('admin-depart/select', 'Financeacc\AdminDepartController@select');

//     // asset print sj
//     Route::resource('printsj', 'Financeacc\PrintSJController')->except(['show']);
//     Route::get('printsj/dtble', 'Financeacc\PrintSJController@dtble');
//     Route::get('printsj/preview', 'Financeacc\PrintSJController@preview');
// });

Route::resource('pettycash', PettycashController::class)->except(['show']);
Route::get('pettycash/dtble', [PettycashController::class, 'dtble']);
Route::get('pettycash/preview/dtble', [PettycashController::class, 'dtblePreview']);
Route::get('pettycash/select', [PettycashController::class, 'select']);
Route::post('pettycash/edit', [PettycashController::class, 'edit']);
Route::post('pettycash/edit-no-po', [PettycashController::class, 'editNoPo']);
Route::get('pettycash/approve/{id}', [PettycashController::class, 'approve']);
Route::get('pettycash/download/template', [PettycashController::class, 'downloadTemplate']);
Route::post('pettycash/unapprove', [PettycashController::class, 'unapprove']);
Route::post('pettycash/reject', [PettycashController::class, 'reject']);
Route::post('pettycash/create-multiple', [PettycashController::class, 'storeMultiple']);
Route::post('pettycash/submit', [PettycashController::class, 'submit']);
Route::get('pettycash/migration', [PettycashController::class, 'migration']);
Route::get('pettycash/migration/check', [PettycashController::class, 'migrationCheck']);
Route::get('pettycash/fix-saldo', [PettycashController::class, 'fixSaldo']);

Route::resource('mass-clearing', MassClearingController::class)->except(['show']);
Route::get('mass-clearing/dtble', [MassClearingController::class, 'dtble']);
Route::get('mass-clearing/preview/dtble', [MassClearingController::class, 'dtblePreview']);
Route::get('mass-clearing/download/template', [MassClearingController::class, 'downloadTemplate']);
Route::get('mass-clearing/generate', [MassClearingController::class, 'generate']);
Route::get('mass-clearing/download/generate/{id}', [MassClearingController::class, 'downloadGenerate']);

Route::resource('configuration-financeacc', ConfigurationController::class)->except(['show']);
