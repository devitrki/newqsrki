<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Financeacc\PettycashController;
use App\Http\Controllers\Financeacc\MassClearingController;

use App\Http\Controllers\Financeacc\AssetController;
use App\Http\Controllers\Financeacc\ValidatorController;
use App\Http\Controllers\Financeacc\AdminDepartController;
use App\Http\Controllers\Financeacc\AssetMutationController;
use App\Http\Controllers\Financeacc\AssetMutationManualController;
use App\Http\Controllers\Financeacc\PrintSJController;
use App\Http\Controllers\Financeacc\AssetRequestMutationController;
use App\Http\Controllers\Financeacc\AssetSoController;

use App\Http\Controllers\Financeacc\ConfigurationController;

/*
|--------------------------------------------------------------------------
| Web Finance Accounting Routes
|--------------------------------------------------------------------------
|
| \financeacc\...
|
*/

Route::group(['prefix' => 'asset'], function () {
    Route::resource('list', AssetController::class)->except(['show']);
    Route::get('list/dtble', [AssetController::class, 'dtble']);
    Route::post('list/sync', [AssetController::class, 'sync']);
    Route::get('list/dtble', [AssetController::class, 'dtble']);
    Route::post('list/check', [AssetController::class, 'checkAssetMutation']);
    Route::post('list/check/request', [AssetController::class, 'checkRequestAssetMutation']);
    Route::post('list/cancel', [AssetController::class, 'cancelAssetMutation']);
    Route::post('list/cancel/request', [AssetController::class, 'cancelRequestAssetMutation']);
    Route::get('costcenter/{plant_id}', [AssetController::class, 'selectCostCenter']);
    Route::post('list/request', [AssetController::class, 'storeRequest']);

    // check
    Route::get('check/{plant}', [AssetController::class, 'check']);

    Route::resource('mutation', AssetMutationController::class)->except(['show']);
    Route::get('mutation/dtble', [AssetMutationController::class, 'dtble']);
    Route::post('mutation/approve/{id}', [AssetMutationController::class, 'approveAssetMutation']);
    Route::post('mutation/unapprove/{id}', [AssetMutationController::class, 'unapproveAssetMutation']);
    Route::post('mutation/approve-request', [AssetMutationController::class, 'approveAssetRequest']);
    Route::post('mutation/unapprove-request', [AssetMutationController::class, 'unapproveAssetRequest']);
    Route::post('mutation/assign', [AssetMutationController::class, 'assignValidator']);
    Route::post('mutation/confirmation', [AssetMutationController::class, 'confirmationValidator']);
    Route::post('mutation/reject-validator', [AssetMutationController::class, 'rejectValidator']);
    Route::post('mutation/approve-am', [AssetMutationController::class, 'approveAMAssetMutation']);
    Route::post('mutation/unapprove-am', [AssetMutationController::class, 'unapproveAMAssetMutation']);
    Route::post('mutation/send', [AssetMutationController::class, 'send']);
    Route::post('mutation/reject-send', [AssetMutationController::class, 'rejectSend']);
    Route::post('mutation/accept', [AssetMutationController::class, 'accept']);
    Route::post('mutation/reject-accept', [AssetMutationController::class, 'rejectAccept']);
    Route::get('mutation/preview', [AssetMutationController::class, 'preview']);

    Route::resource('mutationmanual', AssetMutationManualController::class)->except(['show']);
    Route::get('mutationmanual/dtble', [AssetMutationManualController::class, 'dtble']);
    Route::get('mutationmanual/confirm/{id}', [AssetMutationManualController::class, 'confirm']);

    Route::resource('so', AssetSoController::class)->except(['show']);
    Route::get('so/select/costcenter/{plant_id}', [AssetSoController::class, 'selectCostCenter']);
    Route::get('so/download', [AssetSoController::class, 'download']);
    Route::get('so/preview', [AssetSoController::class, 'preview']);
    Route::post('so/upload', [AssetSoController::class, 'upload']);
    Route::get('so/select/periode', [AssetSoController::class, 'selectPeriode']);
    Route::get('so/generate/{type}', [AssetSoController::class, 'manualGenerateSO']);
    Route::get('so/submit/{type}', [AssetSoController::class, 'manualSubmitSO']);
    Route::get('so/check', [AssetSoController::class, 'checkCountAssetSo']);
    Route::get('so/fix', [AssetSoController::class, 'fixQtyWeb']);

    // mapping validator
    Route::resource('validator', ValidatorController::class)->except(['show']);
    Route::get('validator/dtble', [ValidatorController::class, 'dtble']);
    Route::get('validator/select', [ValidatorController::class, 'select']);

    Route::get('validator/dtble/pic/{id}', [ValidatorController::class, 'dtblePic']);
    Route::post('validator/pic', [ValidatorController::class, 'storePic']);
    Route::put('validator/pic/{id}', [ValidatorController::class, 'updatePic']);
    Route::delete('validator/pic/{id}', [ValidatorController::class, 'destroyPic']);

    // asset request mutation
    Route::resource('request', AssetRequestMutationController::class)->except(['show']);
    Route::get('request/dtble', [AssetRequestMutationController::class, 'dtble']);
    Route::get('request/select', [AssetRequestMutationController::class, 'select']);
    Route::post('request/assign', [AssetRequestMutationController::class, 'assignValidator']);
    Route::post('request/confirmation', [AssetRequestMutationController::class, 'confirmationValidator']);
    Route::post('request/send', [AssetRequestMutationController::class, 'sendAssetRequest']);
    Route::post('request/reject-validator', [AssetRequestMutationController::class, 'rejectValidator']);
    Route::post('request/approve', [AssetRequestMutationController::class, 'approveAssetRequest']);
    Route::post('request/unapprove', [AssetRequestMutationController::class, 'unapproveAssetRequest']);

    // asset mapping user admin department
    Route::resource('admin-depart', AdminDepartController::class)->except(['show']);
    Route::get('admin-depart/dtble', [AdminDepartController::class, 'dtble']);
    Route::get('admin-depart/select', [AdminDepartController::class, 'select']);

    // asset print sj
    Route::resource('printsj', PrintSJController::class)->except(['show']);
    Route::get('printsj/dtble', [PrintSJController::class, 'dtble']);
    Route::get('printsj/preview', [PrintSJController::class, 'preview']);
});

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
