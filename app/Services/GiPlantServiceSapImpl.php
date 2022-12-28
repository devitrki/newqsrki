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
            'header_text' => 'WEB TRANSFER',
            'posting_date' => $data_gi['header']->date,
            'document_date' => $data_gi['header']->date,
            'items' => []
        ];

        $coind = 1;

        foreach ($data_gi['items'] as $giItem) {
            $dataUpload['items'][] = [
                'po_item' => $coind,
                'material_id' => $giItem->material_code,
                'qty' => (float)$giItem->qty,
                'uom_id' => (strtolower($giItem->uom) == 'pac') ? 'PAK' : $giItem->uom,
                'sloc_id' => Plant::getSlocIdGiPlant($data_gi['header']->receiving_plant_id),
                'item_text' => '',
                'gr_receipt' => $data_gi['header']->requester,
                'plant_id' => $data_gi['header']->receiving_plant_code,
            ];
            $coind += 1;
        }

        $dataUpload = [$dataUpload];

        $sapRepository = new SapRepositorySapImpl($data_gi['header']->company_id);
        $sapResponse = $sapRepository->uploadGiPlant($dataUpload);

        $document_number = ""; #no gi
        $document_posto = ""; #no po sto

        if ($sapResponse['status']) {
            $resSap = $sapResponse['response'];
            $lastRespSap = $resSap[sizeof($resSap) - 1];

            if ($lastRespSap['gi_status']['success'] &&
                $lastRespSap['po_status']['success'] &&
                $lastRespSap['release_status']['success'] &&
                $lastRespSap['gi_status']['document_number'] != '' &&
                $lastRespSap['po_status']['document_number'] != ''
                )
            {
                $document_number = $lastRespSap['gi_status']['document_number'];
                $document_posto = $lastRespSap['po_status']['document_number'];
                $message = Lang::get("message.upload.success", ["data" => Lang::get("gi plant")]);
            } else {
                $status = false;

                if ($lastRespSap['po_status']['success']) {
                    if ($lastRespSap['po_status']['document_number'] != "") {
                        $document_posto = $lastRespSap['po_status']['document_number'];
                        $message = Lang::get("POSTO numbers have been created, but not GI numbers. Please resend a few minutes later");
                    }
                }
            }

            $giPlant = GiPlant::find($giPlantId);
            $giPlant->json_sap = json_encode($dataUpload);
            if($document_number != ''){
                $giPlant->document_number = $document_number;
            }
            if($document_posto != ''){
                $giPlant->document_posto = $document_posto;
            }

            if(!$giPlant->save()){
                $status = false;
                $message = Lang::get("Sorry, an error occurred when save to database, please try again later");
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
