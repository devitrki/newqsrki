<?php

namespace App\Models\Logbook;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Library\Helper;

use App\Models\Plant;
use App\Models\Configuration;

class LbDlyDuties extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getLastDataByLbAppReviewId($lbAppReviewId, $column, $section)
    {
        $query = DB::table('lb_dly_duties')
                    ->where('lb_app_review_id', $lbAppReviewId)
                    ->where('section', $section)
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
    public static function getDataReport($plantId, $date, $section = null)
    {
        $items = [];
        $header = [];

        if($section != null){
            $query = DB::table('lb_dly_duties_dets as ldd')
                        ->join('lb_dly_duties as ld', 'ld.id', 'ldd.lb_dly_duties_id')
                        ->join('lb_app_reviews as lar', 'lar.id', 'ld.lb_app_review_id')
                        ->where('lar.plant_id', $plantId)
                        ->where('lar.date', $date)
                        ->where('ld.section', $section)
                        ->select('ld.task', 'ld.section', 'ld.note', 'ldd.opening', 'ldd.closing', 'ldd.midnite', 'ld.lb_app_review_id');

            $items = $query->get();

            $appReview = [];
            if($query->count() > 0){
                $appReview = LbAppReview::getFullDataById($items[0]->lb_app_review_id);
            }

            $header = [
                'plant' => Plant::getShortNameById($plantId),
                'date' => Helper::DateConvertFormat($date, 'Y/m/d', 'd/m/Y'),
                'section' => $section,
                'appReview' => $appReview
            ];

        } else {
            $sections = ['Cashier', 'Lobby', 'Kitchen'];
            foreach ($sections as $sec) {
                $query = DB::table('lb_dly_duties_dets as ldd')
                            ->join('lb_dly_duties as ld', 'ld.id', 'ldd.lb_dly_duties_id')
                            ->join('lb_app_reviews as lar', 'lar.id', 'ld.lb_app_review_id')
                            ->where('lar.plant_id', $plantId)
                            ->where('lar.date', $date)
                            ->where('ld.section', $sec)
                            ->select('ld.task', 'ld.section', 'ld.note', 'ldd.opening', 'ldd.closing', 'ldd.midnite', 'ld.lb_app_review_id');

                $item = $query->get();

                $appReview = [];
                if($query->count() > 0){
                    $appReview = LbAppReview::getFullDataById($item[0]->lb_app_review_id);
                }

                $header = [
                    'plant' => Plant::getShortNameById($plantId),
                    'date' => Helper::DateConvertFormat($date, 'Y/m/d', 'd/m/Y'),
                    'section' => $sec,
                    'appReview' => $appReview
                ];

                $items[] = [
                    'data' => $item,
                    'header' => $header
                ];
            }
        }

        return [
            'header' => $header,
            'items' => $items,
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
            'title' => Lang::get('DAILY DUTIES'),
            'data' => LbDlyDuties::getDataReport($param->plant, $param->date)
        ];

        $path = 'reports/logbook/daily-duties/pdf/';
        $filename = 'report-daily-duties-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.pdf';
        $pdf = PDF::loadView('logbook.pdf.daily-duties-pdf', $report_data)->setPaper('A4', 'portrait')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
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
