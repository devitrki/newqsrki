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


class LbDlyInvKitchen extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getLastDataByLbAppReviewId($lbAppReviewId, $column)
    {
        $query = DB::table('lb_dly_inv_kitchens')
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

                $query = DB::table('lb_dly_inv_kitchens as dik')
                            ->leftJoin('lb_app_reviews as ar', 'ar.id', '=', 'dik.lb_app_review_id')
                            ->select('dik.product_name', 'dik.uom', 'dik.frekuensi', 'dik.stock_opening', 'dik.stock_in', 'dik.stock_out',
                            'dik.stock_closing', 'dik.lb_app_review_id', 'dik.note')
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
            $query = DB::table('lb_dly_inv_kitchens as dik')
                    ->leftJoin('lb_app_reviews as ar', 'ar.id', '=', 'dik.lb_app_review_id')
                    ->select('dik.product_name', 'dik.uom', 'dik.frekuensi', 'dik.stock_opening', 'dik.stock_in', 'dik.stock_out',
                    'dik.stock_closing', 'dik.lb_app_review_id', 'dik.note')
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
            'title' => Lang::get('FORM DAILY INVENTORY (KITCHEN)'),
            'data' => LbDlyInvKitchen::getDataReport($param->plant, $param->from_date, $param->until_date)
        ];

        $path = 'reports/logbook/daily-inventory-kitchen/pdf/';
        $filename = 'report-daily-inventory-kitchen-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.pdf';
        $pdf = PDF::loadView('logbook.pdf.daily-inventory-kitchen-pdf', $report_data)->setPaper('A4', 'portrait')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
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
