<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;

use App\Repositories\SapRepositoryAppsImpl;

use App\Models\Plant;
use App\Models\Financeacc\AssetMutation;

class AssetServiceAppsImpl implements AssetService
{
    public function syncAsset($plantId)
    {
        $status = true;
        $message = '';

        $plant = DB::table('plants')
                    ->where('id', $plantId)
                    ->first();

        $param = [
            'plant' => $plant->code
        ];

        $sapRepository = new SapRepositoryAppsImpl();
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
                    'number' => $assetsap['asset_number'],
                    'number_sub' => $assetsap['sub_number'],
                    'plant_id' => $plantId,
                    'description' => $assetsap['description'],
                    'spec_user' => $assetsap['spec_user'],
                    'qty_web' => round($assetsap['qty']),
                    'uom' => $assetsap['uom'],
                    'cost_center' => $assetsap['cost_center'],
                    'cost_center_code' => $assetsap['cc_code'],
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

        } else {
            $status = false;
            $message = Lang::get("Sorry, an error occurred, please try again later");
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

        $param = [
            'sap-client' => config('qsrki.api.sap.client'),
            'pgmna' => 'zwsrki001',
            'p_anln1' => $assetMutation->number,
            'p_anln2' => $assetMutation->number_sub,
            'p_kostl' => $assetMutation->to_cost_center_code,
            'p_werks' => Plant::getCodeById($assetMutation->to_plant_id),
            'p_lgort' => 'r100',
        ];

        !dd($param);

        $sapRepository = new SapRepositoryAppsImpl();
        $sapResponse = $sapRepository->mutationAsset($param);

        if ($sapResponse['status']) {
            $status = true;
            $res_sap = $sapResponse['response'];

            $last_resp_sap = $res_sap[sizeof($res_sap) - 1];
            if ($last_resp_sap['type'] != 'S') {
                $status = false;
                $message = 'Feedback SAP : ' . $last_resp_sap['msg'];
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
                    'number' => $assetSapTo['asset_number'],
                    'number_sub' => $assetSapTo['sub_number'],
                    'plant_id' => $to_plant_id,
                    'description' => $assetSapTo['description'],
                    'spec_user' => $assetSapTo['spec_user'],
                    'qty_web' => round($assetSapTo['qty']),
                    'uom' => $assetSapTo['uom'],
                    'cost_center' => $assetSapTo['cost_center'],
                    'cost_center_code' => $assetSapTo['cc_code'],
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
                            'number' => $assetSapFrom['asset_number'],
                            'number_sub' => $assetSapFrom['sub_number'],
                            'plant_id' => $from_plant_id,
                            'description' => $assetSapFrom['description'],
                            'spec_user' => $assetSapFrom['spec_user'],
                            'qty_web' => round($assetSapFrom['qty']),
                            'uom' => $assetSapFrom['uom'],
                            'cost_center' => $assetSapFrom['cost_center'],
                            'cost_center_code' => $assetSapFrom['cc_code'],
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
