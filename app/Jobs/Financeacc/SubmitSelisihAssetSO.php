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

use App\Mail\Financeacc\Assets\NotificationSelisihAssetSo;

use App\Models\Configuration;
use App\Models\Plant;
use App\Models\Financeacc\AssetSo;

class SubmitSelisihAssetSO implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $plant;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($plant)
    {
        $this->plant = $plant;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {

            // get asset so id this periode
            $assetSoId = $this->getAssetSoId();

            if ($assetSoId != 0) {

                $assetSo = DB::table('asset_sos')
                            ->where('id', $assetSoId)
                            ->first();

                $sendSelisihDepartAsset = true;

                if ($this->plant != 'dc') {

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
                    $statusAssetSoOutlet = Configuration::getValueByKeyFor('financeacc', 'status_outlet_asset_so');
                    if ($statusAssetSoOutlet != 'Not Running') {
                        Configuration::setValueByKeyFor('financeacc', 'status_outlet_asset_so', 'Not Running');
                    }

                    // check send selisih to depart asset
                    if ($assetSo->send_depart_asset_outlet == '0') {
                        $sendSelisihDepartAsset = false;
                    }

                    // get data sended am outlet
                    $listSendAm = json_decode($assetSo->send_am_outlet);

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
                    $statusAssetSoDC = Configuration::getValueByKeyFor('financeacc', 'status_dc_asset_so');
                    if ($statusAssetSoDC != 'Not Running') {
                        Configuration::setValueByKeyFor('financeacc', 'status_dc_asset_so', 'Not Running');
                    }

                    // check send selisih to depart asset
                    if ($assetSo->send_depart_asset_dc == '0') {
                        $sendSelisihDepartAsset = false;
                    }

                    // get data sended am dc
                    $listSendAm = json_decode($assetSo->send_am_dc);
                }

                // send email selisih to depart asset if false
                if (!$sendSelisihDepartAsset) {

                    // create file excel selisih
                    $fileSelisihAssetSo = AssetSo::GenerateSelisihSoExcel($assetSo->id, $this->plant, 'asset', []);

                    // send selisih to depart asset
                    $emailDepartAsset = Configuration::getValueByKeyFor('financeacc', 'email_depart_asset');
                    $to = explode(',', $emailDepartAsset);
                    Mail::send(new NotificationSelisihAssetSo($assetSo->id, $this->plant, $to, $fileSelisihAssetSo));

                    // update send depart asset complete
                    $update = [];

                    if ($this->plant != 'dc') {
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

                if ($this->plant != 'dc') {
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

                        // check am have email or not
                        $qUserAM = DB::table('user_areas')
                                    ->join('users', 'users.id', 'user_areas.user_id')
                                    ->where('user_areas.area_plant_id', $amNotYetSend)
                                    ->select('users.email');

                        if ($qUserAM->count() > 0) {

                            // am have email to send file selisih
                            $userAm = $qUserAM->first();

                            // create file excel selisih
                            $fileSelisihAssetSo = AssetSo::GenerateSelisihSoExcel($assetSo->id, $this->plant, 'am', $amNotYetSend);

                            // send selisih to depart asset
                            $to = [$userAm->email];

                            Mail::send(new NotificationSelisihAssetSo($assetSo->id, $this->plant, $to, $fileSelisihAssetSo));
                        }

                    }

                    // update am sended file selisih
                    $sendAmOutletSended = array_merge($listSendAm, $amNotYetSends->toArray());
                    $update = [];

                    if ($this->plant != 'dc') {
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

                if ($this->plant != 'dc') {
                    Log::alert('Submmit asset so outlet failed : asset not yet created');
                } else {
                    Log::alert('Submmit asset so dc failed : asset not yet created');
                }

            }
        } catch (\Throwable $th) {
            Log::alert('Submmit asset so failed : ' . $th->getMessage());
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
        }

        return $assetSoId;

    }
}
