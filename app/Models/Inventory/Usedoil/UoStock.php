<?php

namespace App\Models\Inventory\Usedoil;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;

use App\Models\Plant;

class UoStock extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public $timestamps = false;

    public static function getStockCurrent($companyId, $plantId, $materialCode){
        $qStock = DB::table('uo_stocks')
                    ->where('company_id', $companyId)
                    ->where('plant_id', $plantId)
                    ->where('material_code', $materialCode);

        $stockCurrent = 0;

        if( $qStock->count() > 0 ){
            $stock = $qStock->first();
            $stockCurrent = $stock->stock;
        } else {
            $updateStock = DB::table('uo_stocks')->insert(
                [
                    'company_id' => $companyId,
                    'plant_id' => $plantId,
                    'material_code' => $materialCode,
                    'stock' => $stockCurrent,
                ]
            );
        }

        return $stockCurrent;
    }

    public static function updateStock($companyId, $plantId, $materialCode, $qty){
        $qStock = DB::table('uo_stocks')
                    ->where('company_id', $companyId)
                    ->where('plant_id', $plantId)
                    ->where('material_code', $materialCode);

        if( $qStock->count() > 0 ){
            $stock = $qStock->first();
            $newStock = round($stock->stock + $qty, 2);
            $updateStock = DB::table('uo_stocks')
                            ->where('company_id', $companyId)
                            ->where('plant_id', $plantId)
                            ->where('material_code', $materialCode)
                            ->update(['stock' => $newStock]);

        } else {
            $updateStock = DB::table('uo_stocks')->insert(
                [
                    'company_id' => $companyId,
                    'plant_id' => $plantId,
                    'material_code' => $materialCode,
                    'stock' => $qty,
                ]
            );
        }

        return $updateStock;
    }

    // report
    public static function getDataReport($companyId, $plantId)
    {
        $stocks = DB::table('uo_stocks')
                        ->join('uo_materials', 'uo_materials.code', 'uo_stocks.material_code')
                        ->where('uo_stocks.company_id', $companyId)
                        ->where('uo_stocks.plant_id', $plantId)
                        ->select('uo_materials.code', 'uo_materials.name', 'uo_materials.uom', 'uo_stocks.stock');

        $header = [
            'plant' => Plant::getCodeById($plantId) . ' - ' . Plant::getShortNameById($plantId),
        ];

        return [
            'count' => $stocks->count(),
            'header' => $header,
            'items' => $stocks->get()
        ];
    }

}
