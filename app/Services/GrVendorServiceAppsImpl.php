<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

use App\Library\Helper;

use App\Repositories\SapRepositoryAppsImpl;

use App\Models\Plant;
use App\Models\Configuration;
use App\Models\Inventory\GrVendor;

class GrVendorServiceAppsImpl implements GrVendorService
{
    public function getOutstandingPoVendor($plantId)
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
        $sapResponse = $sapRepository->getOutstandingPoVendor($param);

        $outstanding = [];

        if ($sapResponse['status']) {
            $outstanding_sap = $sapResponse['response'];

            foreach ($outstanding_sap as $v) {
                $poDate = Helper::DateConvertFormat($v['EINDT'], 'Ymd', 'Y-m-d');
                $curDate = Date('Y-m-d');
                $diffDays = Helper::DateDifference($poDate, $curDate);
                $vendor_id = round($v['LIFNR']) . '';
                $vendor_allows_day = Configuration::getValueCompByKeyFor($plant->company_id, 'inventory', 'vendor_allow');
                $vendor_allows_days = explode(',', str_replace(' ', '', $vendor_allows_day) );
                $qty_remaining_po = round($v['MENGE'],3) - round($v['WEMNG'], 3);
                if( ($diffDays > 130 && !in_array( $vendor_id , $vendor_allows_days )) ||  $qty_remaining_po <= 0){
                    continue;
                }

                if( is_numeric($v['MATNR'])){
                    $matCode = $v['MATNR'] + 0;
                } else {
                    $matCode = $v['MATNR'];
                }

                $outstanding[] = [
                    'mandt' => $v['MANDT'],
                    'doc_number' => round($v['EBELN']),
                    'vendor_id' => $vendor_id,
                    'vendor_name' => $v['NAME1'],
                    'item_number' => $v['EBELP'],
                    'material_code' => $matCode . "",
                    'material_desc' => $v['TXZ01'],
                    'po_date' => Helper::DateConvertFormat($v['EINDT'], 'Ymd', 'd-m-Y'),
                    'uom' => $v['MEINS'],
                    'qty_po' => round($v['MENGE'], 3),
                    'qty_remaining_po' => $qty_remaining_po,
                    'elikz' => $v['ELIKZ'],
                    'plant_id' => $plantId,
                    'gi_number' => '',
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

        $data = [
            'doc_number' => $request->po_number, #po number
            'item' => $request->item_number, #item number
            'posting_date' => Helper::DateConvertFormat($request->posting_date, 'Y/m/d', 'Ymd'), #posting_date
            'gr_qty' => str_replace('.', ',', $request->qty_gr), #qty gr
            'penerima' => $request->recepient, #recepient
            'surat_jalan' => $request->ref_number, #ref_number
            'storage' => Plant::getSlocIdGrVendor($request->plant_id), #storage
        ];

        !dd($data);

        $sapRepository = new SapRepositoryAppsImpl();
        $sapResponse = $sapRepository->uploadGrVendor($data);

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
            } else {
                $res_success = explode(' ', $res_sap['data']);
                if (strtolower($res_success[0]) == 'document' && strtolower($res_success[2]) == 'posted') {
                    $document_number = $res_success[1];
                } else {
                    // error
                    $message = (isset($res_sap['data'])) ? Lang::get("Feedback SAP") .  ' : '  . $res_sap['data'] : Lang::get("Sorry, an error occurred, please try again later");
                    $status = false;
                }
            }

            if ($document_number != '') {

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
