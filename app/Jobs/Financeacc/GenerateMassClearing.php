<?php

namespace App\Jobs\Financeacc;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Library\Helper;

use App\Exports\Financeacc\MassClearingExport;

use App\Models\Financeacc\MassClearing;
use App\Models\Financeacc\MassClearingDetail;
use App\Models\Interfaces\AlohaTransactionLog;
use App\Models\Interfaces\VtecOrderPayDetail;
use App\Models\Plant;
use App\Models\Pos\Aloha;
use App\Models\SpecialGl;
use App\Models\BankChargeGl;
use App\Models\Financeacc\MassClearingGenerate;
use App\Models\Interfaces\VtecTransactionLog;

class GenerateMassClearing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $massClearingDetail;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($massClearingDetail)
    {
        $this->massClearingDetail = $massClearingDetail;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $massClearingDetail = $this->massClearingDetail;

        // update status transaction on progress
        if( $massClearingDetail->total_row > 1 ){
            $transactions = DB::table('mass_clearing_details')
                                ->where('bank_in_bank_gl', $massClearingDetail->bank_in_bank_gl)
                                ->where('bank_in_date', $massClearingDetail->bank_in_date)
                                ->where('sales_date', $massClearingDetail->sales_date)
                                ->where('sales_month', $massClearingDetail->sales_month)
                                ->where('sales_year', $massClearingDetail->sales_year)
                                ->where('special_gl', $massClearingDetail->special_gl)
                                ->where('plant_id', $massClearingDetail->plant_id)
                                ->where('mass_clearing_id', $massClearingDetail->mass_clearing_id)
                                ->select('id')
                                ->get();

            foreach ($transactions as $trans) {
                MassClearingDetail::updateMassClearingDetail('status_process', 1, $trans->id);
            }
        } else {
            $transaction = DB::table('mass_clearing_details')
                            ->where('bank_in_bank_gl', $massClearingDetail->bank_in_bank_gl)
                            ->where('bank_in_date', $massClearingDetail->bank_in_date)
                            ->where('sales_date', $massClearingDetail->sales_date)
                            ->where('sales_month', $massClearingDetail->sales_month)
                            ->where('sales_year', $massClearingDetail->sales_year)
                            ->where('special_gl', $massClearingDetail->special_gl)
                            ->where('plant_id', $massClearingDetail->plant_id)
                            ->where('mass_clearing_id', $massClearingDetail->mass_clearing_id)
                            ->select('id')
                            ->first();
            MassClearingDetail::updateMassClearingDetail('status_process', 1, $transaction->id);
        }

        // 1 = aloha 2 = vtec
        $pos = Plant::getPosById($massClearingDetail->plant_id);
        $sapCode = SpecialGl::getSapCodebySpecialGl($massClearingDetail->special_gl);
        $reference = SpecialGl::getRefbySpecialGl($massClearingDetail->special_gl);
        $salesDates = MassClearingDetail::getSalesDate($massClearingDetail);
        $nominalPos = 0;
        $documentNumberSalesSaps = [];
        $postingSap = true;
        $totalBankCharge = (int)$massClearingDetail->bank_in_charge;
        $totalBankIn = $massClearingDetail->bank_in_nominal + $totalBankCharge;
        $messageFailed = '';
        $generate = true;

        // get nominal pos and doc number by sales date
        foreach ($salesDates as $salesDate) {
            if( $pos != 1 ){
                // vtec
                $documentNumberSalesSap = VtecTransactionLog::getDocumentNumberSalesSap($massClearingDetail->plant_id, $salesDate);
                if($documentNumberSalesSap == ''){
                    $postingSap = false;
                    break;
                }
                $documentNumberSalesSaps[] = $documentNumberSalesSap;

                $nominalPosDate = VtecOrderPayDetail::getTotalPaymentByMethodPayment($massClearingDetail->plant_id, $salesDate, $sapCode);
            } else {
                // aloha
                $documentNumberSalesSap = AlohaTransactionLog::getDocumentNumberSalesSap($massClearingDetail->plant_id, $salesDate);
                if($documentNumberSalesSap == ''){
                    $postingSap = false;
                    break;
                }
                $documentNumberSalesSaps[] = $documentNumberSalesSap;

                $nominalPosDate = Aloha::getTotalPaymentByMethodPayment($massClearingDetail->plant_id, $salesDate, $sapCode);
            }

            $nominalPos += $nominalPosDate;
        }

        $documentNumber = implode(',', $documentNumberSalesSaps);
        $shortNamePlant = strtoupper(Plant::getShortNameById($massClearingDetail->plant_id, false));
        $customerCodePlant = Plant::getCustomerCodeById($massClearingDetail->plant_id);
        $selisih = abs($nominalPos - $totalBankIn);
        $selisihPercent = 0;
        if($nominalPos <> 0){
            $selisihPercent = round(( $selisih / $nominalPos) * 100, 2);
        }
        $salesDateDesc = '';
        if( sizeof($salesDates) > 1 ){
            $documentNumber = $shortNamePlant . ' ' . $massClearingDetail->sales_date;
            $fromDate = Helper::DateConvertFormat($salesDates[0], 'Y-m-d', 'd/m/Y');
            $untilDate = Helper::DateConvertFormat($salesDates[sizeof($salesDate)-1], 'Y-m-d', 'd/m/Y');
            $salesDateDesc = $fromDate . '-' . $untilDate;
        } else {
            $salesDateDesc = Helper::DateConvertFormat($salesDates[0], 'Y-m-d', 'd/m/Y');
        }

        // check have already posting to sap or not
        if( $postingSap ){
            if( $selisih <> 0){
                if( strtoupper($massClearingDetail->special_gl) == 'Z'  ){
                    // edc mandiri
                    if( $selisihPercent > 5 ){
                        $generate = false;
                        $messageFailed = 'Bank In vs Sales not match';
                    } else {
                        $totalBankCharge += $selisih;
                    }
                } else if( strtoupper($massClearingDetail->special_gl) == '3'  ){
                    // cash
                    if( $selisih > 100 ){
                        $generate = false;
                        $messageFailed = 'Bank In vs Sales not match';
                    }
                } else {
                    $generate = false;
                    $messageFailed = 'Bank In vs Sales not match';
                }
            }
        } else {
            $generate = false;
            $messageFailed = 'Sales not yet posting to SAP';
        }

        if( $generate ){

            $item = 1;
            $insertGenerateItems = [];

            if( $massClearingDetail->total_row > 1 ){

                $transactions = DB::table('mass_clearing_details')
                                    ->where('bank_in_bank_gl', $massClearingDetail->bank_in_bank_gl)
                                    ->where('bank_in_date', $massClearingDetail->bank_in_date)
                                    ->where('sales_date', $massClearingDetail->sales_date)
                                    ->where('sales_month', $massClearingDetail->sales_month)
                                    ->where('sales_year', $massClearingDetail->sales_year)
                                    ->where('special_gl', $massClearingDetail->special_gl)
                                    ->where('plant_id', $massClearingDetail->plant_id)
                                    ->where('mass_clearing_id', $massClearingDetail->mass_clearing_id)
                                    ->select('id', 'bank_in_nominal', 'bank_in_charge', 'bank_in_description')
                                    ->get();

                foreach ($transactions as $trans) {

                    // insert to generate
                    $insertGenerateItems[] = [
                        'mass_clearing_id' => $massClearingDetail->mass_clearing_id,
                        'no' => MassClearingGenerate::getNoGenerate($massClearingDetail->mass_clearing_id),
                        'item' => $item,
                        'customer_code' => $customerCodePlant,
                        'bank_in_date' => $massClearingDetail->bank_in_date,
                        'special_gl' => $massClearingDetail->special_gl,
                        'document_number' => $documentNumber,
                        'ar_value' => $nominalPos,
                        'reference' => $shortNamePlant,
                        'gl_account' => $massClearingDetail->bank_in_bank_gl,
                        'value' => $trans->bank_in_nominal,
                        'tax_code' => '',
                        'assigment' => $reference,
                        'text' => $reference . ' ' . $shortNamePlant . ' ' . $salesDateDesc,
                        'cost_center' => '',
                    ];

                    $item++;

                }

                $item = $item - 1;

            } else {

                // insert to generate
                $insertGenerateItems[] = [
                    'mass_clearing_id' => $massClearingDetail->mass_clearing_id,
                    'no' => MassClearingGenerate::getNoGenerate($massClearingDetail->mass_clearing_id),
                    'item' => $item,
                    'customer_code' => $customerCodePlant,
                    'bank_in_date' => $massClearingDetail->bank_in_date,
                    'special_gl' => $massClearingDetail->special_gl,
                    'document_number' => $documentNumber,
                    'ar_value' => $nominalPos,
                    'reference' => $shortNamePlant,
                    'gl_account' => $massClearingDetail->bank_in_bank_gl,
                    'value' => $massClearingDetail->bank_in_nominal,
                    'tax_code' => '',
                    'assigment' => $reference,
                    'text' => $reference . ' ' . $shortNamePlant . ' ' . $salesDateDesc,
                    'cost_center' => '',
                ];
            }

            $costCenter = Plant::getCostCenterById($massClearingDetail->plant_id);

            // check have bank charge / commision or not
            if( $totalBankCharge > 0 ){

                $referenceCharge = strtoupper(BankChargeGl::getRefbySpecialGl($massClearingDetail->special_gl,  $massClearingDetail->bank_in_bank_gl));
                $referenceChargeText = $referenceCharge;

                if( $referenceCharge != 'BANK CHARGE' ){
                    $referenceChargeText = $referenceCharge . ' ' . $reference;
                }else{
                    $costCenter = 'C1200002';
                }

                // insert to generate
                $insertGenerateItems[] = [
                    'mass_clearing_id' => $massClearingDetail->mass_clearing_id,
                    'no' => MassClearingGenerate::getNoGenerate($massClearingDetail->mass_clearing_id),
                    'item' => $item + 1,
                    'customer_code' => $customerCodePlant,
                    'bank_in_date' => $massClearingDetail->bank_in_date,
                    'special_gl' => $massClearingDetail->special_gl,
                    'document_number' => $documentNumber,
                    'ar_value' => $nominalPos,
                    'reference' => $shortNamePlant,
                    'gl_account' => BankChargeGl::getGlAccountCharge( $massClearingDetail->special_gl,  $massClearingDetail->bank_in_bank_gl),
                    'value' => $totalBankCharge,
                    'tax_code' => 'A0',
                    'assigment' => $referenceCharge,
                    'text' => $referenceChargeText . ' ' . $shortNamePlant . ' ' . $salesDateDesc,
                    'cost_center' => $costCenter,
                ];
            }

            // check this transaction is cash and have tolerance selisih
            if( $selisih > 0 && $selisih < 100 && strtoupper($massClearingDetail->special_gl) == '3' ){

                $glAccountSelisih = '70050102';
                $assigmentSelisih = 'SELISIH SISTEM';

                // insert to generate
                $insertGenerateItems[] = [
                    'mass_clearing_id' => $massClearingDetail->mass_clearing_id,
                    'no' => MassClearingGenerate::getNoGenerate($massClearingDetail->mass_clearing_id),
                    'item' => $item + 1,
                    'customer_code' => $customerCodePlant,
                    'bank_in_date' => $massClearingDetail->bank_in_date,
                    'special_gl' => $massClearingDetail->special_gl,
                    'document_number' => $documentNumber,
                    'ar_value' => $nominalPos,
                    'reference' => $shortNamePlant,
                    'gl_account' => $glAccountSelisih,
                    'value' => $selisih,
                    'tax_code' => 'A0',
                    'assigment' => $assigmentSelisih,
                    'text' => $assigmentSelisih . ' ' . $shortNamePlant . ' ' . $salesDateDesc,
                    'cost_center' => $costCenter,
                ];
            }

            // update transaction status success
            DB::table('mass_clearing_details')
                ->where('bank_in_bank_gl', $massClearingDetail->bank_in_bank_gl)
                ->where('bank_in_date', $massClearingDetail->bank_in_date)
                ->where('sales_date', $massClearingDetail->sales_date)
                ->where('sales_month', $massClearingDetail->sales_month)
                ->where('sales_year', $massClearingDetail->sales_year)
                ->where('special_gl', $massClearingDetail->special_gl)
                ->where('plant_id', $massClearingDetail->plant_id)
                ->where('mass_clearing_id', $massClearingDetail->mass_clearing_id)
                ->update([
                    'selisih' => $selisih,
                    'selisih_percent' => $selisihPercent,
                    'nominal_sales' => $nominalPos,
                    'status_process' => 2,
                    'status_generate' => 1,
                    'description' => 'generated'
                ]);

            // insert generate item
            DB::table('mass_clearing_generates')->insert($insertGenerateItems);

        } else {
            // update transaction status failed
            DB::table('mass_clearing_details')
                ->where('bank_in_bank_gl', $massClearingDetail->bank_in_bank_gl)
                ->where('bank_in_date', $massClearingDetail->bank_in_date)
                ->where('sales_date', $massClearingDetail->sales_date)
                ->where('sales_month', $massClearingDetail->sales_month)
                ->where('sales_year', $massClearingDetail->sales_year)
                ->where('special_gl', $massClearingDetail->special_gl)
                ->where('plant_id', $massClearingDetail->plant_id)
                ->where('mass_clearing_id', $massClearingDetail->mass_clearing_id)
                ->update([
                    'selisih' => $selisih,
                    'selisih_percent' => $selisihPercent,
                    'nominal_sales' => $nominalPos,
                    'status_process' => 2,
                    'status_generate' => 2,
                    'description' => $messageFailed
                ]);
        }

        // check this transaction is last
        $countRemainingTrans = DB::table('mass_clearing_details')
                                ->where('mass_clearing_id', $massClearingDetail->mass_clearing_id)
                                ->where('status_process', 0)
                                ->count();

        if( $countRemainingTrans <= 0 ){
            // export to excel
            $path = 'mass-clearing/';
            $filename = strtotime("now") . Helper::generateRandomStr(5);
            $typefile = '.xlsx';
            Excel::store(new MassClearingExport($massClearingDetail->mass_clearing_id), $path . $filename . $typefile, 'public');

            // update status process mass clearing done
            $massClearing = MassClearing::find($massClearingDetail->mass_clearing_id);
            $massClearing->time_process_finish = \Carbon\Carbon::now();
            $massClearing->status_generate = 2;
            $massClearing->filename = $filename;
            $massClearing->save();
        }


    }
}
