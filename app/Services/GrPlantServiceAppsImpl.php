<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

use App\Library\Helper;

use App\Repositories\SapRepositoryAppsImpl;

use App\Models\Plant;
use App\Models\Material;
use App\Models\Inventory\GrPlant;
use App\Models\Inventory\GrPlantItem;

class GrPlantServiceAppsImpl implements GrPlantService
{
    public function getOutstandingPoPlant($plantId)
    {
        $status = true;
        $message = '';

        $plant = DB::table('plants')
                    ->where('id', $plantId)
                    ->first();

        $param = [
            'plant' => $plant->code
        ];

        $sapRepository = new SapRepositoryAppsImpl();
        $sapResponse = $sapRepository->getOutstandingPoPlant($param);

        $outstanding = [];

        if ($sapResponse['status']) {
            $outstanding_sap = $sapResponse['response'];

            if($outstanding_sap){
                foreach ($outstanding_sap as $v) {
                    $plant_from = Plant::getShortNameByCode($v['code_from']);
                    $plant_to = Plant::getShortNameByCode($v['code_to']);
                    $outstanding[] = [
                        'code_from' => $v['code_from'],
                        'code_to' => $v['code_to'],
                        'plant_from' => $plant_from,
                        'plant_to' => $plant_to,
                        'document_number' => $v['document_number'],
                        'mutation_date' => $v['mutation_date'],
                    ];
                }
            }
        }

        return [
            'status' => $status,
            'message' => $message,
            'data' => $outstanding
        ];
    }

    public function getOutstandingGr($plantId, $documentNumber)
    {
        $status = true;
        $message = '';

        $param = [
            'doc_number' => $documentNumber
        ];

        $sapRepository = new SapRepositoryAppsImpl();
        $sapResponse = $sapRepository->getOutstandingGr($param);

        $detailOutstanding = [];

        if ($sapResponse['status']) {
            $detailOutstandingSap = $sapResponse['response'];
            if($detailOutstandingSap['success']){
                $detailOutstanding['header'] = $detailOutstandingSap['header'];
                foreach ($detailOutstandingSap['detail'] as $v) {
                    $material_id = Material::getIdByCode($v['material_code']);
                    $material_desc = Material::getDescByCode($v['material_code']);
                    $detailOutstanding['detail'][] = [
                        'material_code' => $v['material_code'],
                        'material_desc' => $material_desc,
                        'qty_po' => round(Helper::replaceDelimiterNumber($v['qty']), 3),
                        'qty_remaining' => round(Helper::replaceDelimiterNumber($v['qty_outstanding']), 3),
                        'qty_gr' => 0,
                        'uom' => $v['uom'],
                        'material_id' => $material_id,
                        'item_number' => $v['item_number'],
                    ];
                }
            }
        }

        return [
            'status' => $status,
            'message' => $message,
            'data' => $detailOutstanding
        ];
    }

    public function uploadGrPlant($companyId, $request)
    {
        $status = true;
        $message = Lang::get("message.save.success", ["data" => Lang::get("gr plant")]);

        $material_gr_sap = [];

        $qty = json_decode($request->qty, true);

        foreach (json_decode($request->material_gr) as $idx => $material) {
            /*
                note index material gr
                0 : number (null)
                1 : item number
                2 : material code
                3 : material desc
                4 : qty po
                5 : qty remaining
                6 : qty gr
                7 : uom
                8 : material id
            */
            $qtyIndex = Helper::replaceDelimiterNumber($qty[$idx]);
            if($qtyIndex <= 0){
                continue;
            }

            $material_gr_sap[] = [
                'COL01' => Helper::DateConvertFormat($request->receive_date, 'Y/m/d', 'd.m.Y'), #tgl receive
                'COL02' => $material[1], #item number
                'COL03' => $request->gi_number, #gi number
                'COL04' => $request->po_number, #po number
                'COL05' => $request->plant_to, #receiving plant code
                'COL06' => Helper::replaceDelimiterNumber($qty[$idx], '.', ','), #qty gr
                'COL07' => $material[7], #uom
            ];
        }

        !dd($material_gr_sap);

        $data_upload = [
            'items' => json_encode($material_gr_sap),
        ];

        $sapRepository = new SapRepositoryAppsImpl();
        $sapResponse = $sapRepository->uploadGrPlant($data_upload);

        $document_number = ""; #no GR

        if ($sapResponse['status']) {
            $res_sap = $sapResponse['response'];

            if (is_array($res_sap['data'])) {
                $last_resp_sap = $res_sap['data'][sizeof($res_sap['data']) - 1];
                $res_success = explode(' ', $last_resp_sap['MESSAGE']);
                if (strtolower($res_success[0]) == 'document' && strtolower($res_success[2]) == 'posted') {
                    $document_number = $res_success[1];
                } else {
                    // error
                    $status = false;
                    $message = (isset($last_resp_sap['MESSAGE'])) ? Lang::get("Feedback SAP") .  ' : '  . $last_resp_sap['MESSAGE'] : Lang::get("Sorry, an error occurred, please try again later");
                }
            }else{
                $res_success = explode(' ', $res_sap['data']);
                if( strtolower($res_success[0]) == 'document' && strtolower($res_success[2]) == 'posted' ){
                    $document_number = $res_success[1];
                } else {
                    // error
                    $message = (isset($res_sap['data'])) ? Lang::get("Feedback SAP") .  ' : '  . $res_sap['data'] : Lang::get("Sorry, an error occurred, please try again later");
                    $status = false;
                }
            }

            if($document_number != ''){
                DB::BeginTransaction();

                $success = false;

                $grPlant = new GrPlant;
                $grPlant->company_id = $companyId;
                $grPlant->document_number = $document_number;
                $grPlant->delivery_number = $request->gi_number;
                $grPlant->posto_number = $request->po_number;
                $grPlant->date = $request->receive_date;
                $grPlant->receiving_plant_id = Plant::getIdByCode($request->plant_to);
                $grPlant->issuing_plant_id = Plant::getIdByCode($request->plant_from);
                $grPlant->recepient = $request->recepient;
                $grPlant->gr_from = $request->text;
                if ($grPlant->save()) {
                    foreach (json_decode($request->material_gr) as $idx => $material) {
                        $qtyIndex = Helper::replaceDelimiterNumber($qty[$idx]);
                        if ($qtyIndex <= 0) {
                            continue;
                        }

                        /*
                        note index material gr
                        0 : number (null)
                        1 : item number
                        2 : material code
                        3 : material desc
                        4 : qty po
                        5 : qty remaining
                        6 : qty gr
                        7 : uom
                        8 : material id
                        */

                        $grPlantItem = new GrPlantItem;
                        $grPlantItem->gr_plant_id = $grPlant->id;
                        $grPlantItem->material_id = $material[8];
                        $grPlantItem->qty_gr = $qtyIndex;
                        $grPlantItem->qty_b4_gr = Helper::replaceDelimiterNumber($material[5]);
                        $grPlantItem->qty_po = Helper::replaceDelimiterNumber($material[4]);
                        $grPlantItem->qty_remaining = Helper::replaceDelimiterNumber($material[5]) - $qtyIndex;
                        $grPlantItem->uom = $material[7];
                        if ($grPlantItem->save()) {
                            $success = true;
                        } else {
                            $success = false;
                            exit;
                        }
                    }
                }

                if ($success) {
                    DB::commit();
                    $status = true;
                    $message = Lang::get("message.save.success", ["data" => Lang::get("gr plant")]);
                } else {
                    DB::rollBack();
                    $status = false;
                    $message = Lang::get("message.save.failed", ["data" => Lang::get("gr plant")]);
                }
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
