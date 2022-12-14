<?php

namespace App\Models\Pos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

use Carbon\Carbon;
use App\Library\Helper;

use App\Exports\Pos\PaymentDetailPos;
use App\Exports\Pos\PaymentPos;
use App\Exports\Pos\PromotionTypePos;
use App\Exports\Pos\SalesByMenuPos;
use App\Exports\Pos\SalesByInventoryPos;
use App\Exports\Pos\SummaryPaymentPromotionPos;
use App\Exports\Pos\SalesMenuPerHourPos;
use App\Exports\Pos\SalesInventoryPerHourPos;
use App\Exports\Pos\VoidPos;
use App\Exports\Pos\SalesPerHourPos;

use App\Models\Plant;
use App\Models\Interfaces\VtecOrderDetail;
use App\Models\Interfaces\VtecOrderPayDetail;
use App\Models\Interfaces\VtecSortPayment;
use App\Models\Interfaces\VtecOrderTransaction;
use App\Models\Interfaces\VtecOrderPromotion;
use App\Models\Pos\Aloha;
use App\Models\Pos\Quorion;
use App\Models\Pos\AlohaInterface;

class AllPos extends Model
{

    public static function getDataPaymentDetailReport($date)
    {
        $listStores = Plant::getListStore();

        $header = [];
        $items = [];
        $flag = true;

        foreach ($listStores as $storeID) {
            $pos = Plant::getPosById($storeID);
            if ( !in_array($pos, [1,2,3]) ) {
                continue;
            }

            $dataDatePayment = [
                "date" => Helper::DateConvertFormat($date, 'Y/m/d', 'd.m.Y'),
                "store_code" => Plant::getCustomerCodeById($storeID),
                "store_name" => Plant::getShortNameById($storeID),
                "pos" => Plant::getPosNameById($storeID),
            ];

            $listPayments = VtecSortPayment::getListPayments();
            $totalPayment = 0;
            foreach ($listPayments as $listPayment) {

                if ($flag) {
                    $header[] = $listPayment->title;
                }

                $payAmount = 0;
                $payQty = 0;

                // 1 = aloha, 2 = vtec, 3 = quorion
                switch ($pos) {
                    case 1:
                        $payAmount = Aloha::getTotalPaymentByMethodPayment($storeID, $date, $listPayment->method_payment_name);
                        $payQty = Aloha::getTotalQtyByMethodPayment($storeID, $date, $listPayment->method_payment_name);
                        break;
                    case 2:
                        $payAmount = VtecOrderPayDetail::getTotalPaymentByMethodPayment($storeID, $date, $listPayment->method_payment_name);
                        $payQty = VtecOrderPayDetail::getTotalQtyByMethodPayment($storeID, $date, $listPayment->method_payment_name);
                        break;
                    case 3:
                        $payAmount = Quorion::getTotalPaymentByMethodPayment($storeID, $date, $listPayment->method_payment_name);
                        $payQty = Quorion::getTotalQtyByMethodPayment($storeID, $date, $listPayment->method_payment_name);
                        break;
                }

                $dataDatePayment[$listPayment->title] = $payAmount;
                $dataDatePayment['qty' . $listPayment->title] = $payQty;
                $totalPayment += $payAmount;
            }

            $totalSales = 0;
            // 1 = aloha, 2 = vtec, 3 = quorion
            switch ($pos) {
                case 1:
                    $totalSales = Aloha::getTotalSales($storeID, $date);
                    break;
                case 2:
                    $totalSales = VtecOrderTransaction::getTotalSales($storeID, $date);
                    break;
                case 3:
                    $totalSales = Quorion::getTotalSales($storeID, $date);
                    break;
            }

            $selisih = $totalPayment - $totalSales;
            $dataDatePayment['total_payment'] = $totalPayment;
            $dataDatePayment['total_sales'] = $totalSales;
            $dataDatePayment['selisih'] = $selisih;

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
        if (Excel::store(new PaymentDetailPos($param->date), $path . $filename . $random . $typefile, 'public')) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        };
        return $report;
    }

