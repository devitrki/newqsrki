<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\DB;

class BankGl extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getIdbyGl($gl){
        $query = DB::table('bank_gls')
                    ->select('id')
                    ->where('bank_gl', $gl);
        $id = 0;
        if( $query->count() > 0 ){
            $data = $query->first();
            $id = $data->id;
        }
        return $id;
    }
}
