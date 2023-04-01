<?php

namespace App\Models\Logbook;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Library\Helper;

use App\Models\Plant;

class LbOrganoleptik extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getLastDataByLbAppReviewId($lbAppReviewId, $column)
    {
        $query = DB::table('lb_organoleptiks')
                    ->where('lb_app_review_id', $lbAppReviewId)
                    ->select($column)
                    ->orderBy('updated_at', 'desc');

        $result = '';
        if($query->count() > 0){
            $data = $query->first();
            $result = $data->{$column};
        }
        return $result;
    }

    // report
    public static function getDataReport($plantId, $fromDate, $untilDate)
    {
        $query = DB::table('lb_organoleptiks as lo')
                    ->join('lb_app_reviews as lar', 'lar.id', 'lo.lb_app_review_id')
                    ->where('lar.plant_id', $plantId)
                    ->whereBetween('lar.date', [$fromDate, $untilDate])
                    ->select('lo.id', 'lo.product', 'lo.code',
                            'lo.time', 'lo.taste', 'lo.aroma',
                            'lo.texture', 'lo.color', 'lo.pic', 'lar.date');

        $header = [
            'plant' => Plant::getShortNameById($plantId),
            'date_from' => Helper::DateConvertFormat($fromDate, 'Y/m/d', 'd/m/Y'),
            'date_until' => Helper::DateConvertFormat($untilDate, 'Y/m/d', 'd/m/Y'),
        ];

        return [
            'header' => $header,
            'items' => $query->get(),
        ];
    }

    public static function GenerateReport($type, $param)
    {
        $report = [];

        $report = Self::GenerateReportPdf($param);

        return $report;
    }

    public static function GenerateReportPdf($param)
    {
        $report_data = [
            'title' => Lang::get('ORGANOLEPTIK'),
            'data' => LbOrganoleptik::getDataReport($param->plant, $param->from_date, $param->until_date)
        ];

        $path = 'reports/logbook/organoleptik/pdf/';
        $filename = 'report-organoleptik-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.pdf';
        $pdf = PDF::loadView('logbook.pdf.organoleptik-pdf', $report_data)->setPaper('A4', 'landscape')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        $report = [];
        if (Storage::disk('public')->put($path . $filename . $random . $typefile, $pdf->output())) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        }
        return $report;
    }
}
