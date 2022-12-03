<?php

namespace App\Exports\Financeacc;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use App\Library\Helper;

class SelisihAssetSo implements fromView, ShouldAutoSize
{
    protected $assetSoId;
    protected $plant;
    protected $send;
    protected $sendID;

    function __construct($assetSoId, $plant, $send, $sendID)
    {
        $this->assetSoId = $assetSoId;
        $this->plant = $plant;
        $this->send = $send;
        $this->sendID = $sendID;
    }

    public function view(): View
    {

        $assetSo = DB::table('asset_sos')
                    ->where('id', $this->assetSoId)
                    ->first();

        $periodeMonthSoLabel = Helper::getMonthByNumberMonth($assetSo->month);

        $qSelisihAssetSo = DB::table('asset_so_details')
                            ->join('asset_so_plants', 'asset_so_plants.id', 'asset_so_details.asset_so_plant_id')
                            ->join('plants', 'plants.id', 'asset_so_plants.plant_id')
                            ->select('asset_so_details.*', 'asset_so_plants.cost_center', 'asset_so_plants.cost_center_code','plants.initital', 'plants.short_name')
                            ->where('qty_selisih', '<>', 0)
                            ->where('asset_so_id', $this->assetSoId);

        if ($this->plant != 'dc') {
            $qSelisihAssetSo = $qSelisihAssetSo->where('plants.type', 1);
        } else {
            $qSelisihAssetSo = $qSelisihAssetSo->where('plants.type', 2);
        }

        if($this->send != 'asset'){
            // am
            $plantIdAm = DB::table('mapping_area_plants')
                            ->where('area_plant_id', $this->sendID)
                            ->pluck('plant_id');

            $qSelisihAssetSo = $qSelisihAssetSo->whereIn('asset_so_plants.plant_id', $plantIdAm);
        }

        $selisihAssetSo = $qSelisihAssetSo->get();

        $report_data = [
            'assetSo' => [
                'head' => $assetSo,
                'label' => $periodeMonthSoLabel
            ],
            'data' => $selisihAssetSo
        ];

        return view('financeacc.excel.selisih-asset-so-export-excel', $report_data);
    }
}
