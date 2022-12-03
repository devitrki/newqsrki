<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

use App\Models\Plant;
use App\Models\Inventory\Waste;

use App\Repositories\SapRepositoryAppsImpl;

class WasteServiceAppsImpl implements WasteService
{
    public function uploadWaste($companyId, $wasteId)
    {
        $status = true;
        $message = Lang::get("message.submit.success", ["data" => Lang::get("waste")]);

        $waste = DB::table('wastes')
                    ->where('id', $wasteId)
                    ->first();

        $plantCustCode = Plant::getCustomerCodeById($waste->plant_id);
        $wasteItems  = DB::table('waste_items')->where('waste_id', $wasteId)->get();

        // upload to SAP
        $dataUpload = [];
        foreach ($wasteItems as $item) {

            $material = DB::table('material_outlets')
                            ->where('company_id', $companyId)
                            ->where('code', $item->material_code)
                            ->select('waste_flag')
                            ->first();

            if( $material->waste_flag != 0 ){
                $flag = 'x';
            } else {
                $flag = '';
            }

            $dataUpload[] = [
                'col01' => date('d.m.Y', strtotime($waste->date)), #tgl scrap
                'col02' => '551', #MOVEMENT TYPE selalu 551
                'col03' => $plantCustCode, #customer code
                'col04' => $item->material_code,
                'col05' => $item->qty, #qty
                'col06' => $item->uom, #uom
                'col07' => $flag
            ];

        }

        $dataUpload = [
            'items' => json_encode($dataUpload)
        ];

        !dd($dataUpload);

        $sapRepository = new SapRepositoryAppsImpl();
        $sapResponse = $sapRepository->uploadWaste($dataUpload);

        if ($sapResponse['status']) {
            $res_sap = $sapResponse['response'];

            if( $res_sap['success'] == 'true' ){

                $return = $res_sap['data'];

                if (is_array($return)) {
                    //Error / Warning dan sukses bisa dalam dalam array return
                    //No. DOC selalu ada direturn array terakhir dengan strpos 'posted'
                    $last_error = $return[sizeof($return) - 1];
                    if (substr($last_error['MESSAGE'], 0, 8) == 'Document') {
                        $return          = explode(' ', $last_error['MESSAGE']);
                        $document_number = $return[1];
                    } else {
                        $errors = [];
                        foreach ($return as $error) {
                            $errors[] = $error['MESSAGE'];
                        }

                        if ($errors){
                            $status = false;
                            $message = Lang::get("Feedback SAP") . ' : ' . implode(' <br/> ', $errors);
                        }
                    }
                } elseif (substr($return, 0, 8) == 'Document') {
                    //Jika sukses returnnya string
                    $message          = explode(' ', $return);
                    $document_number = $message[1];
                } else {
                    $status = false;
                    $message = Lang::get("Feedback SAP") . ' : ' . $return;
                }

                if( $document_number != '' ){

                    DB::BeginTransaction();

                    $waste = Waste::find($wasteId);
                    $waste->document_number = $document_number;
                    $waste->submit = 1;
                    $waste->posting_date = date('Y-m-d H:i:s');
                    $waste->save();

                    DB::commit();
                    $status = true;
                    $message = Lang::get("message.submit.success", ["data" => Lang::get("waste")]);
                } else {
                    $status = false;
                }

            } else {
                $status = false;
                $message = Lang::get("message.submit.failed", ["data" => Lang::get("waste")]);
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
