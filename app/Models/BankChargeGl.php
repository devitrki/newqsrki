<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\DB;

class BankChargeGl extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getGlAccountCharge($specialGl, $bankGl){
        $query = DB::table('bank_charge_gls')
                    ->join('bank_gls', 'bank_gls.id', 'bank_charge_gls.bank_gl_id')
                    ->join('special_gls', 'special_gls.id', 'bank_charge_gls.special_gl_id')
                    ->select('bank_charge_gls.bank_charge_gl')
                    ->where('special_gls.special_gl', $specialGl)
                    ->where('bank_gls.bank_gl', $bankGl);

        $bank_charge_gl = '';
        if( $query->count() > 0 ){
            $data = $query->first();
            $bank_charge_gl = $data->bank_charge_gl;
        }
        return $bank_charge_gl;
    }

    public static function getRefbySpecialGl($specialGl, $bankGl){
        $query = DB::table('bank_charge_gls')
                    ->join('bank_gls', 'bank_gls.id', 'bank_charge_gls.bank_gl_id')
                    ->join('special_gls', 'special_gls.id', 'bank_charge_gls.special_gl_id')
                    ->select('bank_charge_gls.reference')
                    ->where('special_gls.special_gl', $specialGl)
                    ->where('bank_gls.bank_gl', $bankGl);

        $reference = '';
        if( $query->count() > 0 ){
            $data = $query->first();
            $reference = $data->reference;
        }
        return $reference;
    }

}
