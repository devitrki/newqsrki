<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\DB;

class SpecialGl extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getIdbySpecialGl($specialGl){
        $query = DB::table('special_gls')
                    ->select('id')
                    ->where('special_gl', $specialGl);
        $id = 0;
        if( $query->count() > 0 ){
            $data = $query->first();
            $id = $data->id;
        }
        return $id;
    }

    public static function getSapCodebySpecialGl($specialGl){
        $query = DB::table('special_gls')
                    ->select('sap_code')
                    ->where('special_gl', $specialGl);
        $sapCode = '';
        if( $query->count() > 0 ){
            $data = $query->first();
            $sapCode = $data->sap_code;
        }
        return $sapCode;
    }

    public static function getRefbySpecialGl($specialGl){
        $query = DB::table('special_gls')
                    ->select('reference')
                    ->where('special_gl', $specialGl);
        $reference = '';
        if( $query->count() > 0 ){
            $data = $query->first();
            $reference = $data->reference;
        }
        return $reference;
    }
}
