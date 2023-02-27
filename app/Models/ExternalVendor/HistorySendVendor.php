<?php

namespace App\Models\ExternalVendor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Library\Helper;

use App\Exports\ExternalVendor\GenerateHistorySendVendor;
use App\Models\Plant;

class HistorySendVendor extends Model
{
    use HasFactory;

    public static function addHistoryFailed($companyId, $date, $sendVendorId, $desc)
    {
        $historySendVendor = new HistorySendVendor;
        $historySendVendor->company_id = $companyId;
        $historySendVendor->date = $date;
        $historySendVendor->send_vendor_id = $sendVendorId;
        $historySendVendor->status = 0;
        $historySendVendor->description = Lang::get($desc);
        $historySendVendor->save();
    }

    public static function getDataReport($plant_id, $dateFrom, $dateUntil, $status)
    {
        $history_send = DB::table('history_send_vendors')
                            ->leftJoin('send_vendors', 'send_vendors.id', '=', 'history_send_vendors.send_vendor_id')
                            ->leftJoin('target_vendors', 'target_vendors.id', '=', 'send_vendors.target_vendor_id')
                            ->leftJoin('template_sales', 'template_sales.id', '=', 'send_vendors.template_sale_id')
                            ->select(
                                'history_send_vendors.date',
                                'history_send_vendors.status',
                                'history_send_vendors.description',
                                'target_vendors.name as target_vendor',
                                'template_sales.name as template_sales',
                                'send_vendors.plant_id',
                            )
                            ->whereBetween('history_send_vendors.date', [$dateFrom, $dateUntil])
                            ->where('send_vendors.plant_id', $plant_id)
                            ->orderBy('history_send_vendors.date');

        $stat = '';
        if ($status == '2') {
            $stat = Lang::get('All');
        } else if ($status == '1') {
            $history_send = $history_send->where('history_send_vendors.status', 1);
            $stat = Lang::get('Success');
        } else {
            $history_send = $history_send->where('history_send_vendors.status', 0);
            $stat = Lang::get('Failed');
        }

        $header = [
            'plant' => Plant::getShortNameById($plant_id),
            'date_from' => Helper::DateConvertFormat($dateFrom, 'Y/m/d', 'd/m/Y'),
            'date_until' => Helper::DateConvertFormat($dateUntil, 'Y/m/d', 'd/m/Y'),
            'status' => $stat
        ];

        $items = $history_send->get();

        foreach ($items as $item) {
            $status_desc = Lang::get('Success');
            if ($item->status != '1') {
                $status_desc = Lang::get('Failed');
            }
            $item->status_desc = $status_desc;
            $item->plant = Plant::getShortNameById($item->plant_id);
        }

        return [
            'count' => $history_send->count(),
            'header' => $header,
            'items' => $items,
        ];
    }

    //  report
    public static function GenerateReport($type, $param)
    {
        $report = [];

        if ($type == 'pdf') {
            $report = Self::GenerateReportPdf($param);
        } else {
            $report = Self::GenerateReportExcel($param);
        }

        return $report;
    }

    public static function GenerateReportPdf($param)
    {
        $report_data = [
            'title' => Lang::get('History Send Vendor'),
            'data' => HistorySendVendor::getDataReport($param->plant, $param->from_date, $param->until_date, $param->status)
        ];

        $path = 'reports/external-vendor/history-send-vendor/pdf/';
        $filename = 'report-history-send-vendor-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.pdf';
        $pdf = PDF::loadView('externalVendors.pdf.history-send-vendor-pdf', $report_data)->setPaper('A4', 'landscape')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        $report = [];
        if (Storage::disk('public')->put($path . $filename . $random . $typefile, $pdf->output())) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        }
        return $report;
    }

    public static function GenerateReportExcel($param)
    {
        $path = 'reports/external-vendor/history-send-vendor/excel/';
        $filename = 'report-history-send-vendor-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.xlsx';
        $report = [];
        if (Excel::store(new GenerateHistorySendVendor($param->plant, $param->from_date, $param->until_date, $param->status), $path . $filename . $random . $typefile, 'public')) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        };
        return $report;
    }
}
