<?php

namespace App\Models\Inventory\Usedoil;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\DB;

class UoSaldoVendor extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getSaldoVendor($id){
        $query = DB::table('uo_saldo_vendors')
                    ->where('uo_vendor_id', $id)
                    ->select('saldo');

        $saldo = 0;

        if( $query->count() > 0 ){
            $data = $query->first();
            $saldo = $data->saldo;
        }

        return $saldo;
    }
}
