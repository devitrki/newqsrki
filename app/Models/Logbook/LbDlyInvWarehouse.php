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

class LbDlyInvWarehouse extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getLastDataByLbAppReviewId($lbAppReviewId, $column)
    {
        $query = DB::table('lb_dly_inv_warehouses')
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

                $query = DB::table('lb_dly_inv_warehouses as diw')
                            ->leftJoin('lb_app_reviews as ar', 'ar.id', '=', 'diw.lb_app_review_id')
                            ->select('diw.product_name', 'diw.uom', 'diw.frekuensi', 'diw.stock_opening', 'diw.stock_in_gr_plant',
                            'diw.stock_in_dc', 'diw.stock_in_vendor', 'diw.stock_in_section', 'diw.stock_out_gi_plant', 'diw.stock_out_dc',
                            'diw.stock_out_vendor', 'diw.stock_out_section', 'diw.stock_closing', 'diw.lb_app_review_id', 'diw.note')
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
            $query = DB::table('lb_dly_inv_warehouses as diw')
                    ->leftJoin('lb_app_reviews as ar', 'ar.id', '=', 'diw.lb_app_review_id')
                    ->select('diw.product_name', 'diw.uom', 'diw.frekuensi', 'diw.stock_opening', 'diw.stock_in_gr_plant',
                    'diw.stock_in_dc', 'diw.stock_in_vendor', 'diw.stock_in_section', 'diw.stock_out_gi_plant', 'diw.stock_out_dc',
                    'diw.stock_out_vendor', 'diw.stock_out_section', 'diw.stock_closing', 'diw.lb_app_review_id', 'diw.note')
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
            'title' => Lang::get('FORM DAILY INVENTORY (WAREHOUSE)'),
            'data' => LbDlyInvWarehouse::getDataReport($param->plant, $param->from_date, $param->until_date)
        ];

        $path = 'reports/logbook/daily-inventory-warehouse/pdf/';
        $filename = 'report-daily-inventory-warehouse-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.pdf';
        $pdf = PDF::loadView('logbook.pdf.daily-inventory-warehouse-pdf', $report_data)->setPaper('A4', 'landscape')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
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
