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

class LbDlyWasted extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getLastDataByLbAppReviewId($lbAppReviewId, $column)
    {
        $query = DB::table('lb_dly_wasteds')
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
    public static function getDataReport($plantId, $fromDate, $untilDate = null)
    {
        $header = [];
        $items = [];

        if( $untilDate ){
            $dtFrom = Carbon::createFromFormat('Y/m/d', $fromDate);
            $dtUntil = Carbon::createFromFormat('Y/m/d', $untilDate);
            $diffDay = $dtFrom->diffInDays($dtUntil);
            $date = Carbon::createFromFormat('Y/m/d', $fromDate);

            for ($i=0; $i <= $diffDay; $i++) {

                $query = DB::table('lb_dly_wasteds as ldw')
                            ->leftJoin('lb_app_reviews as ar', 'ar.id', '=', 'ldw.lb_app_review_id')
                            ->join('material_logbooks as ml', 'ml.id', 'ldw.material_logbook_id')
                            ->select('ml.name', 'ldw.uom', 'ldw.qty', 'ldw.time', 'ldw.remark', 'ldw.last_update', 'ldw.lb_app_review_id')
                            ->where('ar.plant_id', $plantId)
                            ->where('ar.date', $date->format('Y-m-d'));

                $item = $query->get();

                $appReview = [];
                if($query->count() > 0){
                    $appReview = LbAppReview::getFullDataById($item[0]->lb_app_review_id);
                }

                $header = [
                    'plant' => Plant::getShortNameById($plantId),
                    'date' => $date->format('d/m/Y'),
                    'appReview' => $appReview
                ];

                $items[] = [
                    'data' => $item,
                    'header' => $header
                ];

                $date->addDay();
            }

        } else {
            $query = DB::table('lb_dly_wasteds as ldw')
                        ->leftJoin('lb_app_reviews as ar', 'ar.id', '=', 'ldw.lb_app_review_id')
                        ->join('material_logbooks as ml', 'ml.id', 'ldw.material_logbook_id')
                        ->select('ml.name', 'ldw.uom', 'ldw.qty', 'ldw.time', 'ldw.remark', 'ldw.last_update', 'ldw.lb_app_review_id')
                        ->where('ar.plant_id', $plantId)
                        ->where('ar.date', $fromDate);

            $items = $query->get();

            $appReview = [];

            if($query->count() > 0){
                $appReview = LbAppReview::getFullDataById($items[0]->lb_app_review_id);
            }

            $header = [
                'plant' => Plant::getShortNameById($plantId),
                'date' => Helper::DateConvertFormat($fromDate, 'Y/m/d', 'd/m/Y'),
                'appReview' => $appReview
            ];
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
            'title' => Lang::get('DAILY WASTED'),
            'data' => LbDlyWasted::getDataReport($param->plant, $param->from_date, $param->until_date)
        ];

        $path = 'reports/logbook/daily-wasted/pdf/';
        $filename = 'report-daily-wasted-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.pdf';
        $pdf = PDF::loadView('logbook.pdf.daily-wasted-pdf', $report_data)->setPaper('A4', 'portrait')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
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
