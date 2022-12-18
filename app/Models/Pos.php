<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use OwenIt\Auditing\Contracts\Auditable;

use App\Repositories\AlohaRepository;

class Pos extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public function plants()
    {
        return $this->hasMany(Plant::class);
    }

    // utility
    public static function getConfigByKey($posId, $key)
    {
        $value = '';
        $configurations = self::getConfigs($posId);
        $configurations = json_decode($configurations);
        foreach ($configurations as $k => $v) {
            if ($key == $k) {
                $value = $v;
            }
        }

        return $value;
    }

    public static function getConfigs($posId)
    {
        $configurations = Cache::rememberForever('pos_configuration_id_' . $posId, function () use ($posId) {
            $company = DB::table('pos')
                        ->where('id', $posId)
                        ->select('configurations')
                        ->first();

            return $company->configurations;
        });

        return $configurations;
    }

    public static function getInstanceRepo($pos)
    {
        $instanceRepo = null;

        switch ($pos->code) {
            case 'aloha_rki':
                $instanceRepo = new AlohaRepository($pos->id);
                break;

            default:
                $instanceRepo = new AlohaRepository($pos->id);
                break;
        }

        return $instanceRepo;
    }
}
