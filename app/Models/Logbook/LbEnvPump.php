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

class LbEnvPump extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getLastDataByLbAppReviewId($lbAppReviewId, $column)
    {
        $query = DB::table('lb_env_pumps')
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
        $query = DB::table('lb_env_pumps as lep')
                    ->join('lb_app_reviews as lar', 'lar.id', 'lep.lb_app_review_id')
                    ->where('lar.plant_id', $plantId)
                    ->where('lep.month', $month)
                    ->where('lep.year', $year)
                    ->select(['lep.id', 'lar.date',
                        'lep.dgtt', 'lep.hc',
                        'lep.pic', 'lar.plant_id'
                    ]);

        $header = [
            'plant' => Plant::getShortNameById($plantId),
            'year' => $year,
            'month' => Helper::getMonthByNumberMonth($month)
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
            'title' => Lang::get('ENV CONTROL (PRO PUMP)'),
            'data' => LbEnvPump::getDataReport($param->plant, $param->year, $param->month)
        ];

        $path = 'reports/logbook/env-propump/pdf/';
        $filename = 'report-env-propump-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.pdf';
        $pdf = PDF::loadView('logbook.pdf.env-propump-pdf', $report_data)->setPaper('A4', 'portrait')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
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
