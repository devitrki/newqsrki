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


class LbCleanDuties extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getLastDataByLbAppReviewId($lbAppReviewId, $column, $section)
    {
        $query = DB::table('lb_clean_duties')
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
            $qDlyDut = DB::table('lb_clean_duties_dlies as lc')
                        ->join('lb_clean_duties as lcd', 'lcd.id', 'lc.lb_clean_duties_id')
                        ->join('lb_app_reviews as lar', 'lar.id', 'lcd.lb_app_review_id')
                        ->where('lar.plant_id', $plantId)
                        ->where('lar.date', $date)
                        ->where('lcd.section', $section)
                        ->select('lc.task', 'lcd.section', 'lcd.note', 'lc.opening', 'lc.closing', 'lc.midnite', 'lcd.lb_app_review_id');

            $wlyDut = DB::table('lb_clean_duties_wlies as lc')
                        ->join('lb_clean_duties as lcd', 'lcd.id', 'lc.lb_clean_duties_id')
                        ->join('lb_app_reviews as lar', 'lar.id', 'lcd.lb_app_review_id')
                        ->where('lar.plant_id', $plantId)
                        ->where('lar.date', $date)
                        ->where('lcd.section', $section)
                        ->select('lc.task', 'lcd.section', 'lcd.note', 'lc.day', 'lc.pic', 'lc.opening', 'lc.closing', 'lc.midnite', 'lcd.lb_app_review_id')
                        ->get();

            $items = [
                'daily' => $qDlyDut->get(),
                'weekly' => $wlyDut
            ];

            $appReview = [];
            if($qDlyDut->count() > 0){
                $appReview = LbAppReview::getFullDataById($items['daily'][0]->lb_app_review_id);
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
                $qDlyDut = DB::table('lb_clean_duties_dlies as lc')
                            ->join('lb_clean_duties as lcd', 'lcd.id', 'lc.lb_clean_duties_id')
                            ->join('lb_app_reviews as lar', 'lar.id', 'lcd.lb_app_review_id')
                            ->where('lar.plant_id', $plantId)
                            ->where('lar.date', $date)
                            ->where('lcd.section', $sec)
                            ->select('lc.task', 'lcd.section', 'lcd.note', 'lc.opening', 'lc.closing', 'lc.midnite', 'lcd.lb_app_review_id');

                $wlyDut = DB::table('lb_clean_duties_wlies as lc')
                            ->join('lb_clean_duties as lcd', 'lcd.id', 'lc.lb_clean_duties_id')
                            ->join('lb_app_reviews as lar', 'lar.id', 'lcd.lb_app_review_id')
                            ->where('lar.plant_id', $plantId)
                            ->where('lar.date', $date)
                            ->where('lcd.section', $sec)
                            ->select('lc.task', 'lcd.section', 'lcd.note', 'lc.day', 'lc.pic', 'lc.opening', 'lc.closing', 'lc.midnite', 'lcd.lb_app_review_id')
                            ->get();

                $item = [
                    'daily' => $qDlyDut->get(),
                    'weekly' => $wlyDut
                ];

                $appReview = [];
                if($qDlyDut->count() > 0){
                    $appReview = LbAppReview::getFullDataById($item['daily'][0]->lb_app_review_id);
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
            'title' => Lang::get('CLEANING DUTIES'),
            'data' => LbCleanDuties::getDataReport($param->plant, $param->date)
        ];

        $path = 'reports/logbook/cleaning-duties/pdf/';
        $filename = 'report-cleaning-duties-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.pdf';
        $pdf = PDF::loadView('logbook.pdf.cleaning-duties-pdf', $report_data)->setPaper('A4', 'portrait')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
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
