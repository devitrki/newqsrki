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
use App\Models\Inventory\GrPlant;
use App\Models\Inventory\GrPlantItem;

class GrPlantServiceSapImpl implements GrPlantService
{
    public function getOutstandingPoPlant($plantId, $filter = true)
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

        $param = [
            'company_id' => $sapCodeComp,
            'plant_id' => $plant->code,
            'is_vendor' => false
        ];

        $sapRepository = new SapRepositorySapImpl($plant->company_id);
        $sapResponse = $sapRepository->getOutstandingPoVendor($param);
        $outstanding = [];

        if ($sapResponse['status']) {
            $outstandingSap = $sapResponse['response'];

            if ($filter) {
                $giExist = [];

                foreach ($outstandingSap as $v) {
                    $remainingQty = $v['schedule_qty'] + $v['gr_qty'];
                    if (in_array($v['gi_number'], $giExist) || $remainingQty <= 0) {
                        continue;
                    }

                    $plant_from = Plant::getShortNameByCode($v['supplying_plant_id']);
                    $plant_to = Plant::getShortNameByCode($v['receiving_plant_id']);
                    $outstanding[] = [
                        'code_from' => $v['supplying_plant_id'],
                        'code_to' => $v['receiving_plant_id'],
                        'plant_from' => $plant_from,
                        'plant_to' => $plant_to,
                        'document_number' => $v['gi_number'],
                        'mutation_date' => Helper::DateConvertFormat($v['delivery_date'], 'Y-m-d', 'd/m/Y'),
                    ];

                    $giExist[] = $v['gi_number'];
                }
            } else {
                $outstanding = $outstandingSap;
            }
        }

        return [
            'status' => $status,
            'message' => $message,
            'data' => $outstanding
        ];
    }

    public function getOutstandingGr($plantCode, $documentNumber)
    {
        $status = true;
        $message = '';

        $plant = DB::table('plants')
                    ->where('code', $plantCode)
                    ->first();

        $sapCodeComp = Company::getConfigByKey($plant->company_id, 'SAP_CODE');
        if (!$sapCodeComp || $sapCodeComp == '') {
            return [
                'status' => false,
                'message' => Lang::get('Please set SAP_CODE in company configuration'),
            ];
        }

        $payload = [
            'company_id' => $sapCodeComp,
            'plant_id' => '',
            'document_number' => $documentNumber,
            'document_year' => Date('Y')
        ];

        $sapRepository = new SapRepositorySapImpl($plant->company_id);
        $sapResponse = $sapRepository->getOutstandingGr($payload);

        $detailOutstanding = [];

        if ($sapResponse['status']) {
            $detailOutstandingSap = $sapResponse['response'];

            if ($detailOutstandingSap) {
                $detailOutstanding['header']['doc_date'] = $detailOutstandingSap[0]['document_date'];
                $detailOutstanding['header']['document_number'] = $documentNumber;
                $detailOutstanding['header']['plant'] = $detailOutstandingSap[0]['receiving_plant_id'];
                $detailOutstanding['header']['plant_from'] = $detailOutstandingSap[0]['supplying_plant_id'];
                $detailOutstanding['header']['po_number'] = $detailOutstandingSap[0]['po_number'];
                $detailOutstanding['header']['text'] = $detailOutstandingSap[0]['header_text'];
            }

            $detailOutstanding['detail'] = [];

            foreach ($detailOutstandingSap as $v) {
                $material_id = Material::getIdByCode($v['material_id']);
                $qty_outstanding = round($v['entry_qty'], 3) - round($v['receive_qty'], 3);
                $detailOutstanding['detail'][] = [
                    'material_code' => $v['material_id'],
                    'material_desc' => $v['material_name'],
                    'qty_po' => round($v['entry_qty'], 3),
                    'qty_remaining' => $qty_outstanding,
                    'qty_gr' => 0,
                    'uom' => $v['uom_id'],
                    'material_id' => $material_id,
                    'item_number' => $v['item_number'],
                ];
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

        $sapCodeComp = Company::getConfigByKey($companyId, 'SAP_CODE');
        if (!$sapCodeComp || $sapCodeComp == '') {
            return [
                'status' => false,
                'message' => Lang::get('Please set SAP_CODE in company configuration'),
            ];
        }

        $dataUpload = [
            'company_id' => $sapCodeComp,
            'plant_id' => $request->plant_to, // receive plant
            'po_number' => $request->po_number,
            'document_date' => Helper::DateConvertFormat($request->receive_date, 'Y/m/d', 'Y-m-d'),
            'posting_date' => Helper::DateConvertFormat($request->receive_date, 'Y/m/d', 'Y-m-d'),
            'delivery_note' => $request->gi_number,
            'header_text' => '',
            'items' => [],
        ];

        $plantIdTo = Plant::getIdByCode($request->plant_to);
        $slocIdGr = Plant::getSlocIdGr($plantIdTo);

        $qty = json_decode($request->qty, true);
        foreach (json_decode($request->material_gr) as $idx => $material) {
            $qtyIndex = Helper::replaceDelimiterNumber($qty[$idx]);
            if($qtyIndex <= 0){
                continue;
            }

            $dataUpload['items'][] = [
                'movement_type_id' => '',
                'po_item' => $material[1] . '',
                'material_id' => $material[2],
                'sloc_id' => $slocIdGr,
                'entry_qty' => (float)$qtyIndex,
                'entry_uom_id' => $material[7],

            ];
        }

        $sapRepository = new SapRepositorySapImpl($companyId);
        $sapResponse = $sapRepository->uploadGrPlant($dataUpload);

        $document_number = ""; #no GR

        if ($sapResponse['status']) {
            $resSap = $sapResponse['response'];

            $document_number = '';
            $status = true;

            if ((bool)$resSap['success']) {
                $document_number = $resSap['document_number'];
                if( $document_number != '' ){
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
