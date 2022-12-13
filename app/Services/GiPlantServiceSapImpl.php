<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

use App\Library\Helper;

use App\Repositories\SapRepositorySapImpl;
use App\Entities\SapMiddleware;

use App\Models\Plant;
use App\Models\Material;
use App\Models\Company;
use App\Models\Inventory\GiPlant;
use App\Models\Inventory\GiPlantItem;

class GiPlantServiceSapImpl implements GiPlantService
{
    public function uploadGiPlant($giPlantId)
    {
        $status = true;
        $message = Lang::get("message.upload.success", ["data" => Lang::get("gi plant")]);

        $data_gi = GiPlant::getDataDetailById($giPlantId);

        $sapCodeComp = Company::getConfigByKey($data_gi['header']->company_id, 'SAP_CODE');
        if (!$sapCodeComp || $sapCodeComp == '') {
            return [
                'status' => false,
                'message' => Lang::get('Please set SAP_CODE in company configuration'),
            ];
        }

        $dataUpload = [
            'company_id' => $sapCodeComp,
            'supply_plant_id' => $data_gi['header']->issuing_plant_code,
            'ref_doc_no' => $data_gi['header']->issuer,
            'bill_of_ladding' => '',
            'gr_slip' => '',
            'header_text' => '',
            'posting_date' => Helper::DateConvertFormat($data_gi['header']->date, 'Y-m-d', 'Ymd'),
            'document_date' => Helper::DateConvertFormat($data_gi['header']->date, 'Y-m-d', 'Ymd'),
            'items' => []
        ];

        $coind = 1;
        $cokey = Helper::getKeySap();

        foreach ($data_gi['items'] as $giItem) {
            $dataUpload['items'][] = [
                'po_item' => $coind,
                'material_id' => $giItem->material_code,
                'qty' => (float)$giItem->qty,
                'uom_id' => (strtolower($giItem->uom) == 'pac') ? 'PAK' : $giItem->uom,
                'sloc_id' => Plant::getSlocIdGiPlant($data_gi['header']->receiving_plant_id),
                'item_text' => '',
                'gr_receipt' => $cokey,
                'plant_id' => $data_gi['header']->receiving_plant_code,
            ];
            $coind += 1;
        }

        !dd($dataUpload);

        $sapRepository = new SapRepositorySapImpl($data_gi['header']->company_id, true);
        $sapResponse = $sapRepository->uploadGiPlant($dataUpload);

        $document_number = ""; #no GR

        if ($sapResponse['status']) {

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
