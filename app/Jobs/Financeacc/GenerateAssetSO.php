<?php

namespace App\Jobs\Financeacc;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Library\Helper;
use App\Models\Plant;
use App\Models\Financeacc\AssetSo;
use App\Models\Financeacc\AssetSoPlant;
use App\Models\Financeacc\AssetSoDetail;
use App\Models\NotificationSystemRead;
use App\Models\Configuration;

class GenerateAssetSO implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $typePlant;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($typePlant)
    {
        $this->typePlant = $typePlant;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $typePlant = $this->typePlant;

            // get asset so id this periode
            $assetSoId = $this->getAssetSoId();

            // get cost center exclude
            $costCenterExludes = Configuration::getValueByKeyFor('financeacc', 'cost_center_exclude');
            $costCenterExludes = explode(',', $costCenterExludes);

            // type plant, 1 = outlet, 2 = DC
            if ($typePlant != 'dc') {
                // update status so asset outlet true
                $statusAssetSoOutlet = Configuration::getValueByKeyFor('financeacc', 'status_outlet_asset_so');
                if ($statusAssetSoOutlet != 'Running') {
                    Configuration::setValueByKeyFor('financeacc', 'status_outlet_asset_so', 'Running');
                }
            } else {
                // update status so asset dc true
                $statusAssetSoDC = Configuration::getValueByKeyFor('financeacc', 'status_dc_asset_so');
                if ($statusAssetSoDC != 'Running') {
                    Configuration::setValueByKeyFor('financeacc', 'status_dc_asset_so', 'Running');
                }
            }

            // get template notification system for asset so by key
            $keyNotificationAsset = Configuration::getValueByKeyFor('financeacc', 'key_notification_asset_so');
            $qNotifSystem = DB::table('notification_systems')
                                ->select('id')
                                ->where('key', $keyNotificationAsset);
            $notificationSystemId = 0;
            if($qNotifSystem->count() > 0){
                $notifSystem = $qNotifSystem->first();
                $notificationSystemId = $notifSystem->id;
            }

            if($assetSoId != 0){
                if ($typePlant != 'dc') {
                    Log::info('Generate asset outlet periode ' . date('n-Y') . ' running');
                } else {
                    Log::info('Generate asset dc periode ' . date('n-Y') . ' running');
                }

                // get plant already generated asset so
                $qPlantCCAlreadyGenerate = DB::table('asset_so_plants')
                                            ->leftJoin('plants', 'plants.id', 'asset_so_plants.plant_id')
                                            ->where('asset_so_plants.asset_so_id', $assetSoId);

                // type plant, 1 = outlet, 2 = DC
                if($typePlant != 'dc'){
                    // outlet
                    $qPlantCCAlreadyGenerate = $qPlantCCAlreadyGenerate->where('plants.type', 1);
                } else {
                    // dc
                    $qPlantCCAlreadyGenerate = $qPlantCCAlreadyGenerate->where('plants.type', 2);
                }

                $plantCCAlreadyGenerates = $qPlantCCAlreadyGenerate
                                            ->distinct()
                                            ->pluck('asset_so_plants.plant_id')
                                            ->toArray();

                $plantAlreadyGenerate = [];

                foreach ($plantCCAlreadyGenerates as $plantCCAlreadyGenerate) {

                    // get all cc
                    $ccHaveGenerated = DB::table('asset_so_plants')
                                            ->where('plant_id', $plantCCAlreadyGenerate)
                                            ->where('asset_so_id', $assetSoId)
                                            ->pluck('cost_center_code')
                                            ->toArray();

                    // check all cost center generated
                    $checkAllCCAssetGen = DB::table('assets')
                                            ->where('plant_id', $plantCCAlreadyGenerate)
                                            ->whereNotIn('cost_center_code', $ccHaveGenerated)
                                            ->whereNotIn('cost_center_code', $costCenterExludes)
                                            ->count();

                    if($checkAllCCAssetGen <= 0){
                        $plantAlreadyGenerate[] = $plantCCAlreadyGenerate;
                    }

                }

                // get 10 plant to generate not in plant already generate
                $qPlantToGenerates = DB::table('assets')
                                        ->leftJoin('plants', 'plants.id', 'assets.plant_id')
                                        ->whereNotIn('assets.plant_id', $plantAlreadyGenerate)
                                        ->distinct()
                                        ->limit(10);

                // type plant, 1 = outlet, 2 = DC
                if ($typePlant != 'dc') {
                    // outlet
                    $qPlantToGenerates = $qPlantToGenerates->where('plants.type', 1);
                } else {
                    // dc
                    $qPlantToGenerates = $qPlantToGenerates->where('plants.type', 2);
                }

                // check if there are still plants that have not been generated
                if($qPlantToGenerates->count() > 0){

                    // get plant id to generate to array
                    $plantToGenerates = $qPlantToGenerates->pluck('assets.plant_id')->toArray();

                    foreach ($plantToGenerates as $plantToGenerate) {

                        // get all cost center having asset in plant
                        $costCenters = DB::table('assets')
                                        ->where('plant_id', $plantToGenerate)
                                        ->whereNotIn('cost_center_code', $costCenterExludes)
                                        ->distinct()
                                        ->pluck('cost_center', 'cost_center_code')
                                        ->toArray();

                        foreach ($costCenters as $costCenterCode => $costCenter) {

                            // check cost center already generate
                            $checkCCAssetSoHaveGenerated = DB::table('asset_so_plants')
                                                            ->where('asset_so_id', $assetSoId)
                                                            ->where('plant_id', $plantToGenerate)
                                                            ->where('cost_center_code', $costCenterCode)
                                                            ->count();

                            if($checkCCAssetSoHaveGenerated > 0){
                                continue;
                            }

                            DB::beginTransaction();

                            $insertAssetSO = true;

                            // insert into to asset so plant
                            $assetSoPlant = new AssetSoPlant;
                            $assetSoPlant->upload_code = AssetSo::generateUploadCode($plantToGenerate, $costCenterCode);
                            $assetSoPlant->asset_so_id = $assetSoId;
                            $assetSoPlant->plant_id = $plantToGenerate;
                            $assetSoPlant->cost_center = $costCenter;
                            $assetSoPlant->cost_center_code = $costCenterCode;

                            if($assetSoPlant->save()){

                                // get all asset by plant and costcenter
                                $assetPlants = DB::table('assets')
                                                ->select(
                                                    'number',
                                                    'number_sub',
                                                    'description',
                                                    'spec_user',
                                                    'qty_web',
                                                    'uom',
                                                    'remark'
                                                )
                                                ->where('plant_id', $plantToGenerate)
                                                ->where('cost_center_code', $costCenterCode)
                                                ->orderBy('description')
                                                ->get();

                                foreach ($assetPlants as $assetPlant) {

                                    // insert into asset so detail
                                    $assetSoDetail = new AssetSoDetail;
                                    $assetSoDetail->asset_so_plant_id = $assetSoPlant->id;
                                    $assetSoDetail->number = $assetPlant->number;
                                    $assetSoDetail->number_sub = $assetPlant->number_sub;
                                    $assetSoDetail->description = $assetPlant->description;
                                    $assetSoDetail->spec_user = $assetPlant->spec_user;
                                    $assetSoDetail->qty_web  = $assetPlant->qty_web;
                                    $assetSoDetail->uom = $assetPlant->uom;
                                    $assetSoDetail->remark = $assetPlant->remark;
                                    $assetSoDetail->qty_so = 0;
                                    $assetSoDetail->qty_selisih = 0 - $assetPlant->qty_web;
                                    $assetSoDetail->remark_so = '';
                                    if($assetSoDetail->save()){
                                        $insertAssetSO = true;
                                    } else {
                                        $insertAssetSO = false;
                                        break;
                                        Log::alert('Generate asset so failed save so detail, plant : ' .
                                            $plantToGenerate . ' costcenter : ' . $costCenter);
                                    }

                                }

                            } else {
                                $insertAssetSO = false;
                                Log::alert('Generate asset so failed save so plant, plant : ' .
                                    $plantToGenerate . ' costcenter : ' . $costCenter);
                            }

                            if($insertAssetSO){
                                DB::commit();
                            } else {
                                DB::rollBack();
                            }

                        }

                        // send notification for store manager plant that asset so already generated
                        $userModId = Plant::getMODIdPlantById($plantToGenerate);

                        // check mod for store already mapping or not
                        // if not yet mappping don't send notification
                        if ($userModId != 0 && $notificationSystemId != 0) {

                            // send notification for outlet
                            $notificationSystemRead = new NotificationSystemRead;
                            $notificationSystemRead->notification_system_id = $notificationSystemId;
                            $notificationSystemRead->user_id = $userModId;
                            $notificationSystemRead->read = 0;
                            $notificationSystemRead->save();
                        }

                    }

                } else {
                    // all already generate
                    // update status
                    $assetSo = AssetSo::find($assetSoId);
                    if ($typePlant != 'dc') {
                        $assetSo->status_generate_outlet = 1;
                    } else {
                        $assetSo->status_generate_dc = 1;
                    }

                    $assetSo->save();
                }

            } else {
                if ($typePlant != 'dc') {
                    Log::alert('Generate asset so outlet failed to get asset so id');
                } else {
                    Log::alert('Generate asset so dc failed to get asset so id');
                }
            }
        } catch (\Throwable $th) {
            Log::alert('Generate asset so failed : ' . $th->getMessage());
        }

    }

    public function getAssetSoId()
    {
        $periodeMonth = Date('n');
        $periodeYear = Date('Y');

        $qAssetSo = DB::table('asset_sos')
                        ->select('id')
                        ->where('month', $periodeMonth)
                        ->where('year', $periodeYear);

        $assetSoId = 0;

        if($qAssetSo->count() > 0){
            // exist
            $assetSo = $qAssetSo->first();
            $assetSoId = $assetSo->id;
        } else {
            // not exist and create
            $assetSo = new AssetSo;
            $assetSo->month = $periodeMonth;
            $assetSo->month_label = Helper::getMonthByNumberMonth($periodeMonth);
            $assetSo->year = $periodeYear;
            if($assetSo->save()){
                $assetSoId = $assetSo->id;
            }
        }

        return $assetSoId;

    }

}
