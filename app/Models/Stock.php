<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Library\Helper;

use App\Repositories\SapRepositoryAppsImpl;

class Stock extends Model
{
    const RAW = 'ZROH';
    const SEMI = 'ZHAL';
    const FINISH = 'ZFER';
    const PACK = 'ZVER';
    const PR = 'ZPRP';
    const OS = 'ZHIB';

    public static function getStockPlant($plant_id, $type){
        $plant = DB::table('plants')
                    ->where('id', $plant_id)
                    ->first();

        $param = [
            'type' => $type,
            'plant' => $plant->code
        ];

        $sapRepository = new SapRepositoryAppsImpl(true);
        $sapResponse = $sapRepository->getCurrentStockPlant($param);

        $stocks = [];

        if ($sapResponse['status']) {
            $stocks = $sapResponse['response'];
        }
        return $stocks;
    }

    public static function getMaterialTypeAll(){
        return [
            Stock::RAW,
            Stock::SEMI,
            Stock::FINISH,
            Stock::PACK,
            Stock::PR,
            Stock::OS
        ];
    }
}
