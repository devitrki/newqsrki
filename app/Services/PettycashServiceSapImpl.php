<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

use App\Models\Plant;
use App\Models\Company;
use App\Models\Configuration;
use App\Models\Financeacc\Pettycash;

use App\Library\Helper;

use App\Repositories\SapRepositorySapImpl;
use App\Entities\SapMiddleware;

class PettycashServiceSapImpl implements PettycashService
{
    public function uploadPettyCash($companyId, $picFa, $receiveDate, $idSubmiteds)
    {
        $sapCodeComp = Company::getConfigByKey($companyId, 'SAP_CODE');
        if (!$sapCodeComp || $sapCodeComp == '') {
            return [
                "status" => false,
                "message" => Lang::get("Please set SAP_CODE in company configuration")
            ];
        }

        $currencyComp = Company::getConfigByKey($companyId, 'CURRENCY');
        if (!$currencyComp || $currencyComp == '') {
            return [
                "status" => false,
                "message" => Lang::get("Please set CURRENCY in company configuration")
            ];
        }

        $status = true;
        $message = Lang::get('Successfully Posted');

        $dataSubmited = [];
        $unique_items = [];
        $gl_temp = [];
        $i_det = 0;
        $total = 0;

        $transSubmitFirst = DB::table('pettycashes')
                                ->where('id', $idSubmiteds[0])
                                ->first();

        $plantId = $transSubmitFirst->plant_id;
        $vendor = Pettycash::getVendorSAPFixed($companyId, $transSubmitFirst->plant_id);

        foreach ($idSubmiteds as $idSubmited) {
            $transSubmit = DB::table('pettycashes')
                            ->where('id', $idSubmited)
                            ->first();

            // check type
            if( $transSubmit->type != 2 ){
                // kredit / debit
                $total = $total + ( $transSubmit->kredit + (-$transSubmit->debit) );
                $index_detail = Pettycash::getIndexGlTemp($gl_temp, $transSubmit->gl_code);

                if( $transSubmit->type != 0 ){
                    // debit
                    if ($index_detail != 9999) {
                        $dataSubmited[$index_detail]['gl_amount'] = $dataSubmited[$index_detail]['gl_amount'] + (int)$transSubmit->debit;
                        $dataSubmited[$index_detail]['item_text'] = $dataSubmited[$index_detail]['item_text'] . ',' . strtolower( $transSubmit->description);
                    } else {
                        $dataSubmited[$i_det] = [
                            'gl_account' => $transSubmit->gl_code,
                            'is_debit' => ($transSubmit->type == '0') ? true : false,
                            'gl_amount' => (int)$transSubmit->debit,
                            'tax_code' => '', // please confirm to new interface
                            'assignment' => Plant::getShortNameById($transSubmit->plant_id),
                            'item_text' => strtolower($transSubmit->description),
                            'cost_center' => '', // please confirm to new interface
                        ];
                        $gl_temp[] = ['i' => $i_det, 'gl_code' => $transSubmit->gl_code];
                    }
                } else{
                    // kredit
                    if ($index_detail != 9999) {
                        $dataSubmited[$index_detail]['gl_amount'] = $dataSubmited[$index_detail]['gl_amount'] + (int)$transSubmit->kredit;
                        $dataSubmited[$index_detail]['item_text'] = $dataSubmited[$index_detail]['item_text'] . ',' . strtolower( $transSubmit->description);
                    } else {
                        $ccPlant = Plant::getCostCenterById($transSubmit->plant_id);
                        $dataSubmited[$i_det] = [
                            'gl_account' => $transSubmit->gl_code,
                            'is_debit' => ($transSubmit->type == '0') ? true : false,
                            'gl_amount' => (int)$transSubmit->kredit,
                            'tax_code' => Configuration::getValueCompByKeyFor($companyId, 'financeacc', 'tax_code'),
                            'assignment' => Plant::getShortNameById($transSubmit->plant_id),
                            'item_text' => strtolower($transSubmit->description),
                            'cost_center' => Pettycash::getCCSAPFixed($companyId, $transSubmit->gl_code, $ccPlant, $transSubmit->plant_id),
                        ];
                        $gl_temp[] = ['i' => $i_det, 'gl_code' => $transSubmit->gl_code];
                    }
                }

                $i_det += 1;

                if (!in_array($transSubmit->id, $unique_items)) {
                    $unique_items[] = $transSubmit->id;
                }
            } else {
                // kredit by po
                $pettycash = Pettycash::find($idSubmited);
                $pettycash->receive_pic = $picFa;
                $pettycash->receive_date = $receiveDate;
                $pettycash->submited_at = date('Y-m-d H:i:s');
                $pettycash->submit = 1;
                if ($pettycash->save()) {
                    $status = true;
                } else {
                    $status = false;
                }
            }
        }

        if(sizeof($dataSubmited) > 0){
            // upload to SAP
            if ($vendor == "") {
                $vendor = Configuration::getValueCompByKeyFor($companyId, 'financeacc', 'vendor_id_outlet');
            }

            $plantShortName = Plant::getShortNameById($plantId, false);
            $desc_trans = Pettycash::getDescToSAP($unique_items, $plantShortName, $plantId);
            $ref = Plant::getShortNameById($plantId);

            $documentKey = Helper::getKeySap();

            $data_posted = [
                'company_id' => $sapCodeComp, // change to sap code company
                'document_key' => $documentKey,
                'vendor_id' => $vendor,
                'transaction_type' => 'K',
                'invoice_date' => date("Y-m-d", strtotime($receiveDate)),
                'posting_date' => date("Y-m-d"),
                'reference' => $ref,
                'invoice_amount' => $total,
                'currency_id' => $currencyComp,
                'header_text' => $desc_trans,
                'items' => $dataSubmited
            ];

            $sapRepository = new SapRepositorySapImpl($companyId);
            $sapResponse = $sapRepository->uploadPettyCash($data_posted);
            if ($sapResponse['status']) {
                $resSap = $sapResponse['response'];

                $document_number = '';
                $status = true;

                if ($resSap['success']) {
                    $document_number = $resSap['document_number'];

                    foreach ($idSubmiteds as $idSubmited) {
                        $pettycash = Pettycash::find($idSubmited);
                        $pettycash->document_number = $document_number;
                        $pettycash->receive_pic = $picFa;
                        $pettycash->receive_date = $receiveDate;
                        $pettycash->submited_at = date('Y-m-d H:i:s');
                        $pettycash->submit = 1;
                        $pettycash->save();
                    }

                    $message = Lang::get('Successfully Posted');
                } else {
                    $status = false;
                    $message = SapMiddleware::getLastErrorMessage($resSap['logs']);
                }

            } else {
                $status = false;
                $message = Lang::get("Sorry, an error occurred, please try again later");
            }
        }

        return [
            "status" => $status,
            "message" => $message
        ];
    }
}
