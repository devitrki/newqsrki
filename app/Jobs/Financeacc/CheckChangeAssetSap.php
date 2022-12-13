<?php

namespace App\Jobs\Financeacc;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Log;

use App\Services\AssetServiceAppsImpl;
use App\Services\AssetServiceSapImpl;

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
            $assetService = new AssetServiceSapImpl();
            $assetService->checkChangeAsset(
                $this->id,
                $this->number_asset,
                $this->sub_number_asset,
                $this->from_plant_id,
                $this->from_cost_center_code,
                $this->to_plant_id,
                $this->to_cost_center_code
            );

        } catch (\Throwable $th) {
            Log::error('Check Change SAP Asset :' . $this->number_asset . '-' . $this->sub_number_asset .
                        ' from ' . $this->from_plant_id . ' to ' . $this->to_plant_id . ' Error : ' . $th->getMessage());
        }

    }
}
