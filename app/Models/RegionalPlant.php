<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RegionalPlant extends Model
{
    use HasFactory;

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public static function getRegionalPlantIdByUserId($user_id)
    {
        $userRegionals = DB::table('user_regionals')->where('user_id', $user_id);
        $id = 0;
        if ($userRegionals->count() > 0) {
            $userRegional = $userRegionals->first();
            $id = $userRegional->regional_plant_id;
        }
        return $id;
    }

    public static function getRegionalPlantNameByUserId($user_id)
    {
        $userRegionals = DB::table('user_regionals')->where('user_id', $user_id);
        $name = '';
        if ($userRegionals->count() > 0) {
            $userRegional = $userRegionals->first();
            $regionalPlant = DB::table('regional_plants')->where('id', $userRegional->regional_plant_id)->first();
            $name = $regionalPlant->name;
        }
        return $name;
    }
}
