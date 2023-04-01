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

class LbWaterMeter extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getLastDataByLbAppReviewId($lbAppReviewId, $column)
    {
        $query = DB::table('lb_water_meters')
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
    public static function getDataReport($plantId, $year, $month)
    {
        $query = DB::table('lb_water_meters as wm')
                    ->join('lb_app_reviews as lar', 'lar.id', 'wm.lb_app_review_id')
                    ->where('lar.plant_id', $plantId)
                    ->where('wm.month', $month)
                    ->where('wm.year', $year)
                    ->select(['wm.id', 'lar.date',
                        'wm.initial_meter', 'wm.final_meter', 'wm.usage','wm.pic',
                        'wm.lb_app_review_id', 'lar.plant_id'
                    ]);

        $header = [
            'plant' => Plant::getShortNameById($plantId),
            'year' => $year,
            'month' => Helper::getMonthByNumberMonth($month),
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
            'title' => Lang::get('WATER METER FORM'),
            'data' => LbWaterMeter::getDataReport($param->plant, $param->year, $param->month)
        ];

        $path = 'reports/logbook/water-meter/pdf/';
        $filename = 'report-water-meter-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.pdf';
        $pdf = PDF::loadView('logbook.pdf.water-meter-pdf', $report_data)->setPaper('A4', 'portrait')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
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
