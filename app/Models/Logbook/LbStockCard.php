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
use App\Models\MaterialLogbook;

class LbStockCard extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getLastDataByLbAppReviewId($lbAppReviewId, $column)
    {
        $query = DB::table('lb_stock_cards')
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
    public static function getDataReport($plantId, $year, $month, $material)
    {
        $query = DB::table('lb_stock_cards')
                    ->join('lb_app_reviews', 'lb_app_reviews.id', 'lb_stock_cards.lb_app_review_id')
                    ->join('material_logbooks', 'material_logbooks.id', 'lb_stock_cards.material_logbook_id')
                    ->where('lb_app_reviews.plant_id', $plantId)
                    ->where('lb_stock_cards.month', $month)
                    ->where('lb_stock_cards.year', $year)
                    ->where('lb_stock_cards.material_logbook_id', $material)
                    ->select(['lb_stock_cards.id', 'lb_app_reviews.date', 'material_logbooks.name', 'material_logbooks.uom',
                        'lb_stock_cards.no_po', 'lb_stock_cards.stock_initial', 'lb_stock_cards.stock_in_gr',
                        'lb_stock_cards.stock_in_tf', 'lb_stock_cards.stock_out_used', 'lb_stock_cards.stock_out_waste',
                        'lb_stock_cards.stock_out_tf', 'lb_stock_cards.stock_last', 'lb_stock_cards.description',
                        'lb_stock_cards.pic', 'lb_stock_cards.material_logbook_id', 'lb_app_reviews.plant_id'
                    ]);

        $materialLogbook = MaterialLogbook::getDataById($material);
        $header = [
            'plant' => Plant::getShortNameById($plantId),
            'year' => $year,
            'month' => Helper::getMonthByNumberMonth($month),
            'materialName' => $materialLogbook->name,
            'materialUom' => $materialLogbook->uom
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
            'title' => Lang::get('STOCK CARD'),
            'data' => LbStockCard::getDataReport($param->plant, $param->year, $param->month, $param->material)
        ];

        $path = 'reports/logbook/stock-card/pdf/';
        $filename = 'report-stock-card-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.pdf';
        $pdf = PDF::loadView('logbook.pdf.stock-card-pdf', $report_data)->setPaper('A4', 'landscape')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
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
