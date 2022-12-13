<?php

namespace App\Models\Financeacc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Asset extends Model
{
    use HasFactory;

    public static function getCostCenterCodeByPlantBy($plant_id)
    {
        $plant = DB::table('assets')
                    ->where('plant_id', $plant_id)
                    ->select('cost_center_code');

        $cc_code = '';
        if($plant->count() > 0){
            $plant_c = $plant->first();
            $cc_code = $plant_c->cost_center_code;
        }
        return $cc_code;
    }
}
