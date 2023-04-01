<?php

namespace App\Models\Logbook;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\DB;

class LbAppReview extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getFullDataById($id)
    {
        $query = DB::table('lb_app_reviews')
                        ->leftJoin('plants', 'plants.id', '=', 'lb_app_reviews.plant_id')
                        ->where('lb_app_reviews.id', $id)
                        ->select(['lb_app_reviews.id', 'lb_app_reviews.date', 'lb_app_reviews.mod_approval',
                          'lb_app_reviews.mod_pic', DB::raw("CONCAT(plants.initital ,' ', plants.short_name) AS outlet"),
                          'lb_app_reviews.plant_id', 'lb_app_reviews.company_id']);

        $result = null;
        if($query->count() > 0){
            $result = $query->first();
        }
        return $result;
    }
}
