<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

use App\Repositories\SapRepositoryAppsImpl;

class StockServiceAppsImpl implements StockService
{
    public function getCurrentStockPlant($plantId, $type)
    {
        $status = true;
        $message = '';

        $plant = DB::table('plants')
                    ->where('id', $plantId)
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

        !dd($stocks);

        return [
            'status' => $status,
            'message' => $message,
            'data' => $stocks
        ];
    }
}
