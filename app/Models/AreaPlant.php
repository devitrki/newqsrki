<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AreaPlant extends Model
{
    use HasFactory;

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public static function getAreaPlantIdByUserId($user_id)
    {
        $userAreas = DB::table('user_areas')->where('user_id', $user_id);
        $id = 0;
        if ($userAreas->count() > 0) {
            $userArea = $userAreas->first();
            $id = $userArea->area_plant_id;
        }
        return $id;
    }

    public static function getAreaPlantNameByUserId($user_id)
    {
        $userAreas = DB::table('user_areas')->where('user_id', $user_id);
        $name = '';
        if ($userAreas->count() > 0) {
            $userArea = $userAreas->first();
            $areaPlant = DB::table('area_plants')->where('id', $userArea->area_plant_id)->first();
            $name = $areaPlant->name;
        }
        return $name;
    }
}
