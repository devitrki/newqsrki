<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

use App\Models\Plant;
use App\Models\Financeacc\Pettycash;

use App\Repositories\SapRepositoryAppsImpl;

class PettycashServiceAppsImpl implements PettycashService
{
    public function uploadPettyCash($companyId, $picFa, $receiveDate, $idSubmiteds)
    {
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
                        $dataSubmited[$index_detail]['AMANT'] = $dataSubmited[$index_detail]['AMANT'] + $transSubmit->debit;
                        $dataSubmited[$index_detail]['TAXTT'] = $dataSubmited[$index_detail]['TAXTT'] . ',' . strtolower( $transSubmit->description);
                    } else {
                        $dataSubmited[$i_det] = [
                            'WSTYP' => 'I',
                            'HKONT' => $transSubmit->gl_code,
                            'SHKZG' => ($transSubmit->type == '0') ? 'S' : 'H',
                            'AMANT' => $transSubmit->debit,
                            'ASIGN' => Plant::getShortNameById($transSubmit->plant_id),
                            'TAXTT' => strtolower( $transSubmit->description)
                        ];
                        $gl_temp[] = ['i' => $i_det, 'gl_code' => $transSubmit->gl_code];
                    }
                } else{
                    // kredit
                    if ($index_detail != 9999) {
                        $dataSubmited[$index_detail]['AMANT'] = $dataSubmited[$index_detail]['AMANT'] + $transSubmit->kredit;
                        $dataSubmited[$index_detail]['TAXTT'] = $dataSubmited[$index_detail]['TAXTT'] . ',' . strtolower( $transSubmit->description);
                    } else {
                        $ccPlant = Plant::getCostCenterById($transSubmit->plant_id);
                        $dataSubmited[$i_det] = [
                            'WSTYP' => 'I',
                            'HKONT' => $transSubmit->gl_code,
                            'SHKZG' => ($transSubmit->type == '0') ? 'S' : 'H',
                            'AMANT' => $transSubmit->kredit,
                            'ASIGN' => Plant::getShortNameById($transSubmit->plant_id),
                            'TAXTT' => strtolower( $transSubmit->description),
                            'KOSTL' => Pettycash::getCCSAPFixed($companyId, $transSubmit->gl_code, $ccPlant, $transSubmit->plant_id),
                            'TAXCD' => 'v0',
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

        if( sizeof($dataSubmited) > 0  ){
            // upload to SAP
            if ($vendor == "") {
                $vendor = "700000";
            }

            $plantShortName = Plant::getShortNameById($plantId, false);
            $desc_trans = Pettycash::getDescToSAP($unique_items, $plantShortName, $plantId);
            $ref = Plant::getShortNameById($plantId);

            $header[] = [
                'PTOPR' => 'K',
                'WSTYP' => 'H',
                'BUKRS' => 'RKI', // change to sap code company
                'LIFNR' => $vendor,
                'BLDAT' => date("d.m.Y", strtotime($receiveDate)),
                'BUDAT' => date("d.m.Y"),
                'REFFR' => $ref,
                'AMONT' => strval($total),
                'TEXTT' => $desc_trans,
            ];

            $data_posted = array_merge($header, $dataSubmited);

            $sapRepository = new SapRepositoryAppsImpl();
            $sapResponse = $sapRepository->uploadPettyCash($data_posted);

            if ($sapResponse['status']) {
                $resSap = $sapResponse['response'];
                $return = $resSap[sizeof($resSap) - 1];

                $document_number = '';
                $status = true;

                if (is_array($return)) {
                    $last_error = $return;

                    if (substr($last_error['msg'], 0, 8) == 'Document') {
                        $return          = explode(' ', $last_error['msg']);
                        $document_number = $return[1];
                    } else {
                        if( isset($return['msg']) ){
                            $status = false;
                            $message = 'Feedback SAP : ' . $return['msg'];
                        } else {
                            $errors = [];
                            foreach ($return as $error) {
                                $errors[] = $error['msg'];
                            }
                            if ($errors){
                                $status = false;
                                $message = 'Feedback SAP : ' . $errors[sizeof($errors)-1];
                            }
                        }

                    }
                } elseif (substr($return, -15, -11) == 'doc.') {
                    $return          = explode(' ', $return);
                    $document_number = $return[10];
                } elseif (substr($return, -26, -1) == 'posted without difference') {
                    $document_number = 'No Difference';
                } else {
                    $status = false;
                    $message = 'Feedback SAP : ' . $return;
                }

                if($status){
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
