<?php

namespace App\Jobs\Interfaces\Aloha;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;

use App\Models\Pos\AlohaHistorySendSap;
use App\Models\Pos\AlohaTransactionLog;
use App\Models\Plant;
use App\Models\Pos;

use App\Repositories\SapRepositorySapImpl;

use App\Library\Helper;

class UploadSalesAloha implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $companyId;
    protected $customerCode;
    protected $date;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($companyId, $customerCode, $date)
    {
        $this->companyId = $companyId;
        $this->customerCode = $customerCode;
        $this->date = $date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $plant = Plant::getPlantByCustCode($this->companyId, $this->customerCode);
        $pos = Pos::find($plant->pos_id);

        $posRepository = Pos::getInstanceRepo($pos);
        $initConnectionAloha = $posRepository->initConnectionDB();
        if ($initConnectionAloha['status']) {

            try {
                $dataPos = $posRepository->getSalesFormatSAPMiddleware($this->customerCode, $this->date);
                $calc = $this->getSelisih($dataPos);
                $statusComplete = $posRepository->checkCompleteStoreAloha($this->customerCode, $this->date);
                $statusCorrection = $posRepository->checkCorrectionStoreAloha($this->customerCode, $this->date);

                if($statusComplete < 1){
                    $alohaHistorySendSap = new AlohaHistorySendSap;
                    $alohaHistorySendSap->company_id = $this->companyId;
                    $alohaHistorySendSap->date = $this->date;
                    $alohaHistorySendSap->plant_id = $plant->id;
                    $alohaHistorySendSap->total_payments = $calc['total_payment'];
                    $alohaHistorySendSap->total_sales = $calc['total_sales'];
                    $alohaHistorySendSap->selisih = $calc['selisih'];
                    $alohaHistorySendSap->description = 'Not Yet Complete';
                    $alohaHistorySendSap->send = 0;
                    $alohaHistorySendSap->save();
                } else if ($statusCorrection > 0) {

                    $alohaHistorySendSap = new AlohaHistorySendSap;
                    $alohaHistorySendSap->company_id = $this->companyId;
                    $alohaHistorySendSap->date = $this->date;
                    $alohaHistorySendSap->plant_id = $plant->id;
                    $alohaHistorySendSap->total_payments = $calc['total_payment'];
                    $alohaHistorySendSap->total_sales = $calc['total_sales'];
                    $alohaHistorySendSap->selisih = $calc['selisih'];
                    $alohaHistorySendSap->description = 'Have Correction';
                    $alohaHistorySendSap->send = 0;
                    $alohaHistorySendSap->save();

                } else if($calc['selisih'] > 2){

                    $alohaHistorySendSap = new AlohaHistorySendSap;
                    $alohaHistorySendSap->company_id = $this->companyId;
                    $alohaHistorySendSap->date = $this->date;
                    $alohaHistorySendSap->plant_id = $plant->id;
                    $alohaHistorySendSap->total_payments = $calc['total_payment'];
                    $alohaHistorySendSap->total_sales = $calc['total_sales'];
                    $alohaHistorySendSap->selisih = $calc['selisih'];
                    $alohaHistorySendSap->description = 'Selisih > 2';
                    $alohaHistorySendSap->send = 0;
                    $alohaHistorySendSap->save();

                } else {
                    $dateSap = Helper::DateConvertFormat($this->date, 'Y/m/d', 'Y-m-d');
                    $dateNow = Date::now()->format('Y-m-d');

                    $payloads = [
                        'outlet_id' => $this->customerCode,
                        'transaction_date' => $dateSap,
                        'payments' => $dataPos['payments'],
                        'sales' => $dataPos['sales'],
                        'inventories' => $dataPos['inventories'],
                    ];

                    $statusHistorySap = true;
                    $messageHistorySap = "";

                    $sapRepository = new SapRepositorySapImpl($this->companyId);
                    $sapResponse = $sapRepository->uploadSales($payloads);
                    if ($sapResponse['status']) {
                        $resSap = $sapResponse['response'];

                        if ($resSap['outlet_id']) {

                            $payloads = [
                                'outlet_id' => $this->customerCode,
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

                                if (sizeof($lastTransactionLogs) > 0) {
                                    foreach ($lastTransactionLogs as $lastTransactionLog) {
                                        $message = $lastTransactionLog['document_type'] . ' ' . $lastTransactionLog['status_code'] . ' ' . $lastTransactionLog['message'];

                                        $alohaTransactionLog = new AlohaTransactionLog;
                                        $alohaTransactionLog->company_id = $this->companyId;
                                        $alohaTransactionLog->type = 1;
                                        $alohaTransactionLog->status = $lastTransactionLog['status_code'];
                                        $alohaTransactionLog->message = $message;
                                        $alohaTransactionLog->closing_date = $this->date;
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
                                    Log::info(json_encode($logRespSap));

                                    $statusHistorySap = false;
                                    $messageHistorySap = "Transaction log not found from response middleware";
                                }

                            } else {
                                // adding message error
                                $alohaTransactionLog = new AlohaTransactionLog;
                                $alohaTransactionLog->company_id = $this->companyId;
                                $alohaTransactionLog->type = 0;
                                $alohaTransactionLog->status = 'E';
                                $alohaTransactionLog->message = json_encode($logSapResponse['errors']);
                                $alohaTransactionLog->closing_date = $this->date;
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
                            $alohaTransactionLog->company_id = $this->companyId;
                            $alohaTransactionLog->type = 0;
                            $alohaTransactionLog->status = 'E';
                            $alohaTransactionLog->message = (isset($resSap['errors'])) ? json_encode($resSap['errors']) : "Error from middleware";
                            $alohaTransactionLog->closing_date = $this->date;
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
                    $alohaHistorySendSap->company_id = $this->companyId;
                    $alohaHistorySendSap->date = $this->date;
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

            } catch (\Throwable $th) {
                Log::error("Send Aloha SAP: " . $th->getMessage());

                $alohaHistorySendSap = new AlohaHistorySendSap;
                $alohaHistorySendSap->company_id = $this->companyId;
                $alohaHistorySendSap->date = $this->date;
                $alohaHistorySendSap->plant_id = $plant->id;
                $alohaHistorySendSap->total_payments = 0;
                $alohaHistorySendSap->total_sales = 0;
                $alohaHistorySendSap->selisih = 0;
                $alohaHistorySendSap->description = 'Timeout database aloha';
                $alohaHistorySendSap->send = 0;
                $alohaHistorySendSap->save();
            }

        } else {
            $alohaHistorySendSap = new AlohaHistorySendSap;
            $alohaHistorySendSap->company_id = $this->companyId;
            $alohaHistorySendSap->date = $this->date;
            $alohaHistorySendSap->plant_id = $plant->id;
            $alohaHistorySendSap->total_payments = 0;
            $alohaHistorySendSap->total_sales = 0;
            $alohaHistorySendSap->selisih = 0;
            $alohaHistorySendSap->description = 'Not connect database aloha';
            $alohaHistorySendSap->send = 0;
            $alohaHistorySendSap->save();
        }
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
