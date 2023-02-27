<?php

namespace App\Models\ExternalVendor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\DB;

class TargetVendor extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getTransferTypes() {
        $transferTypes = [
            'ftp' => 'FTP',
            'sftp' => 'SFTP',
            '1utama_api' => '1Utama API'
        ];

        return $transferTypes;
    }

    public static function getConfigByKey($targetVendorId, $key)
    {
        $value = '';
        $configurations = self::getConfigs($targetVendorId);
        $configurations = json_decode($configurations);
        foreach ($configurations as $k => $v) {
            if ($key == $k) {
                $value = $v;
            }
        }

        return $value;
    }

    public static function getConfigs($targetVendorId)
    {
        $targetVendor = DB::table('target_vendors')
                        ->where('id', $targetVendorId)
                        ->select('configurations')
                        ->first();

        return $targetVendor->configurations;
    }
}
