<?php

namespace App\Jobs\Interfaces\Gion\Accurate;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Library\Helper;
use Carbon\Carbon;

use App\Models\Interfaces\GionTicket;
use App\Models\Interfaces\GionOrder;
use App\Models\Interfaces\GionPaymentTicket;
use App\Models\Interfaces\GionHistorySales;
use App\Models\Interfaces\GionBranch;

use App\Models\Configuration;

class UploadSales implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $branch_id;
    protected $date;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($branch_id, $date)
    {
        $this->branch_id = $branch_id;
        $this->date = $date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {

            $access_token = Configuration::getValueByKeyFor('interface', 'access_token');
            $database_session = Configuration::getValueByKeyFor('interface', 'database_session');

            $url_check_session_db = 'https://account.accurate.id/api/db-check-session.do?session=' . $database_session;

            $response = Http::timeout(100)->withHeaders([
                'Authorization' => 'Bearer ' . $access_token,
            ])->get($url_check_session_db);

            if ($response->ok()) {
                $res = $response->json();

                if ($res['s'] == true) {
                    $this->getSessionDatabase();
                } else {
                    $gionHistorySales = new GionHistorySales();
                    $gionHistorySales->date = $this->date;
                    $gionHistorySales->branch_id = $this->branch_id;
                    $gionHistorySales->errors = json_encode(["Server Accurate result failed for db-check-session"]);
                    $gionHistorySales->status = 'Failed';
                    $gionHistorySales->save();
                }
            } else {
                $gionHistorySales = new GionHistorySales();
                $gionHistorySales->date = $this->date;
                $gionHistorySales->branch_id = $this->branch_id;
                $gionHistorySales->errors = json_encode(["Server Accurate failed for db-check-session"]);
                $gionHistorySales->status = 'Failed';
                $gionHistorySales->save();
            }

        } catch (\Throwable $t) {
            // adding history send tax error
            $gionHistorySales = new GionHistorySales();
            $gionHistorySales->date = $this->date;
            $gionHistorySales->branch_id = $this->branch_id;
            $gionHistorySales->errors = json_encode([$t->getMessage()]);
            $gionHistorySales->status = 'Failed';
            $gionHistorySales->save();
        }
    }

    public function getSessionDatabase()
    {
        try {

            $access_token = Configuration::getValueByKeyFor('interface', 'access_token');
            $database_id = Configuration::getValueByKeyFor('interface', 'database_id');

            $url_open_db = 'https://account.accurate.id/api/open-db.do?id=' . $database_id;

            $response = Http::timeout(100)->withHeaders([
                'Authorization' => 'Bearer ' . $access_token,
            ])->get($url_open_db);


            if ($response->ok()) {
                $res = $response->json();
                if ($res['s'] == true) {
                    Configuration::setValueByKeyFor('interface', 'database_session', $res['session']);
                    Configuration::setValueByKeyFor('interface', 'database_host', $res['host']);
                    $this->postedSalesAccurate();
                } else {
                    $gionHistorySales = new GionHistorySales();
                    $gionHistorySales->date = $this->date;
                    $gionHistorySales->branch_id = $this->branch_id;
                    $gionHistorySales->errors = json_encode(["Server Accurate result failed for open-db"]);
                    $gionHistorySales->status = 'Failed';
                    $gionHistorySales->save();
                }
            } else {
                $gionHistorySales = new GionHistorySales();
                $gionHistorySales->date = $this->date;
                $gionHistorySales->branch_id = $this->branch_id;
                $gionHistorySales->errors = json_encode(["Server Accurate failed for open-db"]);
                $gionHistorySales->status = 'Failed';
                $gionHistorySales->save();
            }

        } catch (\Throwable $t) {
            // adding history send tax error
            $gionHistorySales = new GionHistorySales();
            $gionHistorySales->date = $this->date;
            $gionHistorySales->branch_id = $this->branch_id;
            $gionHistorySales->errors = json_encode([$t->getMessage()]);
            $gionHistorySales->status = 'Failed';
            $gionHistorySales->save();
        }
    }

    public function postedSalesAccurate()
    {
        $date = $this->date;
        $branch_id = $this->branch_id;

        $customer_code = Configuration::getValueByKeyFor('interface', 'customer_code');
        $unit_name = Configuration::getValueByKeyFor('interface', 'unit_name');
        $service_charge_code = Configuration::getValueByKeyFor('interface', 'service_charge_code');
        $service_charge_name = Configuration::getValueByKeyFor('interface', 'service_charge_name');
        $account_no_ppn = Configuration::getValueByKeyFor('interface', 'account_no_ppn');
        $account_compliment = Configuration::getValueByKeyFor('interface', 'account_compliment');
        $transDate = Helper::DateConvertFormat($date, 'Y/m/d', 'd/m/Y');
        $branchName = GionBranch::getNameById($branch_id);

        $error = [];

        if ($customer_code == '') {
            $error[] = 'Customer code not yet setting in configuration';
        }

        if ($unit_name == '') {
            $error[] = 'Unit name not yet setting in configuration';
        }

        if ($service_charge_code == '') {
            $error[] = 'Service charge code not yet setting in configuration';
        }

        if ($service_charge_name == '') {
            $error[] = 'Service charge name not yet setting in configuration';
        }

        if ($account_no_ppn == '') {
            $error[] = 'Account No PPN not yet setting in configuration';
        }

        if ($account_compliment == '') {
            $error[] = 'Account No Compliment not yet setting in configuration';
        }

        $invoices = [];
        $payments = [];

        $orders = DB::table('gion_orders')
                    ->leftJoin('gion_items', function ($join) {
                        $join->on('gion_items.alias_code', '=', 'gion_orders.alias_code');
                        $join->on('gion_items.portion', '=', 'gion_orders.portion');
                    })
                    ->where('gion_orders.date', $date)
                    ->where('gion_orders.branch_id', $branch_id)
                    ->select(
                        'gion_items.accurate_code',
                        'gion_orders.menu_name',
                        'gion_orders.portion',
                        'gion_orders.quantity',
                        'gion_orders.price',
                        'gion_orders.note',
                        'gion_orders.alias_code',
                        'gion_orders.order_states'
                    )
                    ->get();

        $items = [];
        $itemsCompliment = [];
        $totalOrderSubmitted = 0;
        $totalOrderCompliment = 0;

        foreach ($orders as $order) {
            if ($order->accurate_code != '' && $order->accurate_code != null) {

                $orderStates = json_decode($order->order_states);
                $orderState = $orderStates[sizeof($orderStates) - 1]->S;

                if ($orderState == 'Submitted' || $orderState == 'Serve Later') {
                    $totalOrderSubmitted = $totalOrderSubmitted + ($order->quantity * $order->price);
                    $indexItem = $this->getIndexItem($items, $order->accurate_code, $order->price, true);
                    if($indexItem >= 0){
                        $items[$indexItem]['quantity'] += intval($order->quantity);
                    } else {
                        $items[] = [
                            'itemNo' => $order->accurate_code,
                            'unitPrice' => intval($order->price),
                            'quantity' => intval($order->quantity),
                            'useTax1' => true,
                        ];
                    }

                } else if ($orderState == 'Gift' || $orderState == 'Comp') {
                    $totalOrderCompliment = $totalOrderCompliment + ($order->quantity * $order->price);
                    $indexItem = $this->getIndexItem($itemsCompliment, $order->accurate_code, $order->price, false);
                    if ($indexItem >= 0) {
                        $itemsCompliment[$indexItem]['quantity'] += intval($order->quantity);
                    } else {
                        $itemsCompliment[] = [
                            'itemNo' => $order->accurate_code,
                            'unitPrice' => intval($order->price),
                            'quantity' => intval($order->quantity),
                            'useTax1' => false,
                        ];
                    }
                }

            } else {
                $error[] = 'Menu ' . $order->menu_name . ' portion ' . $order->portion . ' alias code ' . $order->alias_code . ' not yet mapping code';
            }
        }

        $invoiceNumber = $this->GenerateInvoiceNumber($date);

        if (sizeof($itemsCompliment) > 0) {
            $expense = [];

            // make expense
            if ($totalOrderCompliment > 0) {
                $expense[] = [
                    'accountNo' => $account_compliment,
                    'expenseAmount' => intval($totalOrderCompliment * -1),
                ];
            }

            // insert to invoices

            $invoices[] = [
                'transDate' => $transDate,
                'customerNo' => $customer_code,
                'branchName' => $branchName,
                'cashDiscount' => 0,
                'inclusiveTax' => false,
                'number' => $invoiceNumber . '.comp',
                'detailItem' => $itemsCompliment,
                'detailExpense' => $expense,
            ];
        }

        if (sizeof($items) > 0) {

            $discount = 0;
            $service_charge = 0;
            $detailDiscount = [];

            // make service charge
            $service_charge = DB::table('gion_calculations')
                        ->where('name', 'ServiceCharge')
                        ->where('date', $date)
                        ->where('branch_id', $branch_id)
                        ->sum('calculation_amount');

            if($service_charge > 0 ){
                $items[] = [
                    'itemNo' => $service_charge_code,
                    'unitPrice' => intval($service_charge),
                    'quantity' => 1,
                    'useTax1' => true,
                    'detailNotes' => $service_charge_name,
                ];
            }

            // make discount
            $discount = DB::table('gion_calculations')
                        ->where('name','!=', 'ServiceCharge')
                        ->where('date', $date)
                        ->where('branch_id', $branch_id)
                        ->sum('calculation_amount');


            // insert to invoices

            $invoices[] = [
                'transDate' => $transDate,
                'customerNo' => $customer_code,
                'branchName' => $branchName,
                'cashDiscount' => intval(abs($discount)),
                'inclusiveTax' => false,
                'number' => $invoiceNumber,
                'detailItem' => $items,
            ];

            // get payment

            $totalPayment = DB::table('gion_payment_tickets')
                            ->where('gion_payment_tickets.date', $date)
                            ->where('gion_payment_tickets.branch_id', $branch_id)
                            ->sum('amount');

            $totalSales = intval(floor(($totalOrderSubmitted + $discount + $service_charge) * 1.1));

            $paymentSales = DB::table('gion_payment_tickets')
                            ->leftJoin('gion_payments', 'gion_payments.name_pos', '=', 'gion_payment_tickets.payment_type')
                            ->where('gion_payment_tickets.date', $date)
                            ->where('gion_payment_tickets.branch_id', $branch_id)
                            ->select(
                                'gion_payments.bank_coa',
                                DB::raw('sum(gion_payment_tickets.amount) as amount'),
                                'gion_payment_tickets.payment_type'
                            )
                            ->groupByRaw('gion_payments.bank_coa, gion_payment_tickets.payment_type')
                            ->get();

            foreach ($paymentSales as $indexPaymentSale => $paymentSale) {

                $detailInvoice = [];
                $paymentNumber = $invoiceNumber . '.' . Str::slug($paymentSale->payment_type, '.');

                if ($paymentSale->bank_coa == '' && $paymentSale->bank_coa == null) {
                    $error[] = 'Payment ' . $paymentSale->payment_type . ' not yet mapping bank coa';
                }

                if (sizeof($paymentSales) - 1 == $indexPaymentSale) {

                    // get selisih rounding ppn
                    $selisih = $totalSales - $totalPayment;
                    if ($selisih <> 0) {
                        // have selisih
                        if (abs($selisih) < 1000) {
                            $detailDiscount[] = [
                                'accountNo' => $account_no_ppn,
                                'amount' => intval($selisih),
                                'discountNotes' => "Pembulatan Perhitungan Pajak POS",
                            ];
                        } else {
                            $error[] = $date . ' have price gap ' . $selisih;
                        }
                    }

                    // adding selisih to detail discount

                    $totalPaymentLast = $paymentSale->amount + $selisih;

                    $detailInvoice[] = [
                        'invoiceNo' => $invoiceNumber,
                        'paymentAmount' => intval($totalPaymentLast),
                        'detailDiscount' => $detailDiscount,
                    ];

                    $payments[] = [
                        'bankNo' => $paymentSale->bank_coa,
                        'customerNo' => $customer_code,
                        'chequeAmount' => intval($paymentSale->amount),
                        'transDate' => $transDate,
                        'branchName' => $branchName,
                        'number' => $paymentNumber,
                        'detailInvoice' => $detailInvoice,
                    ];

                } else {

                    $detailInvoice[] = [
                        'invoiceNo' => $invoiceNumber,
                        'paymentAmount' => intval($paymentSale->amount),
                    ];

                    $payments[] = [
                        'bankNo' => $paymentSale->bank_coa,
                        'customerNo' => $customer_code,
                        'chequeAmount' => intval($paymentSale->amount),
                        'transDate' => $transDate,
                        'branchName' => $branchName,
                        'number' => $paymentNumber,
                        'detailInvoice' => $detailInvoice,
                    ];

                }
            }

        } else {
            // adding error not have item transaction
            $error[] = "this date not have item transaction";
        }

        if (sizeof($invoices) < 1) {
            $error[] = "this date not have item transaction";
        }

        if (sizeof($error) < 1) {

            try {

                $access_token = Configuration::getValueByKeyFor('interface', 'access_token');
                $database_session = Configuration::getValueByKeyFor('interface', 'database_session');
                $database_host = Configuration::getValueByKeyFor('interface', 'database_host');
                $url_sales_invoice = $database_host . '/accurate/api/sales-invoice/bulk-save.do';
                $url_sales_receipt = $database_host . '/accurate/api/sales-receipt/bulk-save.do';

                $param_invoices = [
                    'data' => $invoices,
                ];

                $response = Http::timeout(180)->withHeaders([
                    'Authorization' => 'Bearer ' . $access_token,
                    'X-Session-ID' => $database_session
                ])->post($url_sales_invoice, $param_invoices);

                if ($response->ok()) {

                    $res = $response->json();

                    if (!$res['s']) {
                        // have failed
                        foreach ($res['d'] as $data) {
                            if (!$data['s']) {
                                foreach ($data['d'] as $dt) {
                                    $contains = Str::contains($dt, 'Sudah ada data lain dengan No Faktur');
                                    if (!$contains) {
                                        $error[] = $dt;
                                    }
                                }
                            }
                        }
                    }

                    if (sizeof($payments) > 0) {
                        $param_receipt = [
                            'data' => $payments,
                        ];

                        $response_receipt = Http::timeout(100)->withHeaders([
                            'Authorization' => 'Bearer ' . $access_token,
                            'X-Session-ID' => $database_session
                        ])->post($url_sales_receipt, $param_receipt);

                        if ($response_receipt->ok()) {
                            $res_receipt = $response_receipt->json();

                            if (!$res_receipt['s']) {
                                // have failed
                                foreach ($res_receipt['d'] as $data) {
                                    if (!$data['s']) {
                                        foreach ($data['d'] as $dt) {
                                            $contains = Str::contains($dt, 'Sudah ada data lain dengan No Bukti');
                                            if (!$contains) {
                                                $error[] = $dt;
                                            }
                                        }
                                    }
                                }
                            }
                            $gionHistorySales = new GionHistorySales();
                            $gionHistorySales->date = $date;
                            $gionHistorySales->branch_id = $branch_id;
                            $gionHistorySales->errors = json_encode($error);
                            if (sizeof($error) < 1) {
                                // no have error
                                $gionHistorySales->status = 'Success';
                            } else {
                                // have error
                                $gionHistorySales->status = 'Failed';
                            }
                            $gionHistorySales->save();
                        } else {
                            $error[] = "Send to API Payment Accurate Failed";
                            $gionHistorySales = new GionHistorySales();
                            $gionHistorySales->date = $date;
                            $gionHistorySales->branch_id = $branch_id;
                            $gionHistorySales->errors = json_encode($error);
                            if (sizeof($error) < 1) {
                                // no have error
                                $gionHistorySales->status = 'Success';
                            } else {
                                // have error
                                $gionHistorySales->status = 'Failed';
                            }
                            $gionHistorySales->save();
                        }
                    } else {
                        $gionHistorySales = new GionHistorySales();
                        $gionHistorySales->date = $date;
                        $gionHistorySales->branch_id = $branch_id;
                        $gionHistorySales->errors = json_encode($error);
                        if (sizeof($error) < 1) {
                            // no have error
                            $gionHistorySales->status = 'Success';
                        } else {
                            // have error
                            $gionHistorySales->status = 'Failed';
                        }
                        $gionHistorySales->save();
                    }
                } else {
                    Log::alert('Acc Gion Error : Response Error');
                    sleep(120);
                    UploadSales::dispatch($this->branch_id, $this->date)->onConnection('gion')->onQueue('gion');
                    // $error[] = "Send to API Invoice Accurate Failed";
                    // $gionHistorySales = new GionHistorySales();
                    // $gionHistorySales->date = $date;
                    // $gionHistorySales->branch_id = $branch_id;
                    // $gionHistorySales->errors = json_encode($error);
                    // if (sizeof($error) < 1) {
                    //     // no have error
                    //     $gionHistorySales->status = 'Success';
                    // } else {
                    //     // have error
                    //     $gionHistorySales->status = 'Failed';
                    // }
                    // $gionHistorySales->save();
                }
            } catch (\Throwable $t) {
                // adding history send tax error
                Log::alert('Acc Gion Error : ' . $t->getMessage());
                sleep(120);
                UploadSales::dispatch($this->branch_id, $this->date)->onConnection('gion')->onQueue('gion');
                // $error[] = $t->getMessage();
                // $gionHistorySales = new GionHistorySales();
                // $gionHistorySales->date = $date;
                // $gionHistorySales->branch_id = $branch_id;
                // $gionHistorySales->errors = json_encode($error);
                // $gionHistorySales->status = 'Failed';
                // $gionHistorySales->save();
            }
        } else {
            $gionHistorySales = new GionHistorySales();
            $gionHistorySales->date = $date;
            $gionHistorySales->branch_id = $branch_id;
            $gionHistorySales->errors = json_encode($error);
            $gionHistorySales->status = 'Failed';
            $gionHistorySales->save();
        }
    }

    public function GenerateInvoiceNumber($date)
    {
        $dt = Carbon::createFromFormat('Y/m/d', $date);
        return 'I.' . $dt->year . '.' . $dt->month . '.' . $dt->day;
    }

    public function getIndexItem($data, $key, $price, $tax)
    {
        $index = -1;
        foreach ($data as $k => $v) {
            if ($key == $v['itemNo'] && $v['useTax1'] == $tax && $v['unitPrice'] == $price) {
                $index = $k;
            }
        }

        return $index;
    }
}
