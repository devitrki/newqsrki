<?php

namespace App\Models\Pos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

use Carbon\Carbon;
use App\Library\Helper;

use App\Exports\Pos\PaymentDetailPos;
use App\Exports\Pos\PaymentPosEx;
use App\Exports\Pos\PromotionTypePos;
use App\Exports\Pos\SalesByMenuPos;
use App\Exports\Pos\SalesByInventoryPos;
use App\Exports\Pos\SummaryPaymentPromotionPos;
use App\Exports\Pos\SalesMenuPerHourPos;
use App\Exports\Pos\SalesInventoryPerHourPos;
use App\Exports\Pos\VoidPos;
use App\Exports\Pos\SalesPerHourPos;

use App\Models\Pos;
use App\Models\Plant;
use App\Models\PaymentPos;
use App\Models\Material;
use App\Models\Pos\Aloha;
use App\Models\Pos\Quorion;
use App\Models\Pos\AlohaInterface;
use App\Models\Interfaces\VtecOrderDetail;
use App\Models\Interfaces\VtecOrderPayDetail;
use App\Models\Interfaces\VtecSortPayment;
use App\Models\Interfaces\VtecOrderTransaction;
use App\Models\Interfaces\VtecOrderPromotion;

class AllPos extends Model
{

    public static function getDataPaymentDetailReport($companyId, $date)
    {
        $listStores = Plant::getListStore($companyId);

        $header = [];
        $items = [];
        $flag = true;

        foreach ($listStores as $storeID) {
            $pos_id = Plant::getPosById($storeID);
            $pos = Pos::find($pos_id);

            $dataDatePayment = [
                "date" => Helper::DateConvertFormat($date, 'Y/m/d', 'd.m.Y'),
                "store_code" => Plant::getCustomerCodeById($storeID),
                "store_name" => Plant::getShortNameById($storeID),
                "pos" => $pos->name,
            ];

            $listPayments = PaymentPos::getListPayments();
            $totalPayment = 0;

            $posRepository = Pos::getInstanceRepo($pos);
            $initConnectionAloha = $posRepository->initConnectionDB();
            if (!$initConnectionAloha['status']) {
                continue;
            }

            foreach ($listPayments as $listPayment) {

                if ($flag) {
                    $header[] = $listPayment->title;
                }

                $payAmount = 0;
                $payQty = 0;

                $payAmount = $posRepository->getTotalPaymentByMethodPayment($storeID, $date, $listPayment->method_payment_name);
                $payQty = $posRepository->getTotalQtyByMethodPayment($storeID, $date, $listPayment->method_payment_name);

                $dataDatePayment[$listPayment->title] = $payAmount;
                $dataDatePayment['qty' . $listPayment->title] = $payQty;
                $totalPayment += $payAmount;
            }

            $totalSales = $posRepository->getTotalSales($storeID, $date);

            $selisih = $totalPayment - $totalSales;
            $dataDatePayment['total_payment'] = $totalPayment;
            $dataDatePayment['total_sales'] = $totalSales;
            $dataDatePayment['selisih'] = round($selisih, 2);

            $flag = false;

            $items[] = $dataDatePayment;

        }

        return [
            'headers' => $header,
            'items' => $items,
            'status' => true,
            'message' => ""
        ];
    }

    public static function GeneratePaymentDetailReport($type, $param)
    {
        $report = [];

        $report = Self::GeneratePaymentDetailReportExcel($param);

        return $report;
    }

