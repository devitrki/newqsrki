<?php

namespace App\Jobs\Financeacc;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Lang;

use App\Mail\Financeacc\Assets\NotificationSelisihAssetSo;

use App\Models\Configuration;
use App\Models\Plant;
use App\Models\Financeacc\AssetSo;

class SubmitAssetSO implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $companyId;
    protected $typePlant;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($companyId, $typePlant)
    {
        $this->companyId = $companyId;
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
            $companyId = $this->companyId;
            $typePlant = $this->typePlant;

            // get asset so id this periode
            $assetSoId = $this->getAssetSoIdSubmit($companyId);

            if ($assetSoId != 0) {

                $assetSo = DB::table('asset_sos')
                            ->where('company_id', $companyId)
                            ->where('id', $assetSoId)
                            ->first();

                $sendSelisihDepartAsset = true;
                $periode = $assetSo->month_label . ' ' . $assetSo->year;

                if ($typePlant != 'dc') {

                    // log run
                    Log::info('Submit asset so outlet periode ' . date('m-Y') . ' running');

                    // check status submit outlet
                    if($assetSo->status_submit_outlet != 1){
                        // update status submit outlet
                        $uAssetSo = AssetSo::find($assetSoId);
                        $uAssetSo->status_submit_outlet = 1;
                        $uAssetSo->save();
                    }

                    // update status so asset outlet false
                    $statusAssetSoOutlet = Configuration::getValueCompByKeyFor($companyId, 'financeacc', 'status_outlet_asset_so');
                    if ($statusAssetSoOutlet != 'Not Running') {
                        Configuration::setValueCompByKeyFor($companyId, 'financeacc', 'status_outlet_asset_so', 'Not Running');
                    }

                    // check send selisih to depart asset
                    if ($assetSo->send_depart_asset_outlet == '0') {
                        $sendSelisihDepartAsset = false;
                    }

                    // get data sended am outlet
                    $listSendAm = json_decode($assetSo->send_am_outlet);

                    // subject email
                    $subject = Lang::get('Result Stock Opname Asset Outlet Periode ') . ' ' . $periode;

                } else {

                    // log run
                    Log::info('Submit asset so dc periode ' . date('m-Y') . ' running');

                    // check status submit dc
                    if ($assetSo->status_submit_dc != 1) {
                        // update status submit dc
                        $uAssetSo = AssetSo::find($assetSoId);
                        $uAssetSo->status_submit_dc = 1;
                        $uAssetSo->save();
                    }

                    // update status so asset dc false
                    $statusAssetSoDC = Configuration::getValueCompByKeyFor($companyId, 'financeacc', 'status_dc_asset_so');
                    if ($statusAssetSoDC != 'Not Running') {
                        Configuration::setValueCompByKeyFor($companyId, 'financeacc', 'status_dc_asset_so', 'Not Running');
                    }

                    // check send selisih to depart asset
                    if ($assetSo->send_depart_asset_dc == '0') {
                        $sendSelisihDepartAsset = false;
                    }

                    // get data sended am dc
                    $listSendAm = json_decode($assetSo->send_am_dc);

                    // subject email
                    $subject = Lang::get('Result Stock Opname Asset DC Periode ') . ' ' . $periode;
                }

                // send email selisih to depart asset if false
                if (!$sendSelisihDepartAsset) {

                    if( $this->checkPlantAMHaveSelisih($assetSo->id, $typePlant, 0) ){
                        // create file excel selisih
                        $fileSelisihAssetSo = AssetSo::GenerateSelisihSoExcel($assetSo->id, $typePlant, 0);

                        // send selisih to depart asset
                        $emailDepartAsset = Configuration::getValueCompByKeyFor($companyId, 'financeacc', 'email_depart_asset');
                        $to = explode(',', $emailDepartAsset);
                        $cc = [];

                        try {
                            Mail::send(new NotificationSelisihAssetSo($periode, $subject, $to, $cc, $fileSelisihAssetSo, 'Depart Asset'));
                        } catch (\Throwable $th) {
                            Log::alert('Send mail selisih asset to depart asset error : ' . $th->getMessage());
                        }
                    }

                    // update send depart asset complete
                    $update = [];

                    if ($typePlant != 'dc') {
                        $update = [
                            'send_depart_asset_outlet' => 1
                        ];
                    } else {
                        $update = [
                            'send_depart_asset_dc' => 1
                        ];
                    }

                    DB::table('asset_sos')
                        ->where('id', $assetSoId)
                        ->update($update);
                }

                // check already or not send selisih to am
                if (!$listSendAm) {
                    $listSendAm = [];
                }

                $qAmNotYetSends = DB::table('mapping_area_plants')
                                    ->join('plants', 'plants.id', 'mapping_area_plants.plant_id')
                                    ->whereNotIn('mapping_area_plants.area_plant_id', $listSendAm)
                                    ->distinct();

                if ($typePlant != 'dc') {
                    $qAmNotYetSends = $qAmNotYetSends->where('plants.type', 1);
                } else {
                    $qAmNotYetSends = $qAmNotYetSends->where('plants.type', 2);
                }

                if ($qAmNotYetSends->count() > 0) {

                    // have area not yet send
                    $amNotYetSends = $qAmNotYetSends
                                        ->limit('10')
                                        ->pluck('mapping_area_plants.area_plant_id');

                    foreach ($amNotYetSends as $amNotYetSend) {

                        if( !$this->checkPlantAMHaveSelisih($assetSo->id, $typePlant, $amNotYetSend) ){
                            continue;
                        }

                        // check am have email or not
                        $qUserAM = DB::table('user_areas')
                                    ->join('users', 'users.id', 'user_areas.user_id')
                                    ->join('area_plants', 'area_plants.id', 'user_areas.area_plant_id')
                                    ->where('user_areas.area_plant_id', $amNotYetSend)
                                    ->select('users.email', 'user_areas.area_plant_id', 'area_plants.name');

                        if ($qUserAM->count() > 0) {

                            // am have email to send file selisih
                            $userAm = $qUserAM->first();

                            // create file excel selisih
                            $fileSelisihAssetSo = AssetSo::GenerateSelisihSoExcel($assetSo->id, $typePlant, $amNotYetSend);

                            Log::info($fileSelisihAssetSo);

                            // send selisih to depart asset
                            $to = [$userAm->email];
                            $cc = [];

                            // get email rm by am
                            $qRmUserAm = DB::table('user_regionals')
                                            ->join('mapping_regional_areas', 'mapping_regional_areas.regional_plant_id', 'user_regionals.regional_plant_id')
                                            ->join('users', 'users.id', 'user_regionals.user_id')
                                            ->where('mapping_regional_areas.area_plant_id', $amNotYetSend)
                                            ->select('users.email');

                            if($qRmUserAm->count()){
                                $rmUserAm = $qRmUserAm->first();
                                $cc[] = $rmUserAm->email;
                            }


                            if ($typePlant != 'dc') {
                                $dearAM = 'Area Manager';
                            } else {
                                $dearAM = 'Supervisor';
                            }

                            try {
                                Mail::send(new NotificationSelisihAssetSo($periode, $subject, $to, $cc, $fileSelisihAssetSo, $dearAM . ' ' . $userAm->name));
                            } catch (\Throwable $th) {
                                Log::alert('Send mail selisih asset to am asset error : ' . $th->getMessage());
                            }

                        }

                    }

                    // update am sended file selisih
                    $sendAmOutletSended = array_merge($listSendAm, $amNotYetSends->toArray());
                    $update = [];

                    if ($typePlant != 'dc') {
                        $update = [
                            'send_am_outlet' => json_encode($sendAmOutletSended)
                        ];
                    } else {
                        $update = [
                            'send_am_dc' => json_encode($sendAmOutletSended)
                        ];
                    }

                    DB::table('asset_sos')
                        ->where('id', $assetSoId)
                        ->update($update);

                }

            } else {

                if ($typePlant != 'dc') {
                    Log::alert('Manual submmit asset so outlet failed : asset not yet created');
                } else {
                    Log::alert('Manual submmit asset so dc failed : asset not yet created');
                }
            }
        } catch (\Throwable $th) {
            Log::alert('Submit asset so failed : ' . $th->getMessage());
        }
    }

    public function getAssetSoIdSubmit($companyId)
    {
        $periodeMonth = Date('n');
        $periodeYear = Date('Y');

        $qAssetSo = DB::table('asset_sos')
                        ->select('id')
                        ->where('company_id', $companyId)
                        ->where('month', $periodeMonth)
                        ->where('year', $periodeYear);

        $assetSoId = 0;

        if($qAssetSo->count() > 0){
            // exist
            $assetSo = $qAssetSo->first();
            $assetSoId = $assetSo->id;
        }

        return $assetSoId;

    }

    public function checkPlantAMHaveSelisih($assetSoId, $typePlant, $purposeToId){
        $qAssetSoPlants = DB::table('asset_so_plants')
                            ->join('asset_so_details', 'asset_so_details.asset_so_plant_id', 'asset_so_plants.id')
                            ->join('plants', 'plants.id', 'asset_so_plants.plant_id')
                            ->where('asset_so_plants.asset_so_id', $assetSoId)
                            ->where('qty_selisih', '<>', '0')
                            ->select('asset_so_plants.id', 'plants.short_name', 'asset_so_plants.cost_center')
                            ->groupBy('asset_so_plants.id', 'plants.short_name', 'asset_so_plants.cost_center');

        if ($typePlant != 'dc') {
            // outlet
            $qAssetSoPlants = $qAssetSoPlants->where('plants.type', 1);
        } else {
            // dc
            $qAssetSoPlants = $qAssetSoPlants->where('plants.type', 2);
        }

        if ($purposeToId != '0') {
            // am
            $plantIdAm = DB::table('mapping_area_plants')
                            ->where('area_plant_id', $purposeToId)
                            ->pluck('plant_id');

            $qAssetSoPlants = $qAssetSoPlants->whereIn('asset_so_plants.plant_id', $plantIdAm);
        }

        $countAssetSoPlants = $qAssetSoPlants->count();

        if( $countAssetSoPlants > 0 ){
            return true;
        } else {
            return false;
        }
    }
}
