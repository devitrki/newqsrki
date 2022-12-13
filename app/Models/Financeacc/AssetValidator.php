<?php

namespace App\Models\Financeacc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AssetValidator extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getListEmailPicValidator($assetValidatorId, $fromPlantId) {

        $qAssetRequestMutation = DB::table('asset_validator_mappings')
                            ->where('asset_validator_id', $assetValidatorId)
                            // ->where('plant_id', $fromPlantId)
                            ->select('pic_validators');
        $list = [];
        if( $qAssetRequestMutation->count() > 0 ){
            $assetRequestMutation = $qAssetRequestMutation->first();
            $validators = explode(',', $assetRequestMutation->pic_validators);
            foreach ($validators as $validator) {
                $list[] = User::getEmailById($validator);
            }
        }

        return $list;
    }

    public static function getValidatorByUserId($userId) {

        $qAssetRequestMutation = DB::table('asset_validator_mappings')
                            ->select('pic_validators', 'asset_validator_id');
        $list = [];
        if( $qAssetRequestMutation->count() > 0 ){
            $assetRequestMutations = $qAssetRequestMutation->get();
            foreach ($assetRequestMutations as $assetRequestMutation) {
                $validators = explode(',', $assetRequestMutation->pic_validators);
                if( in_array($userId, $validators) ){
                    $list[] = $assetRequestMutation->asset_validator_id;
                }
            }
        }

        return $list;
    }

    public static function getNameById($assetValidatorId) {

        $qAssetRequestMutation = DB::table('asset_validators')
                            ->where('id', $assetValidatorId)
                            ->select('name');
        $name = '';
        if( $qAssetRequestMutation->count() > 0 ){
            $assetRequestMutation = $qAssetRequestMutation->first();
            $name = $assetRequestMutation->name;
        }

        return $name;
    }
}
