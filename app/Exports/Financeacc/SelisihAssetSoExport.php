<?php

namespace App\Exports\Financeacc;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\DB;

class SelisihAssetSoExport implements WithMultipleSheets
{
    use Exportable;

    protected $assetSoId;
    protected $typePlant;
    protected $purposeToId;

    public function __construct($assetSoId, $typePlant, $purposeToId)
    {
        $this->assetSoId = $assetSoId;
        $this->typePlant = $typePlant;
        // 0 = depart asset, else = am
        $this->purposeToId = $purposeToId;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $qAssetSoPlants = DB::table('asset_so_plants')
                            ->join('asset_so_details', 'asset_so_details.asset_so_plant_id', 'asset_so_plants.id')
                            ->join('plants', 'plants.id', 'asset_so_plants.plant_id')
                            ->where('asset_so_plants.asset_so_id', $this->assetSoId)
                            ->where('qty_selisih', '<>', '0')
                            ->select('asset_so_plants.id', 'plants.short_name', 'asset_so_plants.cost_center', 'plants.code')
                            ->groupBy('asset_so_plants.id', 'plants.short_name', 'asset_so_plants.cost_center', 'plants.code');

        if ($this->typePlant != 'dc') {
            // outlet
            $qAssetSoPlants = $qAssetSoPlants->where('plants.type', 1);
        } else {
            // dc
            $qAssetSoPlants = $qAssetSoPlants->where('plants.type', 2);
        }

        if ($this->purposeToId != '0') {
            // am
            $plantIdAm = DB::table('mapping_area_plants')
                            ->where('area_plant_id', $this->purposeToId)
                            ->pluck('plant_id');

            $qAssetSoPlants = $qAssetSoPlants->whereIn('asset_so_plants.plant_id', $plantIdAm);
        }

        $assetSoPlants = $qAssetSoPlants->get();

        foreach ($assetSoPlants as $assetSoPlant) {
            $nameSheet = $assetSoPlant->code . '-' . $assetSoPlant->cost_center;
            $sheets[] = new SelisihAssetSoSheet($nameSheet, $assetSoPlant->id);
        }

        return $sheets;
    }
}
