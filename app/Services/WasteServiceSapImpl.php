<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

use App\Models\Plant;
use App\Models\Company;
use App\Models\Inventory\Waste;

use App\Entities\SapMiddleware;

use App\Repositories\SapRepositorySapImpl;

class WasteServiceSapImpl implements WasteService
{
    public function uploadWaste($companyId, $wasteId)
    {
        $sapCodeComp = Company::getConfigByKey($companyId, 'sap_code');
        if (!$sapCodeComp || $sapCodeComp == '') {
            return [
                "status" => false,
                "message" => Lang::get("Please set sap_code in company configuration")
            ];
        }

        $status = true;
        $message = Lang::get("message.submit.success", ["data" => Lang::get("waste")]);

        $waste = DB::table('wastes')
                    ->where('id', $wasteId)
                    ->first();

        $plantCustCode = Plant::getCustomerCodeById($waste->plant_id);
        $plantSlocIdWaste = Plant::getSlocIdWaste($waste->plant_id);

        // upload to SAP
        $dataUpload = [
            'company_id' => $sapCodeComp,
            'posting_date' => date('Y-m-d', strtotime($waste->date)),
            'customer_id' => $plantCustCode,
            'items' => []
        ];

        $wasteItems  = DB::table('waste_items')->where('waste_id', $wasteId)->get();
        foreach ($wasteItems as $item) {

            $material = DB::table('material_outlets')
                            ->where('company_id', $companyId)
                            ->where('code', $item->material_code)
                            ->select('waste_flag')
                            ->first();

            if( $material->waste_flag != 0 ){
                $flag = true; // x
            } else {
                $flag = false; // ''
            }

            $dataUpload['items'][] = [
                'movement_type_id' => '551', #MOVEMENT TYPE selalu 551
                'sloc_id' => $plantSlocIdWaste, # sloc id plant
                'material_id' => $item->material_code,
                'entry_qty' => (float)$item->qty, #qty
                'entry_uom_id' => $item->uom, #uom
                'batch_number' => '',
                'cost_center_id' => '',
                'is_checked' => $flag
            ];

        }

        $sapRepository = new SapRepositorySapImpl(true);
        $sapResponse = $sapRepository->uploadWaste($dataUpload);

        if ($sapResponse['status']) {
            $resSap = $sapResponse['response'];

            $document_number = '';
            $status = true;

            if ((bool)$resSap['success']) {
                $document_number = $resSap['document_number'];
                if( $document_number != '' ){
                    $waste = Waste::find($wasteId);
                    $waste->document_number = $document_number;
                    $waste->submit = 1;
                    $waste->posting_date = date('Y-m-d H:i:s');
                    $waste->save();

                    $status = true;
                    $message = Lang::get("message.submit.success", ["data" => Lang::get("waste")]);
                } else {
                    $status = false;
                    $message = Lang::get("Feedback SAP") . ' : Document number not created';
                }
            } else {
                $status = false;
                $message = SapMiddleware::getLastErrorMessage($resSap['logs']);
            }

        } else {
            $status = false;
            $message = Lang::get("Sorry, an error occurred, please try again later");
        }

        return [
            "status" => $status,
            "message" => $message
        ];
    }
}
