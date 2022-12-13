<?php

namespace App\Models\Financeacc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\DB;

class AssetAdminDepart extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getHODDepart($plantId, $costCenterCode){
        $user_id = '0';

        $adminDepart = DB::table('asset_admin_departs')
                        ->join('users', 'users.id', 'asset_admin_departs.hod_id')
                        ->where('asset_admin_departs.plant_id', $plantId)
                        ->where('asset_admin_departs.cost_center_code', $costCenterCode)
                        ->select('users.id');

        if($adminDepart->count() > 0){
            $data = $adminDepart->first();
            $user_id = $data->id;
        }

        return $user_id;
    }

    public static function getAdminDepart($plantId, $costCenterCode){
        $user_id = '0';

        $adminDepart = DB::table('asset_admin_departs')
                        ->join('users', 'users.id', 'asset_admin_departs.admin_depart_id')
                        ->where('asset_admin_departs.plant_id', $plantId)
                        ->where('asset_admin_departs.cost_center_code', $costCenterCode)
                        ->select('users.id');

        if($adminDepart->count() > 0){
            $data = $adminDepart->first();
            $user_id = $data->id;
        }

        return $user_id;
    }
}
