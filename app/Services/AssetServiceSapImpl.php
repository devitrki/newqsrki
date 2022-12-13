<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;

use App\Library\Helper;

use App\Repositories\SapRepositorySapImpl;
use App\Entities\SapMiddleware;

use App\Models\Company;
use App\Models\Plant;
use App\Models\Financeacc\AssetMutation;

class AssetServiceSapImpl implements AssetService
{
    public function syncAsset($plantId)
    {
        $status = true;
        $message = '';

        $plant = DB::table('plants')
                    ->where('id', $plantId)
                    ->first();

        $sapCodeComp = Company::getConfigByKey($plant->company_id, 'SAP_CODE');
        if (!$sapCodeComp || $sapCodeComp == '') {
            return [
                'status' => false,
                'message' => Lang::get('Please set SAP_CODE in company configuration'),
            ];
        }

        $param = [
            'company_id' => $sapCodeComp,
            'plant_id' => [$plant->code],
        ];

        $sapRepository = new SapRepositorySapImpl($plant->company_id);
        $sapResponse = $sapRepository->syncAsset($param);

        $assetsaps = [];

        if ($sapResponse['status']) {
            $assetsaps = $sapResponse['response'];

            DB::beginTransaction();

            // delete asset exist plant
            DB::table('assets')
                ->where('plant_id', $plantId)
                ->delete();

            // setup insert all data from sap
            $inserts = [];

            foreach ($assetsaps as $assetsap) {

                $inserts[] = [
                    'company_id' => $plant->company_id,
                    'number' => $assetsap['asset_id'],
                    'number_sub' => $assetsap['asset_sub_id'],
                    'plant_id' => $plantId,
                    'description' => $assetsap['name'],
                    'spec_user' => $assetsap['user_spec'],
                    'qty_web' => round($assetsap['qty']),
                    'uom' => $assetsap['uom_id'],
                    'cost_center' => $assetsap['cost_center_name'],
                    'cost_center_code' => $assetsap['cost_center_id'],
                    'remark' => $assetsap['remark'],
                    "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
                    "updated_at" => \Carbon\Carbon::now(),
                ];

            }

            $insertAssetCollect = collect($inserts);
            $insertAssetChunks = $insertAssetCollect->chunk(100);

            $success = true;

            foreach ($insertAssetChunks as $insertAssetChunk)
            {
                $success = DB::table('assets')->insert($insertAssetChunk->toArray());
            }

            if ($success) {
                DB::commit();
                $status = true;
                $message = Lang::get("message.sync.success", ["data" => Lang::get("asset")]);
            } else {
                DB::rollback();
                $status = false;
                $message = Lang::get("message.sync.failed", ["data" => Lang::get("asset")]);
            }
        }

        return [
            'status' => $status,
            'message' => $message,
            'data' => $assetsaps
        ];
    }

    public function mutationAsset($assetMutation)
    {
        $status = true;
        $message = '';

        $sapCodeComp = Company::getConfigByKey($assetMutation->company_id, 'SAP_CODE');
        if (!$sapCodeComp || $sapCodeComp == '') {
            return [
                'status' => false,
                'message' => Lang::get('Please set SAP_CODE in company configuration'),
            ];
        }

        $payload = [
            'company_id' => $sapCodeComp,
            'asset_number' => $assetMutation->number,
            'sub_asset_number' => $assetMutation->number_sub,
            'cost_center' => $assetMutation->to_cost_center_code,
            'plant_id' => Plant::getCodeById($assetMutation->to_plant_id),
            'sloc_id' => Plant::getSlocIdAssetMutation($assetMutation->to_plant_id),
        ];

        $sapRepository = new SapRepositorySapImpl($assetMutation->company_id, true);
        $sapResponse = $sapRepository->mutationAsset($payload);

        if ($sapResponse['status']) {
            $resSap = $sapResponse['response'];

            $status = true;

            if (!(bool)$resSap['success']) {
                $status = false;
                $message = SapMiddleware::getLastErrorMessage($resSap['logs']);
            }

        } else {
            $status = false;
            $message = Lang::get("Sorry, an error occurred, please try again later");
        }

        return [
            'status' => $status,
            'message' => $message
        ];

    }

