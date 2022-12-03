<?php

namespace App\Exports\Financeacc;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use App\Models\Plant;

class SelisihAssetSoSheet implements FromView, ShouldAutoSize, WithTitle
{
    protected $sheetName;
    protected $assetSoPlantId;

    function __construct($sheetName, $assetSoPlantId)
    {
        $this->sheetName = $sheetName;
        $this->assetSoPlantId = $assetSoPlantId;
    }

    public function view(): View
    {
        $assetSoPlantId = DB::table('asset_so_plants')
                            ->join('asset_sos', 'asset_sos.id', 'asset_so_plants.asset_so_id')
                            ->where('asset_so_plants.id', $this->assetSoPlantId)
                            ->select('asset_sos.month_label', 'asset_sos.year', 'asset_so_plants.plant_id','asset_so_plants.cost_center',
                                    'asset_so_plants.cost_center_code', 'asset_so_plants.note')
                            ->first();

        $periode = $assetSoPlantId->month_label . ' ' . $assetSoPlantId->year;
        $plant = Plant::getCodeById($assetSoPlantId->plant_id) . ' - ' . Plant::getShortNameById($assetSoPlantId->plant_id);

        $selisihAssetSo = DB::table('asset_so_details')
                            ->where('qty_selisih', '<>', 0)
                            ->where('asset_so_plant_id', $this->assetSoPlantId)
                            ->get();

        $report_data = [
            'periode' => $periode,
            'plant' => $plant,
            'costcenter' => $assetSoPlantId->cost_center_code. ' - ' . $assetSoPlantId->cost_center,
            'note' => $assetSoPlantId->note,
            'data' => $selisihAssetSo
        ];

        return view('financeacc.excel.selisih-asset-so-export-excel', $report_data);
    }

    public function title(): string
    {
        return $this->sheetName;
    }
}
