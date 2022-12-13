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

use App\Repositories\AlohaRepository;

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
                            'send_taxes.company_id',
                            'send_taxes.plant_id',
                            'ftp_governments.transfer_type',
                            'send_taxes.prefix_name_store',
                        )
                        ->first();

        $plant = DB::table('plants')
                    ->where('id', $sendTax->plant_id)
                    ->first();

        $plantPosId = Plant::getPosById($sendTax->plant_id);

        // check transaction complete / not complete
        $alohaRepository = new AlohaRepository($plantPosId);
        $initConnectionAloha = $alohaRepository->initConnectionDB();
        if ($initConnectionAloha['status']) {
            $checkTransComplete = $alohaRepository->checkCompleteStoreAloha($plant->customer_code, $this->date);
            if ($checkTransComplete != 0) {
                $this->getTranctionTax($plantPosId, $this->date, $plant->customer_code, $sendTax);
            } else {
                HistorySendTax::addHistoryFailed($sendTax->company_id, $this->date, $sendTax->id, 'Transaction not complete');
            }
        } else {
            HistorySendTax::addHistoryFailed($sendTax->company_id, $this->date, $sendTax->id, 'Init connection aloha failed: '. $initConnectionAloha['message']);
        }
    }

    public function getTranctionTax($plantPosId, $date, $customerCode, $sendTax)
    {
        $alohaRepository = new AlohaRepository($plantPosId);
        $initConnectionAloha = $alohaRepository->initConnectionDB();
        if ($initConnectionAloha['status']) {
            $taxTransactions = $alohaRepository->getTaxTransaction($customerCode, $date);
            if (sizeof($taxTransactions) > 0) {
                $date_fn = Helper::DateConvertFormat($this->date, 'Y/m/d', 'Ymd');
                // generate file excel
                $fileName = $sendTax->prefix_name_store . '_' . $date_fn;
                $fileType = '.csv';
                $fileNameUploaded = $fileName . $fileType;
                $filePath = 'tax/csv/';
                $fileUpload = storage_path('app/public/' . $filePath . $fileNameUploaded);

                if (Excel::store(new GenerateTransaction($taxTransactions), $filePath . $fileNameUploaded, 'public')) {
                    // get amount transaction
                    $amount = 0;
                    foreach ($taxTransactions as $v) {
                        $amount += $v->Total;
                    }
                    $this->uploadToFTP($sendTax, $fileUpload, $fileNameUploaded, $amount);

                } else {
                    // adding error generate file sales transaction
                    HistorySendTax::addHistoryFailed($sendTax->company_id, $this->date, $sendTax->id, 'Generate file csv failed');
                }
            } else {
                HistorySendTax::addHistoryFailed($sendTax->company_id, $this->date, $sendTax->id, 'Transactions zero');
            }
        } else {
            HistorySendTax::addHistoryFailed($sendTax->company_id, $this->date, $sendTax->id, 'Init connection aloha failed: '. $initConnectionAloha['message']);
        }
    }

    public function uploadToFTP($sendTax, $fileUpload, $fileNameUploaded, $amount)
    {
        try {
            // setup ftp / sftp
            $configFtp = SendTax::getConfigFtp($sendTax->id);

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
                $historySendTax->company_id = $sendTax->company_id;
                $historySendTax->date = $this->date;
                $historySendTax->send_tax_id = $sendTax->id;
                $historySendTax->amount = $amount;
                $historySendTax->status = 1;
                $historySendTax->description = '';
                $historySendTax->save();
            } else {
                // error
                HistorySendTax::addHistoryFailed($sendTax->company_id, $this->date, $sendTax->id, 'Send file to ftp / sftp failed');
            }
        } catch (\Throwable $t) {
            // adding history send tax error
            HistorySendTax::addHistoryFailed($sendTax->company_id, $this->date, $sendTax->id, $t->getMessage());
        }
    }

}
