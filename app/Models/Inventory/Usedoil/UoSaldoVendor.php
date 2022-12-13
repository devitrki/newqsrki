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

    // report
    public static function getDataReport($companyId)
    {
        $saldoVendors = DB::table('uo_saldo_vendors')
                        ->join('uo_vendors', 'uo_vendors.id', 'uo_saldo_vendors.uo_vendor_id')
                        ->where('uo_vendors.company_id', $companyId)
                        ->select('uo_saldo_vendors.saldo', 'uo_vendors.name');

        return [
            'count' => $saldoVendors->count(),
            'items' => $saldoVendors->get()
        ];
    }
}
