<?php

namespace App\Jobs\Interfaces\Vtec;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

use App\Models\Interfaces\VtecOrderTransaction;
use App\Models\Interfaces\VtecTransactionLog;
use App\Models\Interfaces\VtecHistorySendSap;
use App\Models\Interfaces\VtecHistorySendWarehouse;
use App\Models\Plant;

class UploadSalesVtec implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $plantID;
    protected $date;
    protected $type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($plantID, $date, $type)
    {
        $this->plantID = $plantID;
        $this->date = $date;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // type 1 = SAP, 2 = WAREHOUSE
        if ($this->type == 1) {
            $this->sendToSAP($this->date, $this->plantID);
        } else {
            $this->sendToWarehouse($this->date, $this->plantID);
        }
    }

    public function sendToSAP($date, $plantID)
    {
        $url = config('qsrki.api.apps.url') . 'recheese/daily-sales/sap/sales/upload';

        $dataPos = VtecOrderTransaction::getDataInterfacePOS($date, $plantID);

        // if( abs($dataPos['total_payments'] - $dataPos['total_sales']) > 2000 ){
        //     // selisih > 2000, jangan di send ke SAP
        //     // insert to history send sales report

        //     $vtecHistorySendSap = new VtecHistorySendSap;
        //     $vtecHistorySendSap->date = $date;
        //     $vtecHistorySendSap->plant_id = $plantID;
        //     $vtecHistorySendSap->total_payments = $dataPos['total_payments'];
        //     $vtecHistorySendSap->total_sales = $dataPos['total_sales'];
        //     $vtecHistorySendSap->selisih = $dataPos['total_payments'] - $dataPos['total_sales'];
        //     $vtecHistorySendSap->description = 'Selisih > 2000';
        //     $vtecHistorySendSap->send = 0;
        //     $vtecHistorySendSap->save();

        // } else {

            $pos = [
                'payment' => json_encode($dataPos['payments']),
                'sales' => json_encode($dataPos['sales']),
                'inventory' => json_encode($dataPos['inventories']),
            ];

            // posting to SAP
            $response = Http::asForm()->post($url, [
                'pos' => $pos,
            ]);

            $statusHistorySap = false;
            $messageHistorySap = "";

            if ($response->ok()) {
                $res_sap = $response->json();

                if ($res_sap['success']) {

                    // insert to history return sap report
                    foreach ($res_sap['data'] as $res) {
                        $message = $res['BLART'] . ' ' . $res['CODE'] . ' ' . $res['MESSAGE'];

                        $vtecTransactionLog = new VtecTransactionLog;
                        $vtecTransactionLog->type = 1;
                        $vtecTransactionLog->status = $res['CODE'];
                        $vtecTransactionLog->message = $message;
                        $vtecTransactionLog->closing_date = $date;
                        $vtecTransactionLog->plant_id = $plantID;
                        if ($vtecTransactionLog->save()) {
                            $statusHistorySap = true;
                            $messageHistorySap = "Success send to SAP";
                        } else {
                            $statusHistorySap = false;
                            $messageHistorySap = "Save vtec transaction log error";
                            break;
                        }
                    }
                } else {
                    // adding message error
                    $vtecTransactionLog = new VtecTransactionLog;
                    $vtecTransactionLog->type = 0;
                    $vtecTransactionLog->status = 'E';
                    $vtecTransactionLog->message = $res_sap['message'];
                    $vtecTransactionLog->closing_date = $date;
                    $vtecTransactionLog->plant_id = $plantID;
                    if ($vtecTransactionLog->save()) {
                        $statusHistorySap = false;
                    } else {
                        $statusHistorySap = false;
                        $messageHistorySap = "Save vtec error transaction log error";
                    }
                }
            } else {
                // error send sap
                $statusHistorySap = false;
                $messageHistorySap = "Error send sales to API SAP";
            }

            // insert to history send sales report
            $vtecHistorySendSap = new VtecHistorySendSap;
            $vtecHistorySendSap->date = $date;
            $vtecHistorySendSap->plant_id = $plantID;
            $vtecHistorySendSap->total_payments = $dataPos['total_payments'];
            $vtecHistorySendSap->total_sales = $dataPos['total_sales'];
            $vtecHistorySendSap->selisih = $dataPos['total_payments'] - $dataPos['total_sales'];
            $vtecHistorySendSap->description = $messageHistorySap;
            if ($statusHistorySap) {
                $vtecHistorySendSap->send = 1;
            } else {
                $vtecHistorySendSap->send = 0;
            }
            $vtecHistorySendSap->save();

        // }

    }

    public function sendToWarehouse($date, $plantID)
    {
        $statusHistoryWH = 0;
        $totalSalesWH = 0;
        $customerCode = Plant::getCustomerCodeById($plantID);

        DB::connection('warehouse')->beginTransaction();

        DB::connection('warehouse')
            ->table('Sales_Transactions_Udex')
            ->where('Trx_date', $date)
            ->where('Store_id', $customerCode)
            ->delete();

        sleep(3);

        $dataTransactionVtec = DB::table('vtec_order_details')
                                ->where('plant_id', $plantID)
                                ->where('SaleDate', $date)
                                ->where('TransactionStatusID', 2)
                                ->where('OrderStatusID', 2)
                                ->where('ProductID', '<>', 0)
                                ->where('ProductTypeID', '<>', 14)
                                ->select(
                                    DB::raw('CONCAT(ComputerID , TransactionID ) as Trans_id'),
                                    'OrderDetailID',
                                    'ReceiptNumber',
                                    'ShopCode',
                                    'ShopName',
                                    'ComputerID',
                                    'SaleDate',
                                    'PaidTime',
                                    'PaidStaffID',
                                    'PaidStaff',
                                    'ProductCode',
                                    'ProductName',
                                    'ProductSetType',
                                    'TotalQty',
                                    'PricePerUnit',
                                    'DiscPercent',
                                    'TotalDiscount',
                                    'NetSale',
                                    DB::raw('ROUND(NetSale * 1.1, -2) as AfterTax'),
                                    'SaleMode',
                                    'SaleModeName',
                                )
                                ->orderBy('TransactionID')
                                ->get();

        $collectionTransactionVtec = collect($dataTransactionVtec->toArray());
        $chunksTransactionVtec = $collectionTransactionVtec->chunk(90);

        foreach ($chunksTransactionVtec as $transactions) {
            $insert = [];
            foreach ($transactions as $transaction) {
                $totalSalesWH += $transaction->AfterTax;
                $insert[] = [
                    'Trans_id' => $transaction->Trans_id,
                    'Order_seq' => $transaction->OrderDetailID,
                    'Bill_no' => $transaction->ReceiptNumber,
                    'Store_id' => $transaction->ShopCode,
                    'Store_name' => $transaction->ShopName,
                    'POS_no' => $transaction->ComputerID,
                    'Trx_date' => $transaction->SaleDate,
                    'Trx_time' => $transaction->PaidTime,
                    'Paid_staffid' => $transaction->PaidStaffID,
                    'Paid_staff' => Str::limit($transaction->PaidStaff, 25, ''),
                    'Product_id' => $transaction->ProductCode,
                    'Product_name' => $transaction->ProductName,
                    'Item_Type' => $transaction->ProductSetType,
                    'Qty' => (int)$transaction->TotalQty,
                    'Price' => (int)$transaction->PricePerUnit,
                    'Disc_pct' => (int)$transaction->DiscPercent,
                    'Disc_amt' => (int)$transaction->TotalDiscount,
                    'Net_sale' => (int)$transaction->NetSale,
                    'AfterTax' => (int)$transaction->AfterTax,
                    'Salestype' => $transaction->SaleMode,
                    'Salestype_name' => $transaction->SaleModeName,
                    'Add1' => 'Vtec',
                    'Add2' => '0',
                ];
            }

            $statusInsert = DB::connection('warehouse')->table('Sales_Transactions_Udex')->insert($insert);

            if ($statusInsert) {
                $statusHistoryWH = 1;
            } else {
                $statusHistoryWH = 0;
                break;
            }
        }

        $vtecHistorySendWarehouse = new VtecHistorySendWarehouse;
        $vtecHistorySendWarehouse->date = $date;
        $vtecHistorySendWarehouse->plant_id = $plantID;
        $vtecHistorySendWarehouse->total_sales = $totalSalesWH;
        if ($statusHistoryWH == 0) {
            $vtecHistorySendWarehouse->send = 0;
            $vtecHistorySendWarehouse->description = "Error insert transaction to Data Warehouse";
            DB::connection('warehouse')->rollBack();
        } else {
            $vtecHistorySendWarehouse->send = 1;
            $vtecHistorySendWarehouse->description = "Success send to Data Warehouse";
            DB::connection('warehouse')->commit();
        }
        $vtecHistorySendWarehouse->save();

    }

}
