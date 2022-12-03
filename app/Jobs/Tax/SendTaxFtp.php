<?php

namespace App\Jobs\Tax;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Library\Helper;
use App\Models\Tax\SendTax;
use App\Models\Tax\HistorySendTax;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Tax\GenerateTransaction;

use App\Models\Plant;

class SendTaxFtp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $date;
    public $send_tax_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($date, $send_tax_id)
    {
        $this->date = $date;
        $this->send_tax_id = $send_tax_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sendTax = DB::table('send_taxes')
                        ->leftJoin('ftp_governments', 'ftp_governments.id', '=', 'send_taxes.ftp_government_id')
                        ->leftJoin('plants', 'plants.id', '=', 'send_taxes.plant_id')
                        ->where('send_taxes.id', $this->send_tax_id)
                        ->select(
                            'send_taxes.id',
                            'send_taxes.plant_id',
                            'ftp_governments.transfer_type',
                            'send_taxes.prefix_name_store',
                        )
                        ->first();

        // check transaction complete / not complete
        $urlComplete = config('qsrki.api.aloha.url') . config('qsrki.api.aloha.complete');

        $param = [
            'date' => Helper::DateConvertFormat($this->date, 'Y/m/d', 'd/m/Y'),
            'store_code' => Plant::getCustomerCodeById($sendTax->plant_id)
        ];

        $responseComplete = Http::post($urlComplete, $param);
        if ($responseComplete->successful()) {
            $respComplete = $responseComplete->json();

            if ($respComplete['status'] == 'success') {

                if ($respComplete['data'] != 'null') {

                    $count = $respComplete['data']['Count'];

                    if($count != 0){

                        $this->getTranctionTax($param, $sendTax);

                    }else{
                        // adding error transaction not complete
                        HistorySendTax::addHistoryFailed($this->date, $sendTax->id, 'Transaction not complete');
                    }
                } else {
                    // adding error null result
                    HistorySendTax::addHistoryFailed($this->date, $sendTax->id, 'Transaction not complete');
                }
            } else {
                // adding error result
                HistorySendTax::addHistoryFailed($this->date, $sendTax->id, 'Check complete result transaction failed');
            }

        }else {
            // adding error api complete
            HistorySendTax::addHistoryFailed($this->date, $sendTax->id, 'Check complete transaction failed');
        }
    }

    public function getTranctionTax($param, $sendTax)
    {
        // get data
        $url = config('qsrki.api.aloha.url') . config('qsrki.api.aloha.tax_transaction');

        $response = Http::post($url, $param);
        if ($response->successful()) {
            $resp = $response->json();

            if ($resp['status'] == 'success') {

                if ($resp['data'] != 'null' && $resp['data'] != '') {
                    $taxTransaction = $resp['data'];

                    if (sizeof($taxTransaction) > 0) {
                        $date_fn = Helper::DateConvertFormat($this->date, 'Y/m/d', 'Ymd');
                        // generate file excel
                        $fileName = $sendTax->prefix_name_store . '_' . $date_fn;
                        $fileType = '.csv';
                        $fileNameUploaded = $fileName . $fileType;
                        $filePath = 'tax/csv/';
                        $fileUpload = storage_path('app/public/' . $filePath . $fileNameUploaded);

                        if (Excel::store(new GenerateTransaction($taxTransaction), $filePath . $fileNameUploaded, 'public')) {
                            // get amount transaction
                            $amount = 0;
                            foreach ($taxTransaction as $v) {
                                $amount += $v['total'];
                            }
                            $this->uploadToFTP($sendTax->id, $fileUpload, $fileNameUploaded, $amount);

                        } else {
                            // adding error generate file sales transaction
                            HistorySendTax::addHistoryFailed($this->date, $sendTax->id, 'Generate file csv failed');
                        }

                    } else{
                        // adding error null transaction
                        HistorySendTax::addHistoryFailed($this->date, $sendTax->id, 'Transactions zero');
                    }

                } else {
                    // adding error null transaction
                    HistorySendTax::addHistoryFailed($this->date, $sendTax->id, 'Transactions zero');
                }
            } else {
                // adding error result transaction
                HistorySendTax::addHistoryFailed($this->date, $sendTax->id, 'Result transaction failed');
            }
        } else {
            // adding error get transaction
            HistorySendTax::addHistoryFailed($this->date, $sendTax->id, 'Get transaction failed');
        }
    }

    public function uploadToFTP($id, $fileUpload, $fileNameUploaded, $amount)
    {
        try {
            // setup ftp / sftp
            $configFtp = SendTax::getConfigFtp($id);

            if ($configFtp['driver'] != 'sftp') {
                // ftp
                $ftp = Storage::createFtpDriver($configFtp);
            } else {
                // sftp
                $ftp = Storage::createSftpDriver($configFtp);
            }

            // send file csv to ftp / sftp
            $path = $ftp->putFileAs('', $fileUpload, $fileNameUploaded);
            if ($path != '') {
                // success
                $historySendTax = new HistorySendTax;
                $historySendTax->date = $this->date;
                $historySendTax->send_tax_id = $id;
                $historySendTax->amount = $amount;
                $historySendTax->status = 1;
                $historySendTax->description = '';
                $historySendTax->save();
            } else {
                // error
                HistorySendTax::addHistoryFailed($this->date, $id, 'Send file to ftp / sftp failed');
            }
        } catch (\Throwable $t) {
            // adding history send tax error
            HistorySendTax::addHistoryFailed($this->date, $id, $t->getMessage());
        }
    }

}
