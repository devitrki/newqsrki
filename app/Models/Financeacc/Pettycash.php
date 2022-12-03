<?php

namespace App\Models\Financeacc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\DB;

use App\Library\Helper;

use App\Models\Plant;
use App\Models\PettycashCcGl;
use App\Models\Configuration;

class Pettycash extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getVendorSAPFixed($companyId, $plantId)
    {
        $plantType = Plant::getTypeByPlantId($plantId);
        $vendorIdDC = Configuration::getValueCompByKeyFor($companyId, 'financeacc', 'vendor_id_dc');
        $vendorIdOutlet = Configuration::getValueCompByKeyFor($companyId, 'financeacc', 'vendor_id_outlet');

        $vendor = $vendorIdOutlet;
        if( $plantType != 'Outlet' ){
            // dc
            $vendor = $vendorIdDC;
        }

        return $vendor;
    }

    public static function getIndexGlTemp($gl_temp, $gl_code)
    {
        $i = 9999;
        foreach ($gl_temp as $k => $v) {
            if ($v['gl_code'] == $gl_code) {
                $i = $v['i'];
            }
        }
        return $i;
    }

    public static function getCCSAPFixed($companyId, $gl, $cc, $plantId)
    {
        $pettycashGlCc = DB::table('pettycash_cc_gls')
                            ->where('company_id', $companyId)
                            ->where('gl', $gl);

        if ($pettycashGlCc->count() > 0) {
            $pettycashGlCc = $pettycashGlCc->first();
            if ($pettycashGlCc->privilege != 0) {
                $plantType = Plant::getTypeByPlantId($plantId);
                $privilegeDesc = PettycashCcGl::getDescPrivilege($pettycashGlCc->privilege);
                if($plantType == $privilegeDesc){
                    $cc = $pettycashGlCc->cc;
                }
            } else {
                $cc = $pettycashGlCc->cc;
            }
        }

        return $cc;
    }

    public static function getDescToSAP($uniqueItems, $desc, $plantId)
    {
        $trans_id = "(";
        $transId = [];
        foreach ($uniqueItems as $k => $v) {
            $transId[] = $v;
            if (($k + 1) != sizeof($uniqueItems)) {
                $trans_id .= "'" . $v . "',";
            } else {
                $trans_id .= "'" . $v . "'";
            }
        }
        $trans_id .= ")";
        $label = 'RF ';
        $plantType = Plant::getTypeByPlantId($plantId);
        if( $plantType != 'Outlet' ){
            $label = '';
        }

        $descSAP = $label . $desc . ' ' . Pettycash::getDescVoucherN0($transId, sizeof($uniqueItems)) . ' (' . Pettycash::getDescTglVoucherN0($transId, sizeof($uniqueItems)) . ')';
        return $descSAP;
    }

    public static function getDescVoucherN0($trans_id, $count)
    {
        $minVoucher = DB::table('pettycashes')
                        ->whereIn('id', $trans_id)
                        ->min('voucher_number');

        $voucher = $minVoucher;
        if ($count > 1) {
            $maxVoucher = DB::table('pettycashes')
                            ->whereIn('id', $trans_id)
                            ->max('voucher_number');
            $voucher .= '-' . $maxVoucher;
        }

        return $voucher;
    }

    public static function getDescTglVoucherN0($trans_id, $count)
    {
        if ($count > 1) {
            $minTransDate = DB::table('pettycashes')
                            ->whereIn('id', $trans_id)
                            ->min('transaction_date');
            $exp_trans_date = explode('-', $minTransDate);

            $maxTransDate = DB::table('pettycashes')
                            ->whereIn('id', $trans_id)
                            ->max('transaction_date');
            $exp_trans_date2 = explode('-', $maxTransDate);

            if ($exp_trans_date[1] != $exp_trans_date2[1] && $exp_trans_date[0] != $exp_trans_date2[0]) {
                $trans_date = $exp_trans_date[2] . ' ' . Helper::getMonthByNumberMonth((int) $exp_trans_date[1]) . ' ' . $exp_trans_date[0] . ' - ' . $exp_trans_date2[2] . ' ' . Helper::getMonthByNumberMonth((int) $exp_trans_date2[1]) . ' ' . $exp_trans_date2[0];
            } else if ($exp_trans_date[1] == $exp_trans_date2[1] && $exp_trans_date[0] != $exp_trans_date2[0]) {
                $trans_date = $exp_trans_date[2] . ' ' . Helper::getMonthByNumberMonth((int) $exp_trans_date[1]) . ' - ' . $exp_trans_date2[2] . ' ' . Helper::getMonthByNumberMonth((int) $exp_trans_date2[1]) . ' ' . $exp_trans_date2[0];
            } else if ($exp_trans_date[2] != $exp_trans_date2[2] && $exp_trans_date[1] == $exp_trans_date2[1] && $exp_trans_date[0] == $exp_trans_date2[0]) {
                $trans_date = $exp_trans_date[2] . '-' . $exp_trans_date2[2] . ' ' . Helper::getMonthByNumberMonth((int) $exp_trans_date2[1]) . ' ' . $exp_trans_date2[0];
            } else {
                $trans_date = $exp_trans_date[2] . ' ' . Helper::getMonthByNumberMonth((int) $exp_trans_date2[1]) . ' ' . $exp_trans_date2[0];
            }
        } else {
            $minTransDate = DB::table('pettycashes')
                            ->whereIn('id', $trans_id)
                            ->min('transaction_date');
            $exp_trans_date = explode('-', $minTransDate);
            $trans_date = $exp_trans_date[2] . ' ' . Helper::getMonthByNumberMonth((int) $exp_trans_date[1]) . ' ' . $exp_trans_date[0];
        }

        return $trans_date;
    }

}
