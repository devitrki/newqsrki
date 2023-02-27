<?php

namespace App\Jobs\ExternalVendor;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;

use App\Library\Helper;

use App\Exports\ExternalVendor\GenerateTransactionSales;
use App\Repositories\AlohaRepository;

use App\Models\Plant;
use App\Models\Company;
use App\Models\ExternalVendor\SendVendor;
use App\Models\ExternalVendor\HistorySendVendor;
use App\Models\ExternalVendor\TargetVendor;

class SendTransactionVendor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $date;
    public $sendVendorId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($date, $sendVendorId)
    {
        $this->date = $date;
        $this->sendVendorId = $sendVendorId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sendVendor = DB::table('send_vendors')
                        ->leftJoin('plants', 'plants.id', '=', 'send_vendors.plant_id')
                        ->leftJoin('target_vendors', 'target_vendors.id', '=', 'send_vendors.target_vendor_id')
                        ->where('send_vendors.id', $this->sendVendorId)
                        ->select(
                            'send_vendors.id',
                            'send_vendors.company_id',
                            'send_vendors.plant_id',
                            'send_vendors.prefix_name_store',
                            'send_vendors.template_sale_id',
                            'send_vendors.target_vendor_id',
                            'target_vendors.transfer_type',
                        )
                        ->first();

        $plant = DB::table('plants')
                    ->where('id', $sendVendor->plant_id)
                    ->first();

        $plantPosId = Plant::getPosById($sendVendor->plant_id);

        // check transaction complete / not complete
        $alohaRepository = new AlohaRepository($plantPosId);
        $initConnectionAloha = $alohaRepository->initConnectionDB();
        if ($initConnectionAloha['status']) {
            $checkTransComplete = $alohaRepository->checkCompleteStoreAloha($plant->customer_code, $this->date);
            if ($checkTransComplete != 0) {
                $this->sendTransaction($plantPosId, $this->date, $plant->customer_code, $sendVendor);
            } else {
                HistorySendVendor::addHistoryFailed($sendVendor->company_id, $this->date, $sendVendor->id, 'Transaction not complete');
            }
        } else {
            HistorySendVendor::addHistoryFailed($sendVendor->company_id, $this->date, $sendVendor->id, 'Init connection aloha failed: '. $initConnectionAloha['message']);
        }
    }

    public function sendTransaction($plantPosId, $date, $customerCode, $sendVendor)
    {
        $alohaRepository = new AlohaRepository($plantPosId);
        $initConnectionAloha = $alohaRepository->initConnectionDB();
        if ($initConnectionAloha['status']) {
            $transactions = $alohaRepository->getTransactionReceiptNumber($customerCode, $date);
            if (sizeof($transactions) > 0) {
                $taxPercent = Company::getConfigByKey($sendVendor->company_id, 'TAX_PERCENT');
                $transactionTemplateFormats = SendVendor::generateFormatTransaction($sendVendor->template_sale_id, $transactions, (float)$taxPercent);

                if ($sendVendor->transfer_type == '1utama_api') {
                    $result = $this->sendWith1UtamaApi($transactionTemplateFormats['datas'], $sendVendor->target_vendor_id);
                    if ($result['status']) {
                        $historySendVendor = new HistorySendVendor;
                        $historySendVendor->company_id = $sendVendor->company_id;
                        $historySendVendor->date = $this->date;
                        $historySendVendor->send_vendor_id = $sendVendor->id;
                        $historySendVendor->amount = $transactionTemplateFormats['total'];
                        $historySendVendor->status = 1;
                        $historySendVendor->description = '';
                        $historySendVendor->save();
                    } else {
                        HistorySendVendor::addHistoryFailed($sendVendor->company_id, $this->date, $sendVendor->id, $result['message']);
                    }
                } else {
                    HistorySendVendor::addHistoryFailed($sendVendor->company_id, $this->date, $sendVendor->id, "upload ftp");
                }

            } else {
                HistorySendVendor::addHistoryFailed($sendVendor->company_id, $this->date, $sendVendor->id, 'Transactions zero');
            }
        } else {
            HistorySendVendor::addHistoryFailed($sendVendor->company_id, $this->date, $sendVendor->id, 'Init connection aloha failed: '. $initConnectionAloha['message']);
        }
    }

    public function sendWith1UtamaApi($transactions, $targetVendorId)
    {
        $status = false;
        $message = '';

        $host = TargetVendor::getConfigByKey($targetVendorId, 'HOST');
        $authenticationKey = TargetVendor::getConfigByKey($targetVendorId, 'AUTHENTICATION_KEY');

        if (!$host) {
            return [
                'status' => $status,
                'message' => 'Setting config HOST first.'
            ];
        }

        if (!$authenticationKey) {
            return [
                'status' => $status,
                'message' => 'Setting config AUTHENTICATION_KEY first.'
            ];
        }

        $headers = [
            'Content-type' => 'text/json',
            'Authorization' => 'Basic ' . $authenticationKey
        ];

        $url = $host . '/POS/POSService.svc/SendReceipts';

        $res = Http::withHeaders($headers)
                ->put($url, $transactions);

        if ($res->ok()) {
            $status = true;
        } else {
            $message = json_encode([
                $res->status(),
                $res->json()
            ]);
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

}
