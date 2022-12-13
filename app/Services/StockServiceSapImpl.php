<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

use App\Repositories\SapRepositorySapImpl;

use App\Models\Stock;
use App\Models\Company;

class StockServiceSapImpl implements StockService
{
    public function getCurrentStockPlant($plantId, $type)
    {
        $status = true;
        $message = '';

        $plant = DB::table('plants')
                    ->where('id', $plantId)
                    ->first();

        $sapCodeComp = Company::getConfigByKey($plant->company_id, 'SAP_CODE');
        if (!$sapCodeComp || $sapCodeComp == '') {
            return [
                'status' => false,
                'message' => Lang::get('Please set SAP_CODE in company configuration'),
            ];
        }

        $materialTypeId = [$type];
        if ($type == 'all') {
            $materialTypeId = Stock::getMaterialTypeAll();
        }

        $payload = [
            'company_id' => $sapCodeComp,
            'plant_id' => $plant->code,
            'sloc_id' => [],
            'material_type_id' => $materialTypeId
        ];

        $stocks = [];

        $sapRepository = new SapRepositorySapImpl($plant->company_id);
        $sapResponse = $sapRepository->getCurrentStockPlant($payload);
        if ($sapResponse['status']) {
            $stockSaps = $sapResponse['response'];

            foreach ($stockSaps as $stockSap) {
                $stocks[] = [
                    "plant" => $plant->code,
                    "material_type" => $stockSap['material_type_id'],
                    "material_code" => $stockSap['material_id'],
                    "material_desc" => $stockSap['material_name'],
                    "qty" => $stockSap['qty'],
                    "uom" => $stockSap['uom_id'],
                ];
            }
        }

        return [
            'status' => $status,
            'message' => $message,
            'data' => $stocks
        ];
    }
}
