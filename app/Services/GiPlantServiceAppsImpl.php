<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

use App\Library\Helper;

use App\Repositories\SapRepositoryAppsImpl;

use App\Models\Plant;
use App\Models\Material;
use App\Models\Inventory\GiPlant;
use App\Models\Inventory\GiPlantItem;

class GiPlantServiceAppsImpl implements GiPlantService
{
    public function uploadGiPlant($giPlantId)
    {
        $status = true;
        $message = Lang::get("message.upload.success", ["data" => Lang::get("gi plant")]);

        $data_gi = GiPlant::getDataDetailById($giPlantId);

        $data_upload = [];
        $coind = 1;
        $cokey = Helper::getKeySap();
        foreach ($data_gi['items'] as $giItem) {
            $data_upload[] = [
                'SWERK' => $data_gi['header']->issuing_plant_code, #from, WERKS(RFC)
                'MATNR' => $giItem->material_code, #Material Code
                'MATDS' => $giItem->material_desc, #Material Code Desc
                'MENGE' => round($giItem->qty, 3), #Qty
                'MEINS' => (strtolower($giItem->uom) == 'pac') ? 'PAK' : $giItem->uom, #uom, ERFME(RFC)
                'LGORT' => ($data_gi['header']->issuing_plant_type != 1) ? 'DR01' : 'S001', #sloc
                'RWERK' => $data_gi['header']->receiving_plant_code, #to, UMWRK(RFC)
                'EBELN' => "", #null like exam from doc api
                'BUDAT' => Helper::DateConvertFormat($data_gi['header']->date, 'Y-m-d', 'Ymd'), #posting (RFC) 'yyyymmdd'
                'BLDAT' => Helper::DateConvertFormat($data_gi['header']->date, 'Y-m-d', 'Ymd'), #doc date (RFC)
                'MTSNR' => $data_gi['header']->issuer, #No SP manual diganti jadi issuer name (RFC)
                'BKTXT' => 'WEB TRANSFER', #header text (RFC)
                'WEMPF' => $data_gi['header']->requester, #recipient (RFC)
                'CHARG' => '', #batch (RFC)
                'SGTXT' => '', #item description (RFC)
                'COKEY' => $cokey,
                'COIND' => $coind,
            ];
            $coind += 1;
        }

        !dd($data_upload);

        $sapRepository = new SapRepositoryAppsImpl();
        $sapResponse = $sapRepository->uploadGrPlant($data_upload);

        $document_number = ""; #no gi
        $document_posto = ""; #no po sto

        if ($sapResponse['status']) {
            $res_sap = $sapResponse['response'];

            $last_resp_sap = $res_sap[sizeof($res_sap) - 1];
            $status = true;

            if ($last_resp_sap['msgty'] == "S") {
                if (strtolower($last_resp_sap['stat1']) == "x" && strtolower($last_resp_sap['stat2']) == "x" && strtolower($last_resp_sap['stat3']) == "x" && $last_resp_sap['mblnr'] != "" && $last_resp_sap['ebeln'] != "") {
                    $document_number = $last_resp_sap['mblnr'];
                    $document_posto = $last_resp_sap['ebeln'];
                    $message = Lang::get("message.upload.success", ["data" => Lang::get("gi plant")]);
                } else {
                    if (isset($last_resp_sap['ebeln'])) {
                        if ($last_resp_sap['ebeln'] != "") {
                            $document_posto = $last_resp_sap['ebeln'];
                            $status = false;
                            $message = Lang::get("POSTO numbers have been created, but not GI numbers. Please resend a few minutes later");
                        }
                    }
                }
            } else {
                $status = false;
                if (isset($last_resp_sap['msgtx'])) {
                    $message = Lang::get("Feedback SAP") .  ' : '  . $last_resp_sap['msgtx'];
                } else {
                    $message = Lang::get("Sorry, an error occurred, please try again later");
                }
                if (isset($last_resp_sap['ebeln'])) {
                    if ($last_resp_sap['ebeln'] != "") {
                        $document_posto = $last_resp_sap['ebeln'];
                        $message = Lang::get("POSTO numbers have been created, but not GI numbers. Please resend a few minutes later");
                    }
                }
            }

            $giPlant = GiPlant::find($giPlantId);
            $giPlant->json_sap = json_encode($data_upload);
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
