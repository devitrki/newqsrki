<?php

namespace App\Models\Financeacc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MassClearingGenerate extends Model
{
    use HasFactory;

    public $timestamps = false;

    public static function getNoGenerate($massClearingId){
        $query = DB::table('mass_clearing_generates')
                    ->where('mass_clearing_id', $massClearingId);

        $no = 1;
        if( $query->count() > 0 ){
            $no = $query->max('no') + 1;
        }
        return $no;
    }
}