    // payment pos
    public static function getDataPaymentReport($storeID, $dateFrom, $dateUntil)
    {
        $dtFrom = Carbon::createFromFormat('Y/m/d', $dateFrom);
        $dtUntil = Carbon::createFromFormat('Y/m/d', $dateUntil);
        $diffDay = $dtFrom->diffInDays($dtUntil);

        $listPayments = VtecSortPayment::getListPayments();
        $flag = true;
        $pos = Plant::getPosById($storeID);
        $storeCode = Plant::getCustomerCodeById($storeID);
        $storeName = Plant::getShortNameById($storeID);

        $header = [];
        $items = [];

        if (!in_array($pos, [1, 2, 3])) {
            return [
                'headers' => $header,
                'items' => $items,
                'status' => false,
                'message' => "Store " . $storeName . " Not Yet Mapping POS"
            ];
        }

        $date = Carbon::createFromFormat('Y/m/d', $dateFrom);

        for ($i = 0; $i <= $diffDay; $i++) {

            $dataDatePayment = [
                "date" => $date->format('d.m.Y'),
                "store_code" => $storeCode,
                "store_name" => $storeName,
                "pos" => Plant::getPosNameById($storeID),
            ];

            $totalPayment = 0;

            foreach ($listPayments as $listPayment) {

                if ($flag) {
                    $header[] = $listPayment->title;
                }

                $payAmount = 0;

                // 1 = aloha, 2 = vtec, 3 = quorion
                switch ($pos) {
                    case 1:
                        $payAmount = Aloha::getTotalPaymentByMethodPayment($storeID, $date->format('Y-m-d'), $listPayment->method_payment_name);
                        $payQty = Aloha::getTotalQtyByMethodPayment($storeID, $date->format('Y-m-d'), $listPayment->method_payment_name);
                        break;
                    case 2:
                        $payAmount = VtecOrderPayDetail::getTotalPaymentByMethodPayment($storeID, $date->format('Y-m-d'), $listPayment->method_payment_name);
                        $payQty = VtecOrderPayDetail::getTotalQtyByMethodPayment($storeID, $date->format('Y-m-d'), $listPayment->method_payment_name);
                        break;
                    case 3:
                        $payAmount = Quorion::getTotalPaymentByMethodPayment($storeID, $date->format('Y-m-d'), $listPayment->method_payment_name);
                        $payQty = Quorion::getTotalQtyByMethodPayment($storeID, $date->format('Y-m-d'), $listPayment->method_payment_name);
                        break;
                }

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
        if (Excel::store(new PaymentPos($param->store, $param->from_date, $param->until_date), $path . $filename . $random . $typefile, 'public')) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        };
        return $report;
    }

    // promotion type
    public static function getDataPromotionTypeReport($dateFrom, $dateUntil)
    {
        $dtFrom = Carbon::createFromFormat('Y/m/d', $dateFrom);
        $dtUntil = Carbon::createFromFormat('Y/m/d', $dateUntil);

        $header = [
            'date_from' => $dtFrom->format('d/m/Y'),
            'date_until' => $dtUntil->format('d/m/Y'),
        ];

        $items = [];


        $listStores = Plant::getListStore();

        foreach ($listStores as $storeID) {
            $pos = Plant::getPosById($storeID);
            // filtered just pos vtec
            if (!in_array($pos, [2])) {
                continue;
            }

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
    public static function getDataSalesByMenuReport($storeID, $pos, $dateFrom, $dateUntil, $source = 'view'){

        if($source == 'view'){
            $diffDay = Helper::DateDifference(Helper::DateConvertFormat($dateFrom, 'Y/m/d', 'Y-m-d'), Helper::DateConvertFormat($dateUntil, 'Y/m/d', 'Y-m-d'));
            if($diffDay > 7){
                return [
                    'status' => false,
                    'message' => \Lang::get("Date range should not be more than 7 days")
                ];
            }
        }

        $storePos = ($pos == 0) ? Plant::getPosById($storeID) : $pos;

        if( $storePos == 1 ){
            // 1 = aloha
            return AlohaInterface::getDataSalesByMenuReport($storeID, $dateFrom, $dateUntil);
        } else if ( $storePos == 2 ) {
            // 2 = vtec
            return  VtecOrderDetail::getDataReport($storeID, $dateFrom, $dateUntil, $source);
        } else {
            return [
                'status' => false,
                'message' => \Lang::get("POS Outlet Not Yet Mapping")
            ];
        }
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
            'title' => \Lang::get('Sales by Menu Pos'),
            'data' => AllPos::getDataSalesByMenuReport($param->store, $param->pos, $param->from_date, $param->until_date, 'download')
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
        if (Excel::store(new SalesByMenuPos($param->store, $param->pos, $param->from_date, $param->until_date), $path . $filename . $random . $typefile, 'public')) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        };
        return $report;
    }

    // sales by inventory

    public static function getDataSalesByInventoryReport($storeID, $pos, $dateFrom, $dateUntil, $source = 'view')
    {
        if($source == 'view'){
            $diffDay = Helper::DateDifference(Helper::DateConvertFormat($dateFrom, 'Y/m/d', 'Y-m-d'), Helper::DateConvertFormat($dateUntil, 'Y/m/d', 'Y-m-d'));
            if($diffDay > 7){
                return [
                    'status' => false,
                    'message' => \Lang::get("Date range should not be more than 7 days")
                ];
            }
        }

        $storePos = ($pos == 0) ? Plant::getPosById($storeID) : $pos;

        if( $storePos == 1 ){
            // 1 = aloha
            return AlohaInterface::getDataSalesByInventoryReport($storeID, $dateFrom, $dateUntil);
        } else if ( $storePos == 2 ) {
            // 2 = vtec
            return VtecOrderDetail::getDataReportInventory($storeID, $dateFrom, $dateUntil, $source);
        } else {
            return [
                'status' => false,
                'message' => \Lang::get("POS Outlet Not Yet Mapping")
            ];
        }
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
            'title' => \Lang::get('Sales by Inventory Pos'),
            'data' => AllPos::getDataSalesByInventoryReport($param->store, $param->pos, $param->from_date, $param->until_date, 'download')
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
        if (Excel::store(new SalesByInventoryPos($param->store, $param->pos, $param->from_date, $param->until_date), $path . $filename . $random . $typefile, 'public')) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        };
        return $report;
    }

    // summary payment promotion

    public static function getDataSummaryPaymentPromotionReport($storeID, $pos, $date)
    {
        $storePos = ($pos == 0) ? Plant::getPosById($storeID) : $pos;

        if( $storePos == 1 ){
            // 1 = aloha
            return Aloha::getDataSummaryPromotionReport($storeID, $date);
        } else if ( $storePos == 2 ) {
            // 2 = vtec
            return VtecOrderPromotion::getDataReport($storeID, $date);
        } else {
            return [
                'status' => false,
                'message' => \Lang::get("POS Outlet Not Yet Mapping")
            ];
        }
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
        if (Excel::store(new SummaryPaymentPromotionPos($param->store, $param->pos, $param->date), $path . $filename . $random . $typefile, 'public')) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        };
        return $report;
    }

