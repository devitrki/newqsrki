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
use App\Models\Configuration;
use App\Models\Plant;

class LbTemperature extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getLastDataByLbAppReviewId($lbAppReviewId, $column)
    {
        $query = DB::table('lb_temperatures')
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
    public static function getDataReport($plantId, $fromDate, $untilDate, $storage)
    {
        $query = DB::table('lb_temperatures as lt')
                    ->join('lb_app_reviews as lar', 'lar.id', 'lt.lb_app_review_id')
                    ->where('lt.name', $storage)
                    ->where('lar.plant_id', $plantId)
                    ->whereBetween('lar.date', [$fromDate, $untilDate])
                    ->select('lt.id', 'lt.name', 'lt.temp_1', 'lt.temp_2', 'lt.temp_3', 'lt.temp_4', 'lt.temp_5',
                            'lt.note', 'lar.date');

        $start_time_temp = Configuration::getValueByKeyFor('logbook', 'start_time_temp');
        $interval_temp = Configuration::getValueByKeyFor('logbook', 'interval_temp');

        $range_check_temp = [];
        for ($i=1; $i <= 5; $i++) {
            $range_check_temp[] = $start_time_temp . ':00';

            $start_time_temp += $interval_temp;
        }

        $header = [
            'plant' => Plant::getShortNameById($plantId),
            'date_from' => Helper::DateConvertFormat($fromDate, 'Y/m/d', 'd/m/Y'),
            'date_until' => Helper::DateConvertFormat($untilDate, 'Y/m/d', 'd/m/Y'),
            'range_check_temp' => $range_check_temp,
            'storage' => $storage,
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
            'title' => Lang::get('TEMPERATURE FORM'),
            'data' => LbTemperature::getDataReport($param->plant, $param->from_date, $param->until_date, $param->storage)
        ];

        $path = 'reports/logbook/temperature/pdf/';
        $filename = 'report-temperature-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.pdf';
        $pdf = PDF::loadView('logbook.pdf.temperature-pdf', $report_data)->setPaper('A4', 'portrait')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
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
