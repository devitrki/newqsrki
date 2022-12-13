<?php

namespace App\Models\Tax;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Library\Helper;

use App\Exports\Tax\HistorySendFtp;
use App\Models\Plant;

class HistorySendTax extends Model
{
    use HasFactory;

    public static function addHistoryFailed($companyId, $date, $send_tax_id, $desc)
    {
        $historySendTax = new HistorySendTax;
        $historySendTax->company_id = $companyId;
        $historySendTax->date = $date;
        $historySendTax->send_tax_id = $send_tax_id;
        $historySendTax->amount = 0;
        $historySendTax->status = 0;
        $historySendTax->description = Lang::get($desc);
        $historySendTax->save();
    }

    public static function getDataReport($plant_id, $dateFrom, $dateUntil, $status)
    {
        $history_send = DB::table('history_send_taxes')
                            ->leftJoin('send_taxes', 'send_taxes.id', '=', 'history_send_taxes.send_tax_id')
                            ->leftJoin('ftp_governments', 'ftp_governments.id', '=', 'send_taxes.ftp_government_id')
                            ->select('history_send_taxes.date', 'ftp_governments.name', 'send_taxes.plant_id',
                                    'history_send_taxes.amount', 'history_send_taxes.status', 'history_send_taxes.description'
                            )
                            ->whereBetween('history_send_taxes.date', [$dateFrom, $dateUntil])
                            ->where('send_taxes.plant_id', $plant_id)
                            ->orderBy('history_send_taxes.date');

        $stat = '';
        if ($status == '2') {
            $stat = Lang::get('All');
        } else if ($status == '1') {
            $history_send = $history_send->where('history_send_taxes.status', 1);
            $stat = Lang::get('Success');
        } else {
            $history_send = $history_send->where('history_send_taxes.status', 0);
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
            $item->amount = Helper::convertNumberToInd($item->amount, '', 0);
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
            'title' => Lang::get('History Send FTP Tax'),
            'data' => HistorySendTax::getDataReport($param->plant, $param->from_date, $param->until_date, $param->status)
        ];

        $path = 'reports/tax/history-send-ftp/pdf/';
        $filename = 'report-history-send-ftp-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.pdf';
        $pdf = PDF::loadView('tax.pdf.history-send-ftp-pdf', $report_data)->setPaper('A4', 'landscape')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
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
        $path = 'reports/tax/history-send-ftp/excel/';
        $filename = 'report-history-send-ftp-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.xlsx';
        $report = [];
        if (Excel::store(new HistorySendFtp($param->plant, $param->from_date, $param->until_date, $param->status), $path . $filename . $random . $typefile, 'public')) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        };
        return $report;
    }
}