    // sales menu per hour

    public static function getDataSalesMenuPerHourReport($storeID, $pos, $dateFrom, $dateUntil, $source = 'view')
    {
        if($source == 'view'){
            $diffDay = Helper::DateDifference(Helper::DateConvertFormat($dateFrom, 'Y/m/d', 'Y-m-d'), Helper::DateConvertFormat($dateUntil, 'Y/m/d', 'Y-m-d'));
            if($diffDay > 7){
                return [
                    'status' => false,
                    'message' => \Lang::get("Date range should not be more than 7 days")
                ];
            }
        }

        $storePos = ($pos == 0) ? Plant::getPosById($storeID) : $pos;

        if( $storePos == 1 ){
            // 1 = aloha
            return Aloha::getDataSalesMenuPerHourReport($storeID, $dateFrom, $dateUntil);
        } else if ( $storePos == 2 ) {
            // 2 = vtec
            return VtecOrderDetail::getDataMenuPerHourReport($storeID, $dateFrom, $dateUntil, $source);
        } else {
            return [
                'status' => false,
                'message' => \Lang::get("POS Outlet Not Yet Mapping")
            ];
        }
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
        if (Excel::store(new SalesMenuPerHourPos($param->store, $param->pos, $param->from_date, $param->until_date), $path . $filename . $random . $typefile, 'public')) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        };
        return $report;
    }

    // sales inventory per hour

    public static function getDataSalesInventoryPerHourReport($storeID, $pos, $dateFrom, $dateUntil, $source = 'view')
    {
        if($source == 'view'){
            $diffDay = Helper::DateDifference(Helper::DateConvertFormat($dateFrom, 'Y/m/d', 'Y-m-d'), Helper::DateConvertFormat($dateUntil, 'Y/m/d', 'Y-m-d'));
            if($diffDay > 7){
                return [
                    'status' => false,
                    'message' => \Lang::get("Date range should not be more than 7 days")
                ];
            }
        }

        $storePos = ($pos == 0) ? Plant::getPosById($storeID) : $pos;

        if( $storePos == 1 ){
            // 1 = aloha
            return Aloha::getDataSalesInventoryPerHourReport($storeID, $dateFrom, $dateUntil);
        } else if ( $storePos == 2 ) {
            // 2 = vtec
            return VtecOrderDetail::getDataInventoryPerHourReport($storeID, $dateFrom, $dateUntil, $source);
        } else {
            return [
                'status' => false,
                'message' => \Lang::get("POS Outlet Not Yet Mapping")
            ];
        }
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
        if (Excel::store(new SalesInventoryPerHourPos($param->store, $param->pos, $param->from_date, $param->until_date), $path . $filename . $random . $typefile, 'public')) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        };
        return $report;
    }

    // void

    public static function getDataVoidReport($storeID, $pos, $dateFrom, $dateUntil, $source = 'view')
    {
        if($source == 'view'){
            $diffDay = Helper::DateDifference(Helper::DateConvertFormat($dateFrom, 'Y/m/d', 'Y-m-d'), Helper::DateConvertFormat($dateUntil, 'Y/m/d', 'Y-m-d'));
            if($diffDay > 7){
                return [
                    'status' => false,
                    'message' => \Lang::get("Date range should not be more than 7 days")
                ];
            }
        }

        $storePos = ($pos == 0) ? Plant::getPosById($storeID) : $pos;

        if( $storePos == 1 ){
            // 1 = aloha
            return Aloha::getDataVoidReport($storeID, $dateFrom, $dateUntil);
        } else if ( $storePos == 2 ) {
            // 2 = vtec
            return VtecOrderDetail::getDataVoidReport($storeID, $dateFrom, $dateUntil, $source);
        } else {
            return [
                'status' => false,
                'message' => \Lang::get("POS Outlet Not Yet Mapping")
            ];
        }
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
        if (Excel::store(new VoidPos($param->store, $param->pos, $param->from_date, $param->until_date), $path . $filename . $random . $typefile, 'public')) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        };
        return $report;
    }

    // sales per hour

    public static function getDataSalesPerHourReport($storeID, $pos, $dateFrom, $dateUntil, $source = 'view')
    {
        if($source == 'view'){
            $diffDay = Helper::DateDifference(Helper::DateConvertFormat($dateFrom, 'Y/m/d', 'Y-m-d'), Helper::DateConvertFormat($dateUntil, 'Y/m/d', 'Y-m-d'));
            if($diffDay > 7){
                return [
                    'status' => false,
                    'message' => \Lang::get("Date range should not be more than 7 days")
                ];
            }
        }

        $storePos = ($pos == 0) ? Plant::getPosById($storeID) : $pos;

        if( $storePos == 1 ){
            // 1 = aloha
            return Aloha::getDataSalesPerHourReport($storeID, $dateFrom, $dateUntil);
        } else if ( $storePos == 2 ) {
            // 2 = vtec
            return VtecOrderDetail::getDataSalesPerHourReport($storeID, $dateFrom, $dateUntil, $source);
        } else {
            return [
                'status' => false,
                'message' => \Lang::get("POS Outlet Not Yet Mapping")
            ];
        }
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
        if (Excel::store(new SalesPerHourPos($param->store, $param->pos, $param->from_date, $param->until_date), $path . $filename . $random . $typefile, 'public')) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        };
        return $report;
    }


}
