<?php

namespace App\Exports\Financeacc;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

use App\Library\Helper;

use App\Models\Plant;

class AssetSoExport implements fromView, ShouldAutoSize
{
    protected $assetSoPlantId;

    function __construct($assetSoPlantId)
    {
        $this->assetSoPlantId = $assetSoPlantId;
    }

    public function view(): View
    {

        $assetSoPlant = DB::table('asset_so_plants')
                            ->join('asset_sos', 'asset_sos.id', 'asset_so_plants.asset_so_id')
                            ->where('asset_so_plants.id', $this->assetSoPlantId)
                            ->select('asset_so_plants.*', 'asset_sos.month', 'asset_sos.year')
                            ->first();

        $detailAssetSoPlant = DB::table('asset_so_details')
                                ->where('asset_so_plant_id', $assetSoPlant->id)
                                ->get();

        $periodeMonthSoLabel = Helper::getMonthByNumberMonth($assetSoPlant->month);

        $plantCode = Plant::getCodeById($assetSoPlant->plant_id);
        $plantName = Plant::getShortNameById($assetSoPlant->plant_id);
        $plantType = Plant::getTypeByPlantId($assetSoPlant->plant_id);

        $data = [
            'assetSoPlant' => [
                'head' => $assetSoPlant,
                'detail' => $detailAssetSoPlant,
                'label' => $periodeMonthSoLabel
            ],
            'plant' => [
                'code' => $plantCode,
                'name' => $plantName,
                'type' => $plantType
            ]
        ];

        return view('financeacc.excel.asset-so-export-excel', $data);
    }
}
