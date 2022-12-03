<?php

namespace App\Jobs\Financeacc;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Models\Plant;
use App\Models\Financeacc\AssetMutation;

class CheckChangeAssetSap implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $id;
    protected $number_asset;
    protected $sub_number_asset;
    protected $from_plant_id;
    protected $from_cost_center_code;
    protected $to_plant_id;
    protected $to_cost_center_code;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id, $number_asset, $sub_number_asset, $from_plant_id, $from_cost_center_code, $to_plant_id, $to_cost_center_code)
    {
        $this->id = $id;
        $this->number_asset = $number_asset;
        $this->sub_number_asset = $sub_number_asset;
        $this->from_plant_id = $from_plant_id;
        $this->from_cost_center_code = $from_cost_center_code;
        $this->to_plant_id = $to_plant_id;
        $this->to_cost_center_code = $to_cost_center_code;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        try {

            DB::beginTransaction();

            $alreadyInTo = false; // flag for asset receive to plant receive
            $goneInFrom = true; // flag for asset gone from plant sender

            $fromPlantCode = Plant::getCodeById($this->from_plant_id);
            $toPlantCode = Plant::getCodeById($this->to_plant_id);

            $urlSync = config('qsrki.api.apps.url') . 'recheese/daily-sales/sap/asset/list';

            // sync plant to / plant receive
            $responseSyncTo = Http::get($urlSync, [
                'plant' => Plant::getCodeById($this->to_plant_id)
            ]);

            if ($responseSyncTo->ok()) {

                $assetSapTos = $responseSyncTo->json();

                $inserts = [];

                // delete asset exist plant to
                DB::table('assets')->where('plant_id', $this->to_plant_id)->delete();

                // setup insert all data from sap to plant to
                foreach ($assetSapTos as $assetSapTo) {

                    // check asset have already in plant receive
                    if ($this->number_asset == $assetSapTo['asset_number'] && $this->sub_number_asset == $assetSapTo['sub_number'] && $this->to_cost_center_code == $assetSapTo['cc_code'] &&  $toPlantCode == $assetSapTo['plant']) {
                        $alreadyInTo = true;
                    }

                    // accommodate all data to var insert
                    $inserts[] = [
                        'number' => $assetSapTo['asset_number'],
                        'number_sub' => $assetSapTo['sub_number'],
                        'plant_id' => $this->to_plant_id,
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
                $success = DB::table('assets')->insert($inserts);
                if ($success) {

                    // sync plant from / plant sender
                    $responseSyncFrom = Http::get($urlSync, [
                        'plant' => Plant::getCodeById($this->from_plant_id)
                    ]);

                    if ($responseSyncFrom->ok()) {

                        $assetSapFroms = $responseSyncFrom->json();
                        $inserts = [];

                        // delete asset exist plant from / plant sender
                        DB::table('assets')->where('plant_id', $this->from_plant_id)->delete();

                        // setup insert all data from sap
                        foreach ($assetSapFroms as $assetSapFrom) {

                            // check asset already gone from plant from / plant sender or not
                            if ($this->number_asset == $assetSapFrom['asset_number'] && $this->sub_number_asset == $assetSapFrom['sub_number'] && $this->from_cost_center_code == $assetSapFrom['cc_code'] && $fromPlantCode == $assetSapFrom['plant']) {
                                $goneInFrom = false;
                            }

                            // accommodate all asset sap to var insert
                            $inserts[] = [
                                'number' => $assetSapFrom['asset_number'],
                                'number_sub' => $assetSapFrom['sub_number'],
                                'plant_id' => $this->from_plant_id,
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
                        $success = DB::table('assets')->insert($inserts);

                        if ($success) {
                            DB::commit();

                            $assetMutation = AssetMutation::find($this->id);

                            if ($goneInFrom && $alreadyInTo) {

                                $assetMutation->status_changed = 1;

                            } else {

                                $assetMutation->status_changed = 0;

                            }

                            $assetMutation->save();

                        } else {
                            DB::rollback();
                            Log::error('Check Change SAP Asset :' . $this->number_asset . '-' . $this->sub_number_asset .
                            ' from ' . $this->from_plant_id . ' to ' . $this->to_plant_id . ' Error : failed to insert data asset plant from');
                        }

                    } else {
                        DB::rollback();
                        Log::error('Check Change SAP Asset :' . $this->number_asset . '-' . $this->sub_number_asset .
                        ' from ' . $this->from_plant_id . ' to ' . $this->to_plant_id . ' Error : failed to get data plant from, from sap');
                    }


                } else {
                    DB::rollback();
                    Log::error('Check Change SAP Asset :' . $this->number_asset . '-' . $this->sub_number_asset .
                    ' from ' . $this->from_plant_id . ' to ' . $this->to_plant_id . ' Error : failed to insert data asset plant to');
                }

            } else {
                DB::rollback();
                Log::error('Check Change SAP Asset :' . $this->number_asset . '-' . $this->sub_number_asset .
                    ' from ' . $this->from_plant_id . ' to ' . $this->to_plant_id . ' Error : failed to get data plant to from sap');
            }

        } catch (\Throwable $th) {
            Log::error('Check Change SAP Asset :' . $this->number_asset . '-' . $this->sub_number_asset .
                        ' from ' . $this->from_plant_id . ' to ' . $this->to_plant_id . ' Error : ' . $th->getMessage());
        }

    }
}
