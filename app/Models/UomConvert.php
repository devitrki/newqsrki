<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UomConvert extends Model
{
    use HasFactory;

    public static function getSendSapUom($companyId, $baseUom){
        $uomConvert = DB::table('uom_converts')
                    ->where('company_id', $companyId)
                    ->where('base_uom', $baseUom)
                    ->select('send_sap_uom')
                    ->first();

        if ($uomConvert) {
            return $uomConvert->send_sap_uom;
        }

        return $baseUom;
    }
}