    public static function GeneratePaymentDetailReportExcel($param)
    {
        $path = 'reports/pos/payment-detail-pos/excel/';
        $filename = 'report-payment-detail-pos-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.xlsx';
        $report = [];
        if (Excel::store(new PaymentDetailPos($param->company_id, $param->date), $path . $filename . $random . $typefile, 'public')) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        };
        return $report;
    }

    // payment pos
    public static function getDataPaymentReport($companyId, $storeID, $dateFrom, $dateUntil)
    {
        $dtFrom = Carbon::createFromFormat('Y/m/d', $dateFrom);
        $dtUntil = Carbon::createFromFormat('Y/m/d', $dateUntil);
        $diffDay = $dtFrom->diffInDays($dtUntil);

        $listPayments = PaymentPos::getListPayments();
        $flag = true;
        $storeCode = Plant::getCustomerCodeById($storeID);
        $storeName = Plant::getShortNameById($storeID);

        $pos_id = Plant::getPosById($storeID);

        if ($pos_id == '') {
            return [
                'headers' => [],
                'items' => [],
                'status' => false,
                'message' => "Store " . $storeName . " Not Yet Mapping POS"
            ];
        }

        $pos = Pos::find($pos_id);

        $header = [];
        $items = [];

        $date = Carbon::createFromFormat('Y/m/d', $dateFrom);

        $posRepository = Pos::getInstanceRepo($pos);
        $initConnectionAloha = $posRepository->initConnectionDB();
        if (!$initConnectionAloha['status']) {
            return [
                'headers' => [],
                'items' => [],
                'status' => false,
                'message' => "Store " . $storeName . " Not Yet Mapping POS Configuration"
            ];
        }

        for ($i = 0; $i <= $diffDay; $i++) {

            $dataDatePayment = [
                "date" => $date->format('d.m.Y'),
                "store_code" => $storeCode,
                "store_name" => $storeName,
                "pos" => $pos->name
            ];

            $totalPayment = 0;

            foreach ($listPayments as $listPayment) {

                if ($flag) {
                    $header[] = $listPayment->title;
                }

                $payAmount = $posRepository->getTotalPaymentByMethodPayment($storeID, $date->format('Y-m-d'), $listPayment->method_payment_name);
                $payQty = $posRepository->getTotalQtyByMethodPayment($storeID, $date->format('Y-m-d'), $listPayment->method_payment_name);

                $dataDatePayment[$listPayment->title] = $payAmount;
                $dataDatePayment['qty' . $listPayment->title] = $payQty;

                $totalPayment += $payAmount;
            }

            $dataDatePayment['total_payment'] = $totalPayment;
            $items[] = $dataDatePayment;

            $flag = false;
            $date->addDay();
        }

        return [
            'headers' => $header,
            'items' => $items,
            'status' => true,
            'message' => ""
        ];
    }

    public static function GeneratePaymentReport($type, $param)
    {
        $report = [];

        $report = Self::GeneratePaymentReportExcel($param);

        return $report;
    }

    public static function GeneratePaymentReportExcel($param)
    {
        $path = 'reports/pos/payment-pos/excel/';
        $filename = 'report-payment-pos-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.xlsx';
        $report = [];
        if (Excel::store(new PaymentPosEx($param->company_id, $param->store, $param->from_date, $param->until_date), $path . $filename . $random . $typefile, 'public')) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        };
        return $report;
    }

    // promotion type
    public static function getDataPromotionTypeReport($companyId, $dateFrom, $dateUntil)
    {
        $dtFrom = Carbon::createFromFormat('Y/m/d', $dateFrom);
        $dtUntil = Carbon::createFromFormat('Y/m/d', $dateUntil);

        $header = [
            'date_from' => $dtFrom->format('d/m/Y'),
            'date_until' => $dtUntil->format('d/m/Y'),
        ];

        $items = [];


        $listStores = Plant::getListStore($companyId);

        foreach ($listStores as $storeID) {
            $pos_id = Plant::getPosById($storeID);

            if ($pos_id == '') {
                continue;
            }

            $pos = Pos::find($pos_id);

            $qLisPromotions = DB::table('vtec_order_promotions as op')
                                ->join('vtec_order_details as od', function ($join) {
                                    $join->on('op.plant_id', '=', 'od.plant_id')
                                        ->on('op.SaleDate', '=', 'od.SaleDate')
                                        ->on('op.OrderDetailID', '=', 'od.OrderDetailID')
                                        ->on('op.TransactionID', '=', 'od.TransactionID')
                                        ->on('op.ComputerID', '=', 'od.ComputerID');
                                })
                                ->select(
                                    'op.PromotionName',
                                    'op.ShopName',
                                    'od.ProductCode',
                                    'od.ProductName',
                                    DB::raw('count(op.TransactionID) as Bill'),
                                    DB::raw('sum(od.TotalQty) as Qty'),
                                    DB::raw('sum(od.TotalQty * od.PricePerUnit) as TotalRetailPrice'),
                                    DB::raw('sum(od.TotalDiscount) as Discount')
                                )
                                ->where('op.TransactionStatusID', 2)
                                ->where('op.plant_id', $storeID)
                                ->whereBetween('op.SaleDate', [$dateFrom, $dateUntil])
                                ->groupBy(
                                    'op.PromotionName',
                                    'op.ShopName',
                                    'od.ProductCode',
                                    'od.ProductName',
                                )
                                ->orderBy('op.PromotionName');

            if( $qLisPromotions->count() > 0 ){
                $lisPromotions = $qLisPromotions->get();
                $items[] = $lisPromotions->toArray();
            }

        }

        return [
            'headers' => $header,
            'items' => $items,
            'status' => true,
            'message' => ""
        ];
    }

    public static function GeneratePromotionTypeReport($type, $param)
    {
        $report = [];

        $report = Self::GeneratePromotionTypeReportExcel($param);

        return $report;
    }

    public static function GeneratePromotionTypeReportExcel($param)
    {
        $path = 'reports/pos/promotion-type-pos/excel/';
        $filename = 'report-promotion-type-pos';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.xlsx';
        $report = [];
        if (Excel::store(new PromotionTypePos($param->from_date, $param->until_date), $path . $filename . $random . $typefile, 'public')) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        };
        return $report;
    }

    // sales by menu
    public static function getDataSalesByMenuReport($companyId, $storeID, $pos, $dateFrom, $dateUntil, $source = 'view'){

        if($source == 'view'){
            $diffDay = Helper::DateDifference(Helper::DateConvertFormat($dateFrom, 'Y/m/d', 'Y-m-d'), Helper::DateConvertFormat($dateUntil, 'Y/m/d', 'Y-m-d'));
            if($diffDay > 7){
                return [
                    'status' => false,
                    'message' => Lang::get("Date range should not be more than 7 days")
                ];
            }
        }

        $storeName = Plant::getShortNameById($storeID);

        if ($pos == 0) {
            $pos_id = Plant::getPosById($storeID);
            if ($pos_id == '') {
                return [
                    'status' => false,
                    'message' => "Store " . $storeName . " Not Yet Mapping POS"
                ];
            }
        } else {
            $pos_id = $pos;
        }

        $pos = Pos::find($pos_id);

        $posRepository = Pos::getInstanceRepo($pos);
        $initConnectionAloha = $posRepository->initConnectionDB();
        if (!$initConnectionAloha['status']) {
            return [
                'status' => false,
                'message' => "Store " . $storeName . " Not Yet Mapping POS Configuration"
            ];
        }

        $header = [
            'store' => $storeName,
            'date_from' => Helper::DateConvertFormat($dateFrom, 'Y/m/d', 'd/m/Y'),
            'date_until' => Helper::DateConvertFormat($dateUntil, 'Y/m/d', 'd/m/Y'),
        ];

        $items = [];

        $salesByMenus = $posRepository->getDataSalesByMenu($storeID, $dateFrom, $dateUntil);

        foreach ($salesByMenus as $salesByMenu) {
            $items[] = (object)[
                'ProductName' => Material::getDescByCode($salesByMenu->BohName),
                'ProductCode' => $salesByMenu->BohName,
                'SaleModeName' => $salesByMenu->SalesMode,
                'ItemType' => $salesByMenu->ItemType,
                'TotalQty' => $salesByMenu->Quantity,
                'NetSales' => $salesByMenu->GrossSales
            ];
        }

        return [
            'status' => true,
            'count' => sizeof($items),
            'header' => $header,
            'items' => $items,
            'message' => ''
        ];
    }

    public static function GenerateSalesByMenuReport($type, $param)
    {
        $report = [];

        if ($type == 'pdf') {
            $report = Self::GenerateSalesByMenuReportPdf($param);
        } else {
            $report = Self::GenerateSalesByMenuReportExcel($param);
        }

        return $report;
    }

    public static function GenerateSalesByMenuReportPdf($param)
    {
        $report_data = [
            'title' => Lang::get('Sales by Menu Pos'),
            'data' => AllPos::getDataSalesByMenuReport($param->company_id, $param->store, $param->pos, $param->from_date, $param->until_date, 'download')
        ];

        $path = 'reports/pos/sales-by-menu-pos/pdf/';
        $filename = 'report-sales-by-menu-pos-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.pdf';
        $pdf = PDF::loadView('pos.pdf.sales-by-menu-pos-pdf', $report_data)->setPaper('A4', 'portrait')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        $report = [];
        if (Storage::disk('public')->put($path . $filename . $random . $typefile, $pdf->output())) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        }
        return $report;
    }

    public static function GenerateSalesByMenuReportExcel($param)
    {
        $path = 'reports/pos/sales-by-menu-pos/excel/';
        $filename = 'report-sales-by-menu-pos-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.xlsx';
        $report = [];
        if (Excel::store(new SalesByMenuPos($param->company_id, $param->store, $param->pos, $param->from_date, $param->until_date), $path . $filename . $random . $typefile, 'public')) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        };
        return $report;
    }

    // sales by inventory

    public static function getDataSalesByInventoryReport($companyId, $storeID, $pos, $dateFrom, $dateUntil, $source = 'view')
    {
        if($source == 'view'){
            $diffDay = Helper::DateDifference(Helper::DateConvertFormat($dateFrom, 'Y/m/d', 'Y-m-d'), Helper::DateConvertFormat($dateUntil, 'Y/m/d', 'Y-m-d'));
            if($diffDay > 7){
                return [
                    'status' => false,
                    'message' => Lang::get("Date range should not be more than 7 days")
                ];
            }
        }

        $storeName = Plant::getShortNameById($storeID);

        if ($pos == 0) {
            $pos_id = Plant::getPosById($storeID);
            if ($pos_id == '') {
                return [
                    'status' => false,
                    'message' => "Store " . $storeName . " Not Yet Mapping POS"
                ];
            }
        } else {
            $pos_id = $pos;
        }

        $pos = Pos::find($pos_id);

        $posRepository = Pos::getInstanceRepo($pos);
        $initConnectionAloha = $posRepository->initConnectionDB();
        if (!$initConnectionAloha['status']) {
            return [
                'status' => false,
                'message' => "Store " . $storeName . " Not Yet Mapping POS Configuration"
            ];
        }

        $header = [
            'store' => $storeName,
            'date_from' => Helper::DateConvertFormat($dateFrom, 'Y/m/d', 'd/m/Y'),
            'date_until' => Helper::DateConvertFormat($dateUntil, 'Y/m/d', 'd/m/Y'),
        ];

        $items = [];

        $salesByInvetories = $posRepository->getDataSalesByInventory($storeID, $dateFrom, $dateUntil);

        foreach ($salesByInvetories as $salesByInventory) {
            $items[] = (object)[
                'ProductName' => Material::getDescByCode($salesByInventory->BohName),
                'ProductCode' => $salesByInventory->BohName,
                'SaleModeName' => $salesByInventory->SalesMode,
                'TotalQty' => $salesByInventory->Quantity
            ];
        }

        return [
            'status' => true,
            'count' => sizeof($items),
            'header' => $header,
            'items' => $items,
            'message' => ''
        ];

        // $storePos = ($pos == 0) ? Plant::getPosById($storeID) : $pos;

        // if( $storePos == 1 ){
        //     // 1 = aloha
        //     return AlohaInterface::getDataSalesByInventoryReport($storeID, $dateFrom, $dateUntil);
        // } else if ( $storePos == 2 ) {
        //     // 2 = vtec
        //     return VtecOrderDetail::getDataReportInventory($storeID, $dateFrom, $dateUntil, $source);
        // } else {
        //     return [
        //         'status' => false,
        //         'message' => Lang::get("POS Outlet Not Yet Mapping")
        //     ];
        // }
    }

    //  report
    public static function GenerateSalesByInventoryReport($type, $param)
    {
        $report = [];

        if ($type == 'pdf') {
            $report = Self::GenerateSalesByInventoryReportPdf($param);
        } else {
            $report = Self::GenerateSalesByInventoryReportExcel($param);
        }

        return $report;
    }

    public static function GenerateSalesByInventoryReportPdf($param)
    {
        $report_data = [
            'title' => Lang::get('Sales by Inventory Pos'),
            'data' => AllPos::getDataSalesByInventoryReport($param->company_id, $param->store, $param->pos, $param->from_date, $param->until_date, 'download')
        ];

        $path = 'reports/pos/sales-by-inventory-pos/pdf/';
        $filename = 'report-sales-by-inventory-pos-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.pdf';
        $pdf = PDF::loadView('pos.pdf.sales-by-inventory-pos-pdf', $report_data)->setPaper('A4', 'portrait')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        $report = [];
        if (Storage::disk('public')->put($path . $filename . $random . $typefile, $pdf->output())) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        }
        return $report;
    }

    public static function GenerateSalesByInventoryReportExcel($param)
    {
        $path = 'reports/pos/sales-by-inventory-pos/excel/';
        $filename = 'report-sales-by-inventory-pos-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.xlsx';
        $report = [];
        if (Excel::store(new SalesByInventoryPos($param->company_id, $param->store, $param->pos, $param->from_date, $param->until_date), $path . $filename . $random . $typefile, 'public')) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        };
        return $report;
    }

    // summary payment promotion

    public static function getDataSummaryPaymentPromotionReport($companyId, $storeID, $pos, $date)
    {
        $storeName = Plant::getShortNameById($storeID);

        if ($pos == 0) {
            $pos_id = Plant::getPosById($storeID);
            if ($pos_id == '') {
                return [
                    'status' => false,
                    'message' => "Store " . $storeName . " Not Yet Mapping POS"
                ];
            }
        } else {
            $pos_id = $pos;
        }

        $pos = Pos::find($pos_id);

        $posRepository = Pos::getInstanceRepo($pos);
        $initConnectionAloha = $posRepository->initConnectionDB();
        if (!$initConnectionAloha['status']) {
            return [
                'status' => false,
                'message' => "Store " . $storeName . " Not Yet Mapping POS Configuration"
            ];
        }

        $header = [
            'store' => $storeName,
            'date' => Helper::DateConvertFormat($date, 'Y/m/d', 'd/m/Y'),
        ];

        $items = $posRepository->getDataSummaryPromotion($storeID, $date);

        return [
            'status' => true,
            'count' => sizeof($items),
            'header' => $header,
            'items' => $items,
            'message' => ''
        ];
    }

    //  report
    public static function GenerateSummaryPaymentPromotionReport($type, $param)
    {
        $report = [];

        $report = Self::GenerateSummaryPaymentPromotionReportExcel($param);

        return $report;
    }

    public static function GenerateSummaryPaymentPromotionReportExcel($param)
    {
        $path = 'reports/pos/summary-payment-promotion-pos/excel/';
        $filename = 'report-summary-payment-promotion-pos-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.xlsx';
        $report = [];
        if (Excel::store(new SummaryPaymentPromotionPos($param->company_id, $param->store, $param->pos, $param->date), $path . $filename . $random . $typefile, 'public')) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        };
        return $report;
    }

    // sales menu per hour

    public static function getDataSalesMenuPerHourReport($companyId, $storeID, $pos, $dateFrom, $dateUntil, $source = 'view')
    {
        if($source == 'view'){
            $diffDay = Helper::DateDifference(Helper::DateConvertFormat($dateFrom, 'Y/m/d', 'Y-m-d'), Helper::DateConvertFormat($dateUntil, 'Y/m/d', 'Y-m-d'));
            if($diffDay > 7){
                return [
                    'status' => false,
                    'message' => Lang::get("Date range should not be more than 7 days")
                ];
            }
        }

        $storeName = Plant::getShortNameById($storeID);

        if ($pos == 0) {
            $pos_id = Plant::getPosById($storeID);
            if ($pos_id == '') {
                return [
                    'status' => false,
                    'message' => "Store " . $storeName . " Not Yet Mapping POS"
                ];
            }
        } else {
            $pos_id = $pos;
        }

        $pos = Pos::find($pos_id);

        $posRepository = Pos::getInstanceRepo($pos);
        $initConnectionAloha = $posRepository->initConnectionDB();
        if (!$initConnectionAloha['status']) {
            return [
                'status' => false,
                'message' => "Store " . $storeName . " Not Yet Mapping POS Configuration"
            ];
        }

        $header = [
            'store' => $storeName,
            'date_from' => Helper::DateConvertFormat($dateFrom, 'Y/m/d', 'd/m/Y'),
            'date_until' => Helper::DateConvertFormat($dateUntil, 'Y/m/d', 'd/m/Y'),
        ];

        $items = $posRepository->getDataSalesMenuPerHour($storeID, $dateFrom, $dateUntil);

        return [
            'status' => true,
            'count' => sizeof($items),
            'header' => $header,
            'items' => $items,
            'message' => ''
        ];
    }

    //  report
    public static function GenerateSalesMenuPerHourReport($type, $param)
    {
        $report = [];

        $report = Self::GenerateSalesMenuPerHourReportExcel($param);

        return $report;
    }

    public static function GenerateSalesMenuPerHourReportExcel($param)
    {
        $path = 'reports/pos/sales-menu-per-hour-pos/excel/';
        $filename = 'report-sales-menu-per-hour-pos-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.xlsx';
        $report = [];
        if (Excel::store(new SalesMenuPerHourPos($param->company_id, $param->store, $param->pos, $param->from_date, $param->until_date), $path . $filename . $random . $typefile, 'public')) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        };
        return $report;
    }

    // sales inventory per hour

    public static function getDataSalesInventoryPerHourReport($companyId, $storeID, $pos, $dateFrom, $dateUntil, $source = 'view')
    {
        if($source == 'view'){
            $diffDay = Helper::DateDifference(Helper::DateConvertFormat($dateFrom, 'Y/m/d', 'Y-m-d'), Helper::DateConvertFormat($dateUntil, 'Y/m/d', 'Y-m-d'));
            if($diffDay > 7){
                return [
                    'status' => false,
                    'message' => Lang::get("Date range should not be more than 7 days")
                ];
            }
        }

        $storeName = Plant::getShortNameById($storeID);

        if ($pos == 0) {
            $pos_id = Plant::getPosById($storeID);
            if ($pos_id == '') {
                return [
                    'status' => false,
                    'message' => "Store " . $storeName . " Not Yet Mapping POS"
                ];
            }
        } else {
            $pos_id = $pos;
        }

        $pos = Pos::find($pos_id);

        $posRepository = Pos::getInstanceRepo($pos);
        $initConnectionAloha = $posRepository->initConnectionDB();
        if (!$initConnectionAloha['status']) {
            return [
                'status' => false,
                'message' => "Store " . $storeName . " Not Yet Mapping POS Configuration"
            ];
        }

        $header = [
            'store' => $storeName,
            'date_from' => Helper::DateConvertFormat($dateFrom, 'Y/m/d', 'd/m/Y'),
            'date_until' => Helper::DateConvertFormat($dateUntil, 'Y/m/d', 'd/m/Y'),
        ];

        $items = $posRepository->getDataSalesInventoryPerHour($storeID, $dateFrom, $dateUntil);

        return [
            'status' => true,
            'count' => sizeof($items),
            'header' => $header,
            'items' => $items,
            'message' => ''
        ];
    }

    //  report
    public static function GenerateSalesInventoryPerHourReport($type, $param)
    {
        $report = [];

        $report = Self::GenerateSalesInventoryPerHourReportExcel($param);

        return $report;
    }

    public static function GenerateSalesInventoryPerHourReportExcel($param)
    {
        $path = 'reports/pos/sales-inventory-per-hour-pos/excel/';
        $filename = 'report-sales-inventory-per-hour-pos-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.xlsx';
        $report = [];
        if (Excel::store(new SalesInventoryPerHourPos($param->company_id, $param->store, $param->pos, $param->from_date, $param->until_date), $path . $filename . $random . $typefile, 'public')) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        };
        return $report;
    }

    // void

    public static function getDataVoidReport($companyId, $storeID, $pos, $dateFrom, $dateUntil, $source = 'view')
    {
        if($source == 'view'){
            $diffDay = Helper::DateDifference(Helper::DateConvertFormat($dateFrom, 'Y/m/d', 'Y-m-d'), Helper::DateConvertFormat($dateUntil, 'Y/m/d', 'Y-m-d'));
            if($diffDay > 7){
                return [
                    'status' => false,
                    'message' => Lang::get("Date range should not be more than 7 days")
                ];
            }
        }

        $storeName = Plant::getShortNameById($storeID);

        if ($pos == 0) {
            $pos_id = Plant::getPosById($storeID);
            if ($pos_id == '') {
                return [
                    'status' => false,
                    'message' => "Store " . $storeName . " Not Yet Mapping POS"
                ];
            }
        } else {
            $pos_id = $pos;
        }

        $pos = Pos::find($pos_id);

        $posRepository = Pos::getInstanceRepo($pos);
        $initConnectionAloha = $posRepository->initConnectionDB();
        if (!$initConnectionAloha['status']) {
            return [
                'status' => false,
                'message' => "Store " . $storeName . " Not Yet Mapping POS Configuration"
            ];
        }

        $header = [
            'store' => $storeName,
            'date_from' => Helper::DateConvertFormat($dateFrom, 'Y/m/d', 'd/m/Y'),
            'date_until' => Helper::DateConvertFormat($dateUntil, 'Y/m/d', 'd/m/Y'),
        ];

        $items = $posRepository->getDataVoid($storeID, $dateFrom, $dateUntil);

        return [
            'status' => true,
            'count' => sizeof($items),
            'header' => $header,
            'items' => $items,
            'message' => ''
        ];
    }

    //  report
    public static function GenerateVoidReport($type, $param)
    {
        $report = [];

        $report = Self::GenerateVoidReportExcel($param);

        return $report;
    }

    public static function GenerateVoidReportExcel($param)
    {
        $path = 'reports/pos/void-pos/excel/';
        $filename = 'report-void-pos-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.xlsx';
        $report = [];
        if (Excel::store(new VoidPos($param->company_id, $param->store, $param->pos, $param->from_date, $param->until_date), $path . $filename . $random . $typefile, 'public')) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        };
        return $report;
    }

    // sales per hour

    public static function getDataSalesPerHourReport($companyId, $storeID, $pos, $dateFrom, $dateUntil, $source = 'view')
    {
        if($source == 'view'){
            $diffDay = Helper::DateDifference(Helper::DateConvertFormat($dateFrom, 'Y/m/d', 'Y-m-d'), Helper::DateConvertFormat($dateUntil, 'Y/m/d', 'Y-m-d'));
            if($diffDay > 7){
                return [
                    'status' => false,
                    'message' => Lang::get("Date range should not be more than 7 days")
                ];
            }
        }

        $storeName = Plant::getShortNameById($storeID);

        if ($pos == 0) {
            $pos_id = Plant::getPosById($storeID);
            if ($pos_id == '') {
                return [
                    'status' => false,
                    'message' => "Store " . $storeName . " Not Yet Mapping POS"
                ];
            }
        } else {
            $pos_id = $pos;
        }

        $pos = Pos::find($pos_id);

        $posRepository = Pos::getInstanceRepo($pos);
        $initConnectionAloha = $posRepository->initConnectionDB();
        if (!$initConnectionAloha['status']) {
            return [
                'status' => false,
                'message' => "Store " . $storeName . " Not Yet Mapping POS Configuration"
            ];
        }

        $header = [
            'store' => $storeName,
            'date_from' => Helper::DateConvertFormat($dateFrom, 'Y/m/d', 'd/m/Y'),
            'date_until' => Helper::DateConvertFormat($dateUntil, 'Y/m/d', 'd/m/Y'),
        ];

        $items = $posRepository->getDataSalesPerHour($storeID, $dateFrom, $dateUntil);

        return [
            'status' => true,
            'count' => sizeof($items),
            'header' => $header,
            'items' => $items,
            'message' => ''
        ];
    }

    //  report
    public static function GenerateSalesPerHourReport($type, $param)
    {
        $report = [];

        $report = Self::GenerateSalesPerHourReportExcel($param);

        return $report;
    }

    public static function GenerateSalesPerHourReportExcel($param)
    {
        $path = 'reports/pos/sales-per-hour-pos/excel/';
        $filename = 'report-sales-per-hour-pos-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.xlsx';
        $report = [];
        if (Excel::store(new SalesPerHourPos($param->company_id, $param->store, $param->pos, $param->from_date, $param->until_date), $path . $filename . $random . $typefile, 'public')) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        };
        return $report;
    }


}
