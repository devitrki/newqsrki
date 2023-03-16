<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

use App\Library\Helper;

use App\Repositories\SapRepositorySapImpl;
use App\Entities\SapMiddleware;

use App\Models\Plant;
use App\Models\Company;
use App\Models\Configuration;
use App\Models\UomConvert;
use App\Models\Inventory\GrVendor;

class GrVendorServiceSapImpl implements GrVendorService
{
    public function getOutstandingPoVendor($plantId)
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
            'is_vendor' => true
        ];

        $sapRepository = new SapRepositorySapImpl($plant->company_id);
        $sapResponse = $sapRepository->getOutstandingPoVendor($param);

        $outstanding = [];

        if ($sapResponse['status']) {
            $outstandingSap = $sapResponse['response'];

            foreach ($outstandingSap as $v) {
                $poDate = $v['delivery_date'];
                $curDate = Date('Y-m-d');
                $diffDays = Helper::DateDifference($poDate, $curDate);
                $vendor_id = $v['vendor_id'];
                $vendor_allows_day = Configuration::getValueCompByKeyFor($plant->company_id, 'inventory', 'vendor_allow');
                $vendor_allows_days = explode(',', str_replace(' ', '', $vendor_allows_day) );
                $qty_remaining_po = round($v['schedule_qty'],3) - round($v['gr_qty'], 3);
                if( ($diffDays > 130 && !in_array( $vendor_id , $vendor_allows_days )) ||  $qty_remaining_po <= 0){
                    continue;
                }

                $outstanding[] = [
                    'mandt' => '',
                    'doc_number' => $v['po_number'],
                    'vendor_id' => $vendor_id,
                    'vendor_name' => $v['vendor_name'],
                    'item_number' => $v['po_item'],
                    'material_code' => substr($v['material_id'], -7),
                    'material_desc' => $v['material_name'],
                    'po_date' => Helper::DateConvertFormat($v['delivery_date'], 'Y-m-d', 'd-m-Y'),
                    'uom' => $v['uom_id'],
                    'qty_po' => round($v['schedule_qty'], 3),
                    'qty_remaining_po' => $qty_remaining_po,
                    'elikz' => '',
                    'plant_id' => $plantId,
                    'gi_number' => $v['gi_number'],
                ];
            }
        }

        return [
            'status' => $status,
            'message' => $message,
            'data' => $outstanding
        ];
    }

    public function uploadGrVendor($companyId, $request)
    {
        $status = true;
        $message = Lang::get("message.save.success", ["data" => Lang::get("gr po vendor")]);

        $sapCodeComp = Company::getConfigByKey($companyId, 'SAP_CODE');
        if (!$sapCodeComp || $sapCodeComp == '') {
            return [
                'status' => false,
                'message' => Lang::get('Please set SAP_CODE in company configuration'),
            ];
        }

        $dataUpload = [
            'company_id' => $sapCodeComp,
            'plant_id' => Plant::getCodeById($request->plant_id),
            'po_number' => $request->po_number,
            'posting_date' => Helper::DateConvertFormat($request->posting_date, 'Y/m/d', 'Y-m-d'),
            'reference_number' => '',
            'delivery_note' => $request->ref_number,
            'header_text' => '',
            'items' => [
                [
                    'po_item' => $request->item_number,
                    'movement_type_id' => '',
                    'material_id' => $request->material_code ? $request->material_code : '',
                    'qty_entry' => (float)$request->qty_gr,
                    'receiver' => $request->recepient,
                    'sloc_id' => Plant::getSlocIdGrVendor($request->plant_id),
                    'uom_id' => UomConvert::getSendSapUom($companyId, strtoupper($request->uom))
                ]
            ],
        ];

        $sapRepository = new SapRepositorySapImpl($companyId);
        $sapResponse = $sapRepository->uploadGrVendor($dataUpload);

        $document_number = ""; #no GR

        if ($sapResponse['status']) {
            $resSap = $sapResponse['response'];

            $document_number = '';
            $status = true;

            if ((bool)$resSap['success']) {
                $document_number = $resSap['document_number'];
                if( $document_number != '' ){
                    $qty_gr = Helper::replaceDelimiterNumber($request->qty_gr);
                    $qty_remaining_po = Helper::replaceDelimiterNumber($request->qty_remaining_po);
                    $qty_po = Helper::replaceDelimiterNumber($request->qty_po);

                    $grVendor = new GrVendor;
                    $grVendor->company_id = $companyId;
                    $grVendor->gr_number = $document_number;
                    $grVendor->po_number = $request->po_number;
                    $grVendor->ref_number = $request->ref_number;
                    $grVendor->vendor_id = $request->vendor_id;
                    $grVendor->vendor_name = $request->vendor_name;
                    $grVendor->item_number = $request->item_number;
                    $grVendor->material_code = $request->material_code;
                    $grVendor->material_desc = $request->material_desc;
                    $grVendor->plant_id = $request->plant_id;
                    $grVendor->po_date = Helper::DateConvertFormat($request->po_date, 'd-m-Y', 'Y/m/d');
                    $grVendor->posting_date = $request->posting_date;
                    $grVendor->qty_gr = $qty_gr;
                    $grVendor->qty_remaining_po = round($qty_remaining_po, 3);
                    $grVendor->qty_po = $qty_po;
                    $grVendor->qty_remaining = round($qty_remaining_po - $qty_gr, 3);
                    $grVendor->uom = $request->uom;
                    $grVendor->recepient = $request->recepient;
                    $grVendor->batch = $request->batch;
                    if ($grVendor->save()) {
                        $status = true;
                        $message = Lang::get("message.save.success", ["data" => Lang::get("gr po vendor")]);
                    } else {
                        $status = false;
                        $message = Lang::get("message.save.failed", ["data" => Lang::get("gr po vendor")]);
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
            $message = 'Error middleware: ' . $sapResponse['response'];
        }

        return [
            "status" => $status,
            "message" => $message
        ];
    }
}
