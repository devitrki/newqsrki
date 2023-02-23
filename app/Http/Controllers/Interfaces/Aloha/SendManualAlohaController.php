<?php

namespace App\Http\Controllers\Interfaces\Aloha;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

use App\Jobs\Interfaces\Aloha\UploadSalesAloha;

use App\Library\Helper;
use App\Models\Plant;
use App\Models\Pos;
use App\Models\Pos\AlohaHistorySendSap;
use App\Models\Pos\AlohaTransactionLog;

use App\Repositories\SapRepositorySapImpl;
use Illuminate\Support\Facades\Date;

class SendManualAlohaController extends Controller
{
    public function index(Request $request)
    {
        $dataview = [
            'menu_id' => $request->query('menuid'),
        ];
        return view('interfaces.aloha.send-manual-aloha', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');
        $data = [];

        $posId = Pos::getIdByCode($userAuth->company_id_selected, 'aloha');
        $pos = Pos::find($posId);

        $posRepository = Pos::getInstanceRepo($pos);
        $initConnectionAloha = $posRepository->initConnectionDB();
        if ($initConnectionAloha['status']) {
            $plantCode = '';
            if( $request->query('plant-id') != '0'){
                $plantCode = Plant::getCustomerCodeById($request->query('plant-id'));
            }

            $stores = $posRepository->getStoreAloha($request->query('from'), $request->query('until'), $plantCode);

            foreach ($stores as $store) {
                $data[] = [
                    'plant' => Plant::getShortNameByCustCode($userAuth->company_id_selected, $store->SecondaryStoreID),
                    'code' => $store->SecondaryStoreID,
                    'status' => AlohaHistorySendSap::getStatusSendSap($store->DateOfBusiness, $store->SecondaryStoreID),
                    'date' => date("Y/m/d", strtotime($store->DateOfBusiness)),
                    'date_desc' => date("d/m/Y", strtotime($store->DateOfBusiness))
                ];
            }
        }

        return Datatables::of($data)->addIndexColumn()->make();
    }

    public function view(Request $request)
    {
        $userAuth = $request->get('userAuth');
        $plant = Plant::getPlantByCustCode($userAuth->company_id_selected, $request->query('customer-code'));
        $pos = Pos::find($plant->pos_id);
        $posData = [];

        $posRepository = Pos::getInstanceRepo($pos);
        $initConnectionAloha = $posRepository->initConnectionDB();
        if ($initConnectionAloha['status']) {
            $posData = $posRepository->getSalesFormatSAP($request->query('customer-code'), $request->query('date'));
        }

        $dataview = [
            'plant_name' => Plant::getShortNameById($plant->id),
            'customer_code' => $request->query('customer-code'),
            'pos' => $posData,
            'date' => $request->query('date')
        ];

        return view('interfaces.aloha.send-manual-aloha-view', $dataview);
    }

    public function store(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $stat = 'failed';
        $msg = 'Send Manual Failed';

        $plantSends = [];

        foreach (json_decode($request->data) as $data) {
            $plant = Plant::getPlantByCustCode($userAuth->company_id_selected, $data->customer_code);
            $pos = Pos::find($plant->pos_id);

            $posRepository = Pos::getInstanceRepo($pos);
            $initConnectionAloha = $posRepository->initConnectionDB();
            if ($initConnectionAloha['status']) {
                $statusComplete = $posRepository->checkCompleteStoreAloha($plant->customer_code, $data->date);
                if($statusComplete < 1){
                    $msg = "Cust Code " . $data->customer_code . " Date " . $data->date . " Not Yet Complete";
                    break;
                }

                $statusCorrection = $posRepository->checkCorrectionStoreAloha($data->customer_code, $data->date);
                if($statusCorrection > 0){
                    $msg = "Cust Code " . $data->customer_code . " Date " . $data->date . " Have Correction";
                    break;
                }

                $plantSends[] = [
                    'date' => $data->date,
                    'customer_code' => $data->customer_code
                ];

                $stat = 'success';
                $msg = 'Send SAP Succesfully In Queue';
            } else {
                $msg = 'Send Manual Failed, not connect database aloha';
                break;
            }
        }

        if( $stat != 'failed' ){

            foreach ($plantSends as $d) {

                // $this->send($userAuth->company_id_selected, $d['customer_code'], $d['date']);

                if (UploadSalesAloha::dispatch($userAuth->company_id_selected, $d['customer_code'], $d['date'])->onQueue('low')) {
                    $stat = 'success';
                    $msg = 'Send manual succesfully in queue';
                } else {
                    $stat = 'failed';
                    $msg = 'Error when send to queue';
                    break;
                }

            }

        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function send($companyId, $customerCode, $date)
    {
        $plant = Plant::getPlantByCustCode($companyId, $customerCode);
        $pos = Pos::find($plant->pos_id);

        $posRepository = Pos::getInstanceRepo($pos);
        $initConnectionAloha = $posRepository->initConnectionDB();
        if ($initConnectionAloha['status']) {
            $dataPos = $posRepository->getSalesFormatSAPMiddleware($customerCode, $date);
            $calc = $this->getSelisih($dataPos);
            $statusComplete = $posRepository->checkCompleteStoreAloha($customerCode, $date);
            $statusCorrection = $posRepository->checkCorrectionStoreAloha($customerCode, $date);

            if($statusComplete < 1){
                $alohaHistorySendSap = new AlohaHistorySendSap;
                $alohaHistorySendSap->company_id = $companyId;
                $alohaHistorySendSap->date = $date;
                $alohaHistorySendSap->plant_id = $plant->id;
                $alohaHistorySendSap->total_payments = $calc['total_payment'];
                $alohaHistorySendSap->total_sales = $calc['total_sales'];
                $alohaHistorySendSap->selisih = $calc['selisih'];
                $alohaHistorySendSap->description = 'Not Yet Complete';
                $alohaHistorySendSap->send = 0;
                $alohaHistorySendSap->save();
            } else if ($statusCorrection > 0) {

                $alohaHistorySendSap = new AlohaHistorySendSap;
                $alohaHistorySendSap->company_id = $companyId;
                $alohaHistorySendSap->date = $date;
                $alohaHistorySendSap->plant_id = $plant->id;
                $alohaHistorySendSap->total_payments = $calc['total_payment'];
                $alohaHistorySendSap->total_sales = $calc['total_sales'];
                $alohaHistorySendSap->selisih = $calc['selisih'];
                $alohaHistorySendSap->description = 'Have Correction';
                $alohaHistorySendSap->send = 0;
                $alohaHistorySendSap->save();

            } else if($calc['selisih'] > 2000){

                $alohaHistorySendSap = new AlohaHistorySendSap;
                $alohaHistorySendSap->company_id = $companyId;
                $alohaHistorySendSap->date = $date;
                $alohaHistorySendSap->plant_id = $plant->id;
                $alohaHistorySendSap->total_payments = $calc['total_payment'];
                $alohaHistorySendSap->total_sales = $calc['total_sales'];
                $alohaHistorySendSap->selisih = $calc['selisih'];
                $alohaHistorySendSap->description = 'Selisih > 2';
                $alohaHistorySendSap->send = 0;
                $alohaHistorySendSap->save();

            } else {
                $dateSap = Helper::DateConvertFormat($date, 'Y/m/d', 'Y-m-d');
                $dateNow = Date::now()->format('Y-m-d');

                $payloads = [
                    'outlet_id' => $customerCode,
                    'transaction_date' => $dateSap,
                    'payments' => $dataPos['payments'],
                    'sales' => $dataPos['sales'],
                    'inventories' => $dataPos['inventories'],
                ];

                $statusHistorySap = true;
                $messageHistorySap = "";

                $sapRepository = new SapRepositorySapImpl($companyId);
                $sapResponse = $sapRepository->uploadSales($payloads);
                if ($sapResponse['status']) {
                    $resSap = $sapResponse['response'];

                    if ($resSap['outlet_id']) {

                        $payloads = [
                            'outlet_id' => $customerCode,
                            'transaction_date' => $dateNow
                        ];

                        $logSapResponse = $sapRepository->getTransactionLog($payloads);
                        if ($logSapResponse['status']) {

                            $logRespSap = $logSapResponse['response'];
                            $logs = $logRespSap['logs'];
                            $lastTransactionLogs = [];
                            $fiFlag = false;
                            $mmFlag = false;
                            $sdFlag = false;
                            for ($i=sizeof($logs)-1; $i > 0; $i--) {
                                if ($logs[$i]['document_date'] == $dateSap && $logs[$i]['entry_date'] == $dateNow) {
                                    $lastTransactionLogs[] = $logs[$i];
                                    if ($logs[$i]['document_type'] == 'FI') {
                                        $fiFlag = true;
                                    }
                                    if ($logs[$i]['document_type'] == 'MM') {
                                        $mmFlag = true;
                                    }
                                    if ($logs[$i]['document_type'] == 'SD') {
                                        $sdFlag = true;
                                    }
                                }

                                if ($fiFlag && $mmFlag && $sdFlag) {
                                    break;
                                }
                            }

                            foreach ($lastTransactionLogs as $lastTransactionLog) {
                                $message = $lastTransactionLog['document_type'] . ' ' . $lastTransactionLog['status_code'] . ' ' . $lastTransactionLog['message'];

                                $alohaTransactionLog = new AlohaTransactionLog;
                                $alohaTransactionLog->company_id = $companyId;
                                $alohaTransactionLog->type = 1;
                                $alohaTransactionLog->status = $lastTransactionLog['status_code'];
                                $alohaTransactionLog->message = $message;
                                $alohaTransactionLog->closing_date = $date;
                                $alohaTransactionLog->plant_id = $plant->id;
                                if ($alohaTransactionLog->save()) {
                                    $statusHistorySap = true;
                                    $messageHistorySap = "Success send to SAP";
                                } else {
                                    $statusHistorySap = false;
                                    $messageHistorySap = "Save aloha transaction log error";
                                    break;
                                }
                            }

                        } else {
                            // adding message error
                            $alohaTransactionLog = new AlohaTransactionLog;
                            $alohaTransactionLog->company_id = $companyId;
                            $alohaTransactionLog->type = 0;
                            $alohaTransactionLog->status = 'E';
                            $alohaTransactionLog->message = json_encode($logSapResponse['errors']);
                            $alohaTransactionLog->closing_date = $date;
                            $alohaTransactionLog->plant_id = $plant->id;
                            if ($alohaTransactionLog->save()) {
                                $statusHistorySap = false;
                            } else {
                                $statusHistorySap = false;
                                $messageHistorySap = "Save aloha error transaction log error";
                            }
                        }

                    } else {
                        // adding message error
                        $alohaTransactionLog = new AlohaTransactionLog;
                        $alohaTransactionLog->company_id = $companyId;
                        $alohaTransactionLog->type = 0;
                        $alohaTransactionLog->status = 'E';
                        $alohaTransactionLog->message = json_encode($resSap['errors']);
                        $alohaTransactionLog->closing_date = $date;
                        $alohaTransactionLog->plant_id = $plant->id;
                        if ($alohaTransactionLog->save()) {
                            $statusHistorySap = false;
                        } else {
                            $statusHistorySap = false;
                            $messageHistorySap = "Save aloha error transaction daily error";
                        }
                    }

                } else {
                    $statusHistorySap = false;
                    $messageHistorySap = $sapResponse['response'];
                }

                // insert to history send sales report
                $alohaHistorySendSap = new AlohaHistorySendSap;
                $alohaHistorySendSap->company_id = $companyId;
                $alohaHistorySendSap->date = $date;
                $alohaHistorySendSap->plant_id = $plant->id;
                $alohaHistorySendSap->total_payments = $calc['total_payment'];
                $alohaHistorySendSap->total_sales = $calc['total_sales'];
                $alohaHistorySendSap->selisih = $calc['selisih'];
                $alohaHistorySendSap->description = $messageHistorySap;
                if ($statusHistorySap) {
                    $alohaHistorySendSap->send = 1;
                } else {
                    $alohaHistorySendSap->send = 0;
                }
                $alohaHistorySendSap->save();
            }

        } else {
            $alohaHistorySendSap = new AlohaHistorySendSap;
            $alohaHistorySendSap->company_id = $companyId;
            $alohaHistorySendSap->date = $date;
            $alohaHistorySendSap->plant_id = $plant->id;
            $alohaHistorySendSap->total_payments = 0;
            $alohaHistorySendSap->total_sales = 0;
            $alohaHistorySendSap->selisih = 0;
            $alohaHistorySendSap->description = 'Not connect database aloha';
            $alohaHistorySendSap->send = 0;
            $alohaHistorySendSap->save();
        }

        !dd("end");
    }

    public function getSelisih($data)
    {
        $total_payments = 0;
        $total_sales = 0;

        if( sizeof($data['payments']) > 0 ){
            for ($i=0; $i < sizeof($data['payments']); $i++) {
                $total_payments += $data['payments'][$i]['amount'];
            }
        }

        if( sizeof($data['sales']) > 0 ){
            for ($i=0; $i < sizeof($data['sales']); $i++) {
                if ( in_array( $data['sales'][$i]['material_id'], ['9999998', '9999997', '9999995'] ) ) {
                    continue;
                }
                $total_sales += $data['sales'][$i]['gross'];
            }
        }

        return [
            'total_payment' => $total_payments,
            'total_sales' => $total_sales,
            'selisih' => abs($total_payments - $total_sales)
        ];

    }
}
