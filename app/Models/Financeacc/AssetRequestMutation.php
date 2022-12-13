<?php

namespace App\Models\Financeacc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AssetRequestMutation extends Model
{
    use HasFactory;

    /*
        Step request asset mutation
        1 = submit
        2 = cancel
        3 = approve hod
        4 = unapprove hod
        5 = confirmation validator
        6 = reject by validator
        7 = confirmation send dc
        8 = reject by dc
    */

    public static function getStatusRequestByAssetNumber($AssetNumber, $assetSubNumber) {
        $status = -1;

        $qAssetMutation = DB::table('asset_request_mutations')
                            ->where('number', $AssetNumber)
                            ->where('number_sub', $assetSubNumber)
                            ->select('status')
                            ->orderByDesc('created_at');

        if( $qAssetMutation->count() > 0 ){
            $assetMutation = $qAssetMutation->first();
            $status = $assetMutation->status;
        }

        return $status;
    }
}
