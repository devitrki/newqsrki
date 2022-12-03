<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Library\Helper;
use App\Models\Plant;
use App\Models\Configuration;

class GrVendor extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getOutstandingSapByPlantId($plant_id)
    {
        $plantCode = Plant::getCodeById($plant_id);
        $response = Http::get(config('qsrki.api.apps.url') . 'recheese/daily-sales/sap/grvendor/outstanding?plant=' . $plantCode);
        $outstanding = [];

        if ($response->ok()) {
            $outstanding_sap = $response->json();
            foreach ($outstanding_sap as $v) {
                $poDate = Helper::DateConvertFormat($v['EINDT'], 'Ymd', 'Y-m-d');
                $curDate = Date('Y-m-d');
                $diffDays = Helper::DateDifference($poDate, $curDate);
                $vendor_id = round($v['LIFNR']) . '';
                $vendor_allows_day = Configuration::getValueByKeyFor('inventory', 'vendor_allow');
                $vendor_allows_days = explode(',', str_replace(' ', '', $vendor_allows_day) );
                $qty_remaining_po = round($v['MENGE'],3) - round($v['WEMNG'], 3);
                if( ($diffDays > 130 && !in_array( $vendor_id , $vendor_allows_days )) ||  $qty_remaining_po <= 0){
                    continue;
                }

                if( is_numeric($v['MATNR'])){
                    $matCode = $v['MATNR'] + 0;
                } else {
                    $matCode = $v['MATNR'];
                }

                $outstanding[] = [
                    'mandt' => $v['MANDT'],
                    'doc_number' => round($v['EBELN']),
                    'vendor_id' => $vendor_id,
                    'vendor_name' => $v['NAME1'],
                    'item_number' => $v['EBELP'],
                    'material_code' => $matCode . "",
                    'material_desc' => $v['TXZ01'],
                    'po_date' => Helper::DateConvertFormat($v['EINDT'], 'Ymd', 'd-m-Y'),
                    'uom' => $v['MEINS'],
                    'qty_po' => round($v['MENGE'], 3),
                    'qty_remaining_po' => $qty_remaining_po,
                    'elikz' => $v['ELIKZ'],
                    'plant_id' => $plant_id,

                ];
            }
        }
        return $outstanding;
    }
}
