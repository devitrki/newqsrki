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

class LbMonSls extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getLastDataByLbAppReviewId($lbAppReviewId, $column)
    {
        $query = DB::table('lb_mon_sls')
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
    public static function getDataReport($plantId, $fromDate, $untilDate)
    {
        $header = [];
        $items = [];

        $dtFrom = Carbon::createFromFormat('Y/m/d', $fromDate);
        $dtUntil = Carbon::createFromFormat('Y/m/d', $untilDate);
        $diffDay = $dtFrom->diffInDays($dtUntil);
        $date = Carbon::createFromFormat('Y/m/d', $fromDate);

        for ($i=0; $i <= $diffDay; $i++) {

            $qAppReview = DB::table('lb_mon_sls as lms')
                            ->join('lb_app_reviews as ar', 'ar.id', 'lms.lb_app_review_id')
                            ->select('ar.id')
                            ->where('ar.plant_id', $plantId)
                            ->where('ar.date', $date->format('Y-m-d'));

            $appReview = [];
            $item = [];

            if($qAppReview->count() > 0){
                $appReview = $qAppReview->first();
                $item = LbMonSls::GetDataRowReport($appReview->id);
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

        return [
            'header' => $header,
            'items' => $items,
        ];
    }

    public static function GetDataRowReport($appReviewId)
    {
        $cashiers = [];
        $cashier_dets = [];
        $lbMonSls = [];
        $lbMonSlsDets = [];

        $listLabels = [
            ['cashier_name', 'Name'],
            ['opening_cash', 'Opening Cash'],
            ['total_sales', 'Total Sales'],
            ['bca', 'BCA'],
            ['mandiri', 'Mandiri'],
            ['go_pay', 'Go-Pay'],
            ['grab_pay', 'Grab-Pay'],
            ['gobiz', 'GoBiz'],
            ['ovo', 'OVO'],
            ['shoope_pay', 'Shoope-Pay'],
            ['dana', 'Dana'],
            ['voucher', 'Voucher'],
            ['delivery_sales', 'Delivery Sales'],
            ['drive_thru', 'Drive Thru'],
            ['compliment', 'Compliment'],
            ['total_cash_hand', 'Total Cash In Hand']
        ];

        $lbMonSls = DB::table('lb_mon_sls as lms')
                        ->where('lms.lb_app_review_id', $appReviewId)
                        ->first();

        $lbMonSlsDets = DB::table('lb_mon_sls_dets')
                        ->where('lb_mon_sls_id', $lbMonSls->id)
                        ->get();

        foreach ($listLabels as $list) {

            $shifts = ['Opening', 'Midnite', 'Closing'];

            $dataCashier = [
                $list[1], //label
            ];

            foreach ($shifts as $sh => $shift) {

                $lbMonSlsCas = DB::table('lb_mon_sls_cas')
                                ->where('lb_mon_sls_id', $lbMonSls->id)
                                ->where('shift', $shift)
                                ->select('id', 'total_sales', 'total_non_cash', 'total_cash', 'brankas_money', 'pending_pc',
                                        'hand_over_by', 'received_by', 'p100', 'p200', 'p500', 'p1000', 'p2000', 'p5000',
                                        'p10000', 'p20000', 'p50000', 'p100000')
                                ->first();

                $totalSales = ( is_numeric($lbMonSlsCas->total_sales) ) ? $lbMonSlsCas->total_sales : 0 ;
                $p100 = ( is_numeric($lbMonSlsCas->p100) ) ? $lbMonSlsCas->p100 : 0 ;
                $total_non_cash = ( is_numeric($lbMonSlsCas->total_non_cash) ) ? $lbMonSlsCas->total_non_cash : 0 ;
                $total_cash = ( is_numeric($lbMonSlsCas->total_cash) ) ? $lbMonSlsCas->total_cash : 0 ;
                $p200 = ( is_numeric($lbMonSlsCas->p200) ) ? Helper::convertNumberToInd($lbMonSlsCas->p200, '', 0) : 0 ;
                $p500 = ( is_numeric($lbMonSlsCas->p500) ) ? Helper::convertNumberToInd($lbMonSlsCas->p500, '', 0) : 0 ;
                $p1000 = ( is_numeric($lbMonSlsCas->p1000) ) ? Helper::convertNumberToInd($lbMonSlsCas->p1000, '', 0) : 0 ;
                $brankas_money = ( is_numeric($lbMonSlsCas->brankas_money) ) ? $lbMonSlsCas->brankas_money : 0 ;
                $pending_pc = ( is_numeric($lbMonSlsCas->pending_pc) ) ? $lbMonSlsCas->pending_pc : 0 ;
                $p2000 = ( is_numeric($lbMonSlsCas->p2000) ) ? Helper::convertNumberToInd($lbMonSlsCas->p2000, '', 0) : 0 ;
                $p5000 = ( is_numeric($lbMonSlsCas->p5000) ) ? Helper::convertNumberToInd($lbMonSlsCas->p5000, '', 0) : 0 ;
                $p10000 = ( is_numeric($lbMonSlsCas->p10000) ) ? Helper::convertNumberToInd($lbMonSlsCas->p10000, '', 0) : 0 ;
                $p20000 = ( is_numeric($lbMonSlsCas->p20000) ) ? Helper::convertNumberToInd($lbMonSlsCas->p20000, '', 0) : 0 ;
                $p50000 = ( is_numeric($lbMonSlsCas->p50000) ) ? Helper::convertNumberToInd($lbMonSlsCas->p50000, '', 0) : 0 ;
                $p100000 = ( is_numeric($lbMonSlsCas->p100000) ) ? Helper::convertNumberToInd($lbMonSlsCas->p100000, '', 0) : 0 ;
                $cashiers['row2'][$sh] = [Helper::convertNumberToInd($totalSales, 'Rp. ', 0)];
                $cashiers['row3'][$sh] = [$p100];
                $cashiers['row4'][$sh] = [Helper::convertNumberToInd($total_non_cash, 'Rp. ', 0), Helper::convertNumberToInd($total_cash, 'Rp. ', 0), $p200];
                $cashiers['row5'][$sh] = [$p500];
                $cashiers['row6'][$sh] = [$p1000];
                $cashiers['row7'][$sh] = [Helper::convertNumberToInd($brankas_money, 'Rp. ', 0), Helper::convertNumberToInd($pending_pc, 'Rp. ', 0), $p2000];
                $cashiers['row8'][$sh] = [$p5000];
                $cashiers['row9'][$sh] = [$p10000];
                $cashiers['row10'][$sh] = [$lbMonSlsCas->hand_over_by, $lbMonSlsCas->received_by, $p20000];
                $cashiers['row11'][$sh] = [$p50000];
                $cashiers['row12'][$sh] = [$p100000];

                for ($i=1; $i <= 4; $i++) {
                    $lbMonSlsCasDet = DB::table('lb_mon_sls_cas_dets')
                                        ->where('lb_mon_sls_cas_id', $lbMonSlsCas->id)
                                        ->where('cashier_no', 'Cashier ' . $i)
                                        ->select($list[0])
                                        ->first();

                    $dataCashier[] = (is_numeric($lbMonSlsCasDet->{$list[0]})) ? Helper::convertNumberToInd($lbMonSlsCasDet->{$list[0]}, '', 0) : $lbMonSlsCasDet->{$list[0]};
                }

            }

            $cashier_dets[] = $dataCashier;

        }

        return [
            'cashiers' => $cashiers,
            'cashier_dets' => $cashier_dets,
            'lbMonSls' => $lbMonSls,
            'lbMonSlsDets' => $lbMonSlsDets,
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
            'title' => Lang::get('MONEY AND SALES HANDLING'),
            'data' => LbMonSls::getDataReport($param->plant, $param->from_date, $param->until_date)
        ];

        $path = 'reports/logbook/money-sales/pdf/';
        $filename = 'report-money-sales-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.pdf';
        $pdf = PDF::loadView('logbook.pdf.money-sales-pdf', $report_data)->setPaper('A3', 'landscape')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
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
