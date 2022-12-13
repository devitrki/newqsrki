<?php

namespace App\Models\Tax;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Library\Helper;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Tax\GenerateTransaction;

use App\Repositories\AlohaRepository;

use App\Models\Plant;

class SendTax extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getConfigFtp($send_tax_id) {
        $qSendTax = DB::table('send_taxes')
                        ->leftJoin('ftp_governments', 'ftp_governments.id', '=', 'send_taxes.ftp_government_id')
                        ->leftJoin('plants', 'plants.id', '=', 'send_taxes.plant_id')
                        ->where('send_taxes.id', $send_tax_id)
                        ->select(
                            'ftp_governments.transfer_type',
                            'ftp_governments.host',
                            'ftp_governments.username',
                            'ftp_governments.password',
                            'ftp_governments.port',
                            'send_taxes.prefix_name_store'
                        );
        $configFtp = [];

        if ($qSendTax->count() > 0) {

            $sendTax = $qSendTax->first();

            $configFtp = [
                'driver'   => $sendTax->transfer_type,
                'host'     => $sendTax->host,
                'username' => $sendTax->username,
                'password' => $sendTax->password,
                'port'     => (int)$sendTax->port,
                'timeout'  => 60,
            ];
        }

        return $configFtp;
    }

    public static function downloadFileSales($send_tax_id, $date, $fileType)
    {
        $sendTax = DB::table('send_taxes')
                    ->where('send_taxes.id', $send_tax_id)
                    ->select('id', 'plant_id', 'prefix_name_store')
                    ->first();

        $plant = DB::table('plants')
                    ->where('id', $sendTax->plant_id)
                    ->first();

        $plantPosId = Plant::getPosById($sendTax->plant_id);

        $stat = 0;
        $msg = 'Download file sales failed. ';
        $data = [];

        $alohaRepository = new AlohaRepository($plantPosId);
        $initConnectionAloha = $alohaRepository->initConnectionDB();
        if (!$initConnectionAloha['status']) {
            $msg .= $initConnectionAloha['message'];

            return [
                'status' => $stat,
                'message' => $msg,
                'data' => $data
            ];
        }

        $checkTransComplete = $alohaRepository->checkCompleteStoreAloha($plant->customer_code, $date);
        if ($checkTransComplete != 0) {

            $resultTransaction = SendTax::getTranctionTax($plantPosId, $date, $plant->customer_code, $sendTax, $fileType);
            if($resultTransaction){
                $stat = $resultTransaction['status'];
                $data = $resultTransaction['data'];
                $msg = $resultTransaction['message'];
            } else {
                $msg = $resultTransaction['message'];
            }

        } else {
            // adding error transaction not complete
            $msg .= 'Transaction not complete';
        }

        return [
            'status' => $stat,
            'message' => $msg,
            'data' => $data,
        ];

    }

    public static function getTranctionTax($plantPosId, $date, $customerCode, $sendTax, $typeFile)
    {
        $stat = 0;
        $msg = '';
        $data = [];

        $alohaRepository = new AlohaRepository($plantPosId);
        $initConnectionAloha = $alohaRepository->initConnectionDB();
        if (!$initConnectionAloha['status']) {
            $msg .= $initConnectionAloha['message'];

            return [
                'status' => $stat,
                'message' => $msg,
                'data' => $data
            ];
        }

        $taxTransactions = $alohaRepository->getTaxTransaction($customerCode, $date);
        if (sizeof($taxTransactions) > 0) {
            $date_fn = Helper::DateConvertFormat($date, 'Y/m/d', 'Ymd');
            // generate file excel
            $fileName = $sendTax->prefix_name_store . '_' . $date_fn;
            $fileType = '.' . $typeFile;
            $fileNameUploaded = $fileName . $fileType;
            $filePath = 'tax/download/';
            $fileUpload = storage_path('app/public/' . $filePath . $fileNameUploaded);

            if($typeFile != 'txt' ){
                if (Excel::store(new GenerateTransaction($taxTransactions), $filePath . $fileNameUploaded, 'public')) {
                    $stat = 1;
                    $data = [
                        'file' => $fileUpload,
                        'fileName' => $fileNameUploaded,
                    ];
                    $msg = 'Download Sukses';
                } else {
                    // adding error generate file sales transaction
                    $msg = 'Generate file ' . $fileType . ' failed';
                }
            } else {

                $contents = 'Receipt Number|Transaction Time|Transaction Value (Not subject to VAT)|Discount'.  "\n";
                foreach ($taxTransactions as $trans) {
                    $systemDate = Helper::DateConvertFormat($trans->SystemDate, 'Y-m-d H:i:s.v', 'd/m/Y H:i:s');
                    $contents .= $trans->CheckNumber . '|' . $systemDate . '|' . (int)$trans->Total . '|' . (int)$trans->TotalDiscount .  "\n";
                }

                $upload = Storage::disk('public')->put($filePath . $fileNameUploaded, $contents);

                if ($upload) {
                    $stat = 1;
                    $data = [
                        'file' => $fileUpload,
                        'fileName' => $fileNameUploaded,
                    ];
                    $msg = 'Download Success';
                } else {
                    $msg = 'Generate file ' . $fileType . ' failed';
                }

            }

        } else {
            // adding error null transaction
            $msg = 'Transactions zero';
        }

        return [
            'status' => $stat,
            'message' => $msg,
            'data' => $data,
        ];
    }
}
