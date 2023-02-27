<?php

namespace App\Models\ExternalVendor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

use App\Exports\ExternalVendor\GenerateTransactionSales;

use App\Library\Helper;
use App\Repositories\AlohaRepository;

use App\Models\Plant;
use App\Models\Company;
use App\Models\ExternalVendor\TemplateSales;

class SendVendor extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function downloadFileSales($send_vendor_id, $date, $fileType)
    {
        $sendVendor = DB::table('send_vendors')
                    ->where('id', $send_vendor_id)
                    ->select('id', 'company_id', 'plant_id', 'prefix_name_store', 'template_sale_id')
                    ->first();

        $plant = DB::table('plants')
                    ->where('id', $sendVendor->plant_id)
                    ->first();

        $plantPosId = Plant::getPosById($plant->id);

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

            $resultTransaction = SendVendor::getTransaction($plantPosId, $date, $plant->customer_code, $sendVendor, $fileType);
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

    public static function getTransaction($plantPosId, $date, $customerCode, $sendVendor, $typeFile)
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

        $transactions = $alohaRepository->getTransactionReceiptNumber($customerCode, $date);
        if (sizeof($transactions) > 0) {
            $taxPercent = Company::getConfigByKey($sendVendor->company_id, 'TAX_PERCENT');

            $transactionTemplateFormats = SendVendor::generateFormatTransaction($sendVendor->template_sale_id, $transactions, (float)$taxPercent);

            $date_fn = Helper::DateConvertFormat($date, 'Y/m/d', 'Ymd');
            // generate file excel
            $fileName = $sendVendor->prefix_name_store . '_' . $date_fn;
            $fileType = '.' . $typeFile;
            $fileNameUploaded = $fileName . $fileType;
            $filePath = 'external-vendor/download/';
            $fileUpload = storage_path('app/public/' . $filePath . $fileNameUploaded);

            if ($typeFile != 'txt' ){
                if (Excel::store(new GenerateTransactionSales($transactionTemplateFormats['datas'], $transactionTemplateFormats['fields']), $filePath . $fileNameUploaded, 'public')) {
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
                $contents = '';

                foreach ($transactionTemplateFormats['fields'] as $k => $field) {
                    if ($k >= sizeof($transactionTemplateFormats['fields']) - 1) {
                        $contents .= $field . "\n";
                    } else {
                        $contents .= $field . '|';
                    }
                }

                foreach ($transactionTemplateFormats['datas'] as $data) {
                    foreach ($data as $trans) {
                        if (is_bool($trans)) {
                            $trans = $trans ? 'true' : 'false';
                            $contents .= $trans . '|';
                        } else {
                            $contents .= $trans . '|';
                        }

                    }
                    $contents = rtrim($contents, "|");
                    $contents .= "\n";
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

    public static function generateFormatTransaction($templateSaleId, $transactions, $taxPercent, $formatDate = 'Y-m-d', $formatDateTime = 'Y-m-d H:i:s')
    {
        $templateSalesDetails = DB::table('template_sales_details')
                                    ->where('template_sale_id', $templateSaleId)
                                    ->select('data', 'field_name')
                                    ->get();

        $templateSalesFieldNumbers = TemplateSales::getTemplateSalesFieldNumbers();
        $dataTransactions = [];
        $fieldTransactions = [];
        $totalTransactions = 0;

        foreach ($transactions as $transaction) {
            $dataTransactionTemp = [];
            foreach ($templateSalesDetails as $templateSalesDetail) {
                if ($templateSalesDetail->data == 'receipt_time') {
                    $receiptTime = Helper::DateConvertFormat($transaction->{$templateSalesDetail->data}, 'Y-m-d H:i:s.v', $formatDateTime);
                    $dataTransactionTemp[$templateSalesDetail->field_name] = $receiptTime;
                } else if ($templateSalesDetail->data == 'discount_percent') {
                    $dataTransactionTemp[$templateSalesDetail->field_name] = 0.0;
                } else if ($templateSalesDetail->data == 'tax_percent') {
                    $dataTransactionTemp[$templateSalesDetail->field_name] = $taxPercent;
                } else if ($templateSalesDetail->data == 'service_charge_percent') {
                    $dataTransactionTemp[$templateSalesDetail->field_name] = 0.0;
                } else if ($templateSalesDetail->data == 'is_void') {
                    $dataTransactionTemp[$templateSalesDetail->field_name] = ($transaction->grand_total_amount < 0) ? true : false;
                } else if ($templateSalesDetail->data == 'is_test') {
                    $dataTransactionTemp[$templateSalesDetail->field_name] = true;
                } else {
                    if (in_array($templateSalesDetail->data, $templateSalesFieldNumbers)) {
                        if ($templateSalesDetail->data == 'sub_total_amount' && $transaction->{$templateSalesDetail->data} <= 0) {
                            $dataTransactionTemp[$templateSalesDetail->field_name] = (float)$transaction->discount_amount;
                        } else {
                            $dataTransactionTemp[$templateSalesDetail->field_name] = (float)$transaction->{$templateSalesDetail->data};
                        }
                    } else {
                        $dataTransactionTemp[$templateSalesDetail->field_name] = $transaction->{$templateSalesDetail->data};
                    }
                }

                if (!in_array($templateSalesDetail->field_name, $fieldTransactions)) {
                    $fieldTransactions[] = $templateSalesDetail->field_name;
                }
            }
            $totalTransactions += $transaction->grand_total_amount;
            $dataTransactions[] = $dataTransactionTemp;
        }

        return [
            'datas' => $dataTransactions,
            'fields' => $fieldTransactions,
            'total' => $totalTransactions
        ];
    }
}