    public function checkChangeAsset($id, $number_asset, $sub_number_asset, $from_plant_id, $from_cost_center_code, $to_plant_id, $to_cost_center_code)
    {
        DB::beginTransaction();

        $alreadyInTo = false; // flag for asset receive to plant receive
        $goneInFrom = true; // flag for asset gone from plant sender

        $fromPlantCode = Plant::getCodeById($from_plant_id);
        $toPlantCode = Plant::getCodeById($to_plant_id);

        $response = $this->syncAsset($to_plant_id);

        if ($response['status']) {
            $assetSapTos = $response['data'];

            $inserts = [];

            $plantTo = DB::table('plants')
                        ->where('id', $to_plant_id)
                        ->first();

            // delete asset exist plant to
            DB::table('assets')->where('plant_id', $to_plant_id)->delete();

            // setup insert all data from sap to plant to
            foreach ($assetSapTos as $assetSapTo) {

                // check asset have already in plant receive
                if ($number_asset == $assetSapTo['asset_number'] && $sub_number_asset == $assetSapTo['sub_number'] && $to_cost_center_code == $assetSapTo['cc_code'] &&  $toPlantCode == $assetSapTo['plant']) {
                    $alreadyInTo = true;
                }

                // accommodate all data to var insert
                $inserts[] = [
                    'company_id' => $plantTo->company_id,
                    'number' => $assetSapTo['asset_id'],
                    'number_sub' => $assetSapTo['asset_sub_id'],
                    'plant_id' => $to_plant_id,
                    'description' => $assetSapTo['name'],
                    'spec_user' => $assetSapTo['user_spec'],
                    'qty_web' => round($assetSapTo['qty']),
                    'uom' => $assetSapTo['uom_id'],
                    'cost_center' => $assetSapTo['cost_center_desc'],
                    'cost_center_code' => $assetSapTo['cost_center_id'],
                    'remark' => $assetSapTo['remark'],
                    "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
                    "updated_at" => \Carbon\Carbon::now(),
                ];

            }

            // insert all asset to plant receive
            $insertAssetCollect = collect($inserts);
            $insertAssetChunks = $insertAssetCollect->chunk(100);

            $success = true;

            foreach ($insertAssetChunks as $insertAssetChunk)
            {
                $success = DB::table('assets')->insert($insertAssetChunk->toArray());
            }

            if ($success) {
                $response = $this->syncAsset($from_plant_id);

                if ($response['status']) {
                    $assetSapFroms = $response['data'];

                    $inserts = [];

                    $plantFrom = DB::table('plants')
                                ->where('id', $from_plant_id)
                                ->first();

                    // delete asset exist plant from / plant sender
                    DB::table('assets')->where('plant_id', $from_plant_id)->delete();

                    // setup insert all data from sap
                    foreach ($assetSapFroms as $assetSapFrom) {

                        // check asset already gone from plant from / plant sender or not
                        if ($number_asset == $assetSapFrom['asset_number'] && $sub_number_asset == $assetSapFrom['sub_number'] && $from_cost_center_code == $assetSapFrom['cc_code'] && $fromPlantCode == $assetSapFrom['plant']) {
                            $goneInFrom = false;
                        }

                        // accommodate all asset sap to var insert
                        $inserts[] = [
                            'company_id' => $plantFrom->company_id,
                            'number' => $assetSapFrom['asset_id'],
                            'number_sub' => $assetSapFrom['asset_sub_id'],
                            'plant_id' => $from_plant_id,
                            'description' => $assetSapFrom['name'],
                            'spec_user' => $assetSapFrom['user_spec'],
                            'qty_web' => round($assetSapFrom['qty']),
                            'uom' => $assetSapFrom['uom_id'],
                            'cost_center' => $assetSapFrom['cost_center_desc'],
                            'cost_center_code' => $assetSapFrom['cost_center_id'],
                            'remark' => $assetSapFrom['remark'],
                            "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
                            "updated_at" => \Carbon\Carbon::now(),
                        ];
                    }

                    // insert all asset sap plant from / plant sender
                    $insertAssetCollect = collect($inserts);
                    $insertAssetChunks = $insertAssetCollect->chunk(100);

                    $success = true;

                    foreach ($insertAssetChunks as $insertAssetChunk)
                    {
                        $success = DB::table('assets')->insert($insertAssetChunk->toArray());
                    }

                    if ($success) {
                        DB::commit();

                        $assetMutation = AssetMutation::find($id);

                        if ($goneInFrom && $alreadyInTo) {

                            $assetMutation->status_changed = 1;

                        } else {

                            $assetMutation->status_changed = 0;

                        }

                        $assetMutation->save();

                    } else {
                        DB::rollback();
                        Log::error('Check Change SAP Asset :' . $number_asset . '-' . $sub_number_asset .
                        ' from ' . $from_plant_id . ' to ' . $to_plant_id . ' Error : failed to insert data asset plant from');
                    }

                } else {
                    DB::rollback();
                    Log::error('Check Change SAP Asset :' . $number_asset . '-' . $sub_number_asset .
                    ' from ' . $from_plant_id . ' to ' . $to_plant_id . ' Error : failed to get data plant from, from sap');
                }

            } else {
                DB::rollback();
                Log::error('Check Change SAP Asset :' . $number_asset . '-' . $sub_number_asset .
                ' from ' . $from_plant_id . ' to ' . $to_plant_id . ' Error : failed to insert data asset plant to');
            }

        } else {
            DB::rollback();
            Log::error('Check Change SAP Asset :' . $number_asset . '-' . $sub_number_asset .
                ' from ' . $from_plant_id . ' to ' . $to_plant_id . ' Error : failed to get data plant to from sap');
        }

    }
}
