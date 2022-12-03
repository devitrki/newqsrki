<?php

namespace App\Jobs\Interfaces\Aloha;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

use App\Models\Pos\AlohaInterface;
use App\Models\Interfaces\AlohaHistorySendSap;
use App\Models\Interfaces\AlohaTransactionLog;
use App\Models\Plant;
use App\Models\Pos\Aloha;

use App\Library\Helper;

class UploadSalesAloha implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $customerCode;
    protected $date;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($customerCode, $date)
    {
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
        $dateInterface = Helper::DateConvertFormat($this->date, 'Y/m/d', 'd/m/Y');
        $plantId = Plant::getIdByCustomerCode($this->customerCode);
        $dataPos = AlohaInterface::getSalesFormatSAP($this->customerCode, $dateInterface);
        $calc = $this->getSelisih($dataPos);
        $statusComplete = Aloha::checkCompleteStoreAloha($this->customerCode, $this->date);
        $statusCorrection = Aloha::checkCorrectionStoreAloha($this->customerCode, $this->date);

        if($statusComplete < 1){

            $alohaHistorySendSap = new AlohaHistorySendSap;
            $alohaHistorySendSap->date = $this->date;
            $alohaHistorySendSap->plant_id = $plantId;
            $alohaHistorySendSap->total_payments = $calc['total_payment'];
            $alohaHistorySendSap->total_sales = $calc['total_sales'];
            $alohaHistorySendSap->selisih = $calc['selisih'];
            $alohaHistorySendSap->description = 'Not Yet Complete';
            $alohaHistorySendSap->send = 0;
            $alohaHistorySendSap->save();

        } else if ($statusCorrection > 0) {

            $alohaHistorySendSap = new AlohaHistorySendSap;
            $alohaHistorySendSap->date = $this->date;
            $alohaHistorySendSap->plant_id = $plantId;
            $alohaHistorySendSap->total_payments = $calc['total_payment'];
            $alohaHistorySendSap->total_sales = $calc['total_sales'];
            $alohaHistorySendSap->selisih = $calc['selisih'];
            $alohaHistorySendSap->description = 'Have Correction';
            $alohaHistorySendSap->send = 0;
            $alohaHistorySendSap->save();

        } else if($calc['selisih'] > 2000){

            $alohaHistorySendSap = new AlohaHistorySendSap;
            $alohaHistorySendSap->date = $this->date;
            $alohaHistorySendSap->plant_id = $plantId;
            $alohaHistorySendSap->total_payments = $calc['total_payment'];
            $alohaHistorySendSap->total_sales = $calc['total_sales'];
            $alohaHistorySendSap->selisih = $calc['selisih'];
            $alohaHistorySendSap->description = 'Selisih > 2000';
            $alohaHistorySendSap->send = 0;
            $alohaHistorySendSap->save();

        } else {

            $pos = [
                'payment' => json_encode($dataPos['payment']),
                'sales' => json_encode($dataPos['sales']),
                'inventory' => json_encode($dataPos['inventory']),
            ];

            $url = config('qsrki.api.apps.url') . 'recheese/daily-sales/sap/sales/upload';

            // posting to SAP
            $response = Http::asForm()->post($url, [
                'pos' => $pos,
            ]);

            $statusHistorySap = true;
            $messageHistorySap = "";

            if ($response->ok()) {
                $res_sap = $response->json();

                if ($res_sap['success']) {

                    // insert to history return sap report
                    foreach ($res_sap['data'] as $res) {
                        $message = $res['BLART'] . ' ' . $res['CODE'] . ' ' . $res['MESSAGE'];

                        $alohaTransactionLog = new AlohaTransactionLog;
                        $alohaTransactionLog->type = 1;
                        $alohaTransactionLog->status = $res['CODE'];
                        $alohaTransactionLog->message = $message;
                        $alohaTransactionLog->closing_date = $this->date;
                        $alohaTransactionLog->plant_id = $plantId;
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
                    $alohaTransactionLog->type = 0;
                    $alohaTransactionLog->status = 'E';
                    $alohaTransactionLog->message = $res_sap['message'];
                    $alohaTransactionLog->closing_date = $this->date;
                    $alohaTransactionLog->plant_id = $plantId;
                    if ($alohaTransactionLog->save()) {
                        $statusHistorySap = false;
                    } else {
                        $statusHistorySap = false;
                        $messageHistorySap = "Save aloha error transaction log error";
                    }
                }
            } else {
                // error send sap
                $statusHistorySap = false;
                $messageHistorySap = "Error send sales to API SAP";
            }

            // insert to history send sales report
            $alohaHistorySendSap = new AlohaHistorySendSap;
            $alohaHistorySendSap->date = $this->date;
            $alohaHistorySendSap->plant_id = $plantId;
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
    }

    public function getSelisih($data)
    {
        $total_payments = 0;
        $total_sales = 0;

        if( sizeof($data['payment']) > 0 ){
            for ($i=0; $i < sizeof($data['payment']); $i++) {
                $total_payments += $data['payment'][$i]['COL06'];
            }
        }

        if( sizeof($data['sales']) > 0 ){
            for ($i=0; $i < sizeof($data['sales']); $i++) {
                if ( in_array( $data['sales'][$i]['COL06'], ['9999998', '9999997', '9999995'] ) ) {
                    continue;
                }
                $total_sales += $data['sales'][$i]['COL08'];
            }
        }

        return [
            'total_payment' => $total_payments,
            'total_sales' => $total_sales,
            'selisih' => abs($total_payments - $total_sales)
        ];

    }
}
