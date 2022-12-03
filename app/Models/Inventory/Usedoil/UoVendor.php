<?php

namespace App\Models\Inventory\Usedoil;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\DB;

class UoVendor extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getSaldoVendor($id){
        $qSaldoVendor = DB::table('uo_saldo_vendors')
                            ->where('uo_vendor_id', $id)
                            ->select('saldo');

        $saldo = 0;
        if ( $qSaldoVendor->count() > 0 ) {
            $saldoVendor = $qSaldoVendor->first();
            $saldo = $saldoVendor->saldo;
        }

        return $saldo;
    }

    public static function updateSaldoVendor($id, $nominal){
        $qSaldoVendor = DB::table('uo_saldo_vendors')
                            ->where('uo_vendor_id', $id);
        $saldo = 0;
        if( $qSaldoVendor->count() > 0 ){
            $uoSaldoVendor  = $qSaldoVendor->first();
            $saldo = $uoSaldoVendor->saldo + $nominal;
            $updateSaldo = DB::table('uo_saldo_vendors')
                            ->where('uo_vendor_id', $id)
                            ->update(['saldo' => $saldo]);
        } else {
            $saldo = $nominal;
            $uoSaldoVendor = new UoSaldoVendor;
            $uoSaldoVendor->uo_vendor_id = $id;
            $uoSaldoVendor->saldo = $saldo;
            $uoSaldoVendor->save();
        }

        return $saldo;
    }

    public static function getFirstVendorIdSelect($companyId)
    {
        $vendor = DB::table('uo_vendors')
                    ->where('company_id', $companyId)
                    ->select('id')
                    ->orderBy('name')
                    ->first();

        return $vendor->id;
    }

    public static function getNameVendorById($id)
    {
        $qVendor = DB::table('uo_vendors')
                    ->select('name')
                    ->where('id', $id);
        $name = '';
        if($qVendor->count() > 0){
            $vendor = $qVendor->first();
            $name = $vendor->name;
        }
        return $name;
    }
}
