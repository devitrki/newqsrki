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

class LbToilet extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getLastDataByLbAppReviewId($lbAppReviewId, $column)
    {
        $query = DB::table('lb_toilets')
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
    public static function getDataReport($plantId, $date, $shift = null)
    {
        $items = [];
        $header = [];

        $range_shift_1 = Configuration::getValueByKeyFor('logbook', 'range_shift_1');
        $range_shift_2 = Configuration::getValueByKeyFor('logbook', 'range_shift_2');
        $range_shift_3 = Configuration::getValueByKeyFor('logbook', 'range_shift_3');

        if($shift != null){
            $query = DB::table('lb_toilets as lt')
                        ->join('lb_app_reviews as lar', 'lar.id', 'lt.lb_app_review_id')
                        ->where('lar.plant_id', $plantId)
                        ->where('lar.date', $date)
                        ->where('lt.shift', $shift)
                        ->select('lt.id', 'lt.task', 'lt.shift', 'lt.checklis_1', 'lt.checklis_2', 'lt.checklis_3', 'lt.checklis_4', 'lt.checklis_5',
                            'checklis_6', 'lt.checklis_7', 'lt.checklis_8', 'lt.lb_app_review_id');

            $items = $query->get();

            $appReview = [];
            if($query->count() > 0){
                $appReview = LbAppReview::getFullDataById($items[0]->lb_app_review_id);
            }

            $shifts = [];
            if( $shift == 1 ){
                $range_shift_1 = explode(',', $range_shift_1);
                for ($i = trim($range_shift_1[0]); $i <= trim($range_shift_1[1]) ; $i++) {
                    $shifts[] = ($i == 24) ? '0:00' : $i . ':00';
                }
            } else if( $shift == 2 ){
                $range_shift_2 = explode(',', $range_shift_2);
                for ($i = trim($range_shift_2[0]); $i <= trim($range_shift_2[1]) ; $i++) {
                    $shifts[] = ($i == 24) ? '0:00' : $i . ':00';
                }
            } else {
                $range_shift_3 = explode(',', $range_shift_3);
                for ($i = trim($range_shift_3[0]); $i <= trim($range_shift_3[1]) ; $i++) {
                    $shifts[] = ($i == 24) ? '0:00' : $i . ':00';
                }
            }

            $header = [
                'plant' => Plant::getShortNameById($plantId),
                'date' => Helper::DateConvertFormat($date, 'Y/m/d', 'd/m/Y'),
                'shift' => $shift,
                'shifts' => $shifts,
                'appReview' => $appReview
            ];

        } else {
            for ($sh=1; $sh <= 3; $sh++) {
                $query = DB::table('lb_toilets as lt')
                            ->join('lb_app_reviews as lar', 'lar.id', 'lt.lb_app_review_id')
                            ->where('lar.plant_id', $plantId)
                            ->where('lar.date', $date)
                            ->where('lt.shift', $sh)
                            ->select('lt.id', 'lt.task', 'lt.shift', 'lt.checklis_1', 'lt.checklis_2', 'lt.checklis_3', 'lt.checklis_4', 'lt.checklis_5',
                                'checklis_6', 'lt.checklis_7', 'lt.checklis_8', 'lt.lb_app_review_id');

                $item = $query->get();

                $appReview = [];
                if($query->count() > 0){
                    $appReview = LbAppReview::getFullDataById($item[0]->lb_app_review_id);
                }

                $shifts = [];
                if( $sh == 1 ){
                    $range_shift_1 = explode(',', $range_shift_1);
                    for ($i = trim($range_shift_1[0]); $i <= trim($range_shift_1[1]) ; $i++) {
                        $shifts[] = ($i == 24) ? '0:00' : $i . ':00';
                    }
                } else if( $sh == 2 ){
                    $range_shift_2 = explode(',', $range_shift_2);
                    for ($i = trim($range_shift_2[0]); $i <= trim($range_shift_2[1]) ; $i++) {
                        $shifts[] = ($i == 24) ? '0:00' : $i . ':00';
                    }
                } else {
                    $range_shift_3 = explode(',', $range_shift_3);
                    for ($i = trim($range_shift_3[0]); $i <= trim($range_shift_3[1]) ; $i++) {
                        $shifts[] = ($i == 24) ? '0:00' : $i . ':00';
                    }
                }


                $header = [
                    'plant' => Plant::getShortNameById($plantId),
                    'date' => Helper::DateConvertFormat($date, 'Y/m/d', 'd/m/Y'),
                    'shift' => $sh,
                    'shifts' => $shifts,
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
            'title' => Lang::get('TOILET CHECKLIST'),
            'data' => LbToilet::getDataReport($param->plant, $param->date)
        ];

        $path = 'reports/logbook/toilet/pdf/';
        $filename = 'report-toilet-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.pdf';
        $pdf = PDF::loadView('logbook.pdf.toilet-pdf', $report_data)->setPaper('A4', 'portrait')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
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
