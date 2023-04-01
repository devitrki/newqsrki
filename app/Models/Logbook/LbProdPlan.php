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

class LbProdPlan extends Model implements Auditable
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
    public static function getDataReport($plantId, $product, $fromDate, $untilDate)
    {
        $header = [];
        $items = [];

        $dtFrom = Carbon::createFromFormat('Y/m/d', $fromDate);
        $dtUntil = Carbon::createFromFormat('Y/m/d', $untilDate);
        $diffDay = $dtFrom->diffInDays($dtUntil);
        $date = Carbon::createFromFormat('Y/m/d', $fromDate);

        for ($i=0; $i <= $diffDay; $i++) {

            $qAppReview = DB::table('lb_app_reviews')
                            ->select('id')
                            ->where('plant_id', $plantId)
                            ->where('date', $date->format('Y-m-d'));

            $appReview = [];
            $item = [];

            if($qAppReview->count() > 0){
                $appReview = $qAppReview->first();
                $item = LbProdPlan::GetDataRowReport($appReview->id, $product);
            }

            $header = [
                'plant' => Plant::getShortNameById($plantId),
                'product' => $product,
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

    public static function GetDataRowReport($appReviewId, $product)
    {
        $qLbProdPlan = DB::table('lb_prod_plans')
                        ->where('lb_app_review_id', $appReviewId)
                        ->where('product', $product);

        $times = [6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 0, 1, 2, 3, 4, 5];
        $lfryers = ['A', 'B', 'C', 'D'];

        $lbProdPlan = [];
        $lbProdTimes = [];
        $lbProdTemps = [];
        $lbProdTempVerifys = [];
        $lbProdQualities = [];
        $lbProdUsedOil = [];

        if( $qLbProdPlan->count() > 0 ){

            $lbProdPlan = $qLbProdPlan->first();

            // get data for prod time
            foreach ($times as $t) {
                $time = $t . ':00';

                $lbProdTime = DB::table('lb_prod_times')
                                ->where('lb_prod_plan_id', $lbProdPlan->id)
                                ->where('time', $time)
                                ->select('id', 'plan_cooking', 'plan_cooking_total', 'act_cooking', 'act_cooking_total')
                                ->first();

                $lbProdTimes['time'][] = $time;
                $lbProdTimes['planning'][] = $lbProdTime->plan_cooking . ' / ' . $lbProdTime->plan_cooking_total;
                $lbProdTimes['actual'][] = $lbProdTime->act_cooking . ' / ' . $lbProdTime->act_cooking_total;

                $lbProdTimeDetails = DB::table('lb_prod_time_details')
                                        ->where('lb_prod_time_id', $lbProdTime->id)
                                        ->select('quantity', 'exp_prod_code', 'fryer', 'temperature', 'holding_time',
                                            'self_life', 'vendor')
                                        ->get();

                $quantities = [];
                $expProdCodes = [];
                $fryers = [];
                $temperatures = [];
                $holdingTimes = [];
                $selfLifes = [];
                $vendors = [];

                foreach ($lbProdTimeDetails as $lbProdTimeDetail) {
                    $quantities[] = $lbProdTimeDetail->quantity;
                    $expProdCodes[] = $lbProdTimeDetail->exp_prod_code;
                    $fryers[] = $lbProdTimeDetail->fryer;
                    $temperatures[] = $lbProdTimeDetail->temperature;
                    $holdingTimes[] = $lbProdTimeDetail->holding_time;
                    $selfLifes[] = $lbProdTimeDetail->self_life;
                    $vendors[] = $lbProdTimeDetail->vendor;
                }

                $lbProdTimes['quantity'][] = $quantities;
                $lbProdTimes['exp_prod_code'][] = $expProdCodes;
                $lbProdTimes['fryer'][] = $fryers;
                $lbProdTimes['temperature'][] = $temperatures;
                $lbProdTimes['holding_time'][] = $holdingTimes;
                $lbProdTimes['self_life'][] = $selfLifes;
                $lbProdTimes['vendor'][] = $vendors;
            }

            // get prod temp
            $lbProdTemps = DB::table('lb_prod_temps')
                            ->where('lb_prod_plan_id', $lbProdPlan->id)
                            ->select('food_name', 'time', 'fryer_temp', 'product_temp', 'result', 'corrective_action', 'pic')
                            ->get();

            // get prod temp verify
            $lbProdTempVerifys = DB::table('lb_prod_temp_verifies')
                            ->where('lb_prod_plan_id', $lbProdPlan->id)
                            ->select('fryer', 'shift1_temp', 'shift2_temp', 'shift3_temp', 'note')
                            ->get();

            // get prod quality
            $lbProdQualities = [];
            foreach ($lfryers as $lfryer) {
                $lbProdQuality = DB::table('lb_prod_qualities')
                                    ->where('lb_prod_plan_id', $lbProdPlan->id)
                                    ->where('fryer', $lfryer)
                                    ->select('tpm', 'temp', 'oil_status', 'filtration')
                                    ->first();

                $lbProdQualities[$lfryer] = $lbProdQuality;
            }

            // get prod used oil
            $lbProdUsedOil = DB::table('lb_prod_used_oil')
                                ->where('lb_prod_plan_id', $lbProdPlan->id)
                                ->select('stock_first', 'stock_in_gr', 'stock_in_fryer_a', 'stock_in_fryer_b', 'stock_in_fryer_c',
                                    'stock_in_fryer_d', 'stock_change_oil', 'stock_out', 'stock_last')
                                ->first();

        }

        return [
            'lbProdPlan' => $lbProdPlan,
            'lbProdTimes' => $lbProdTimes,
            'lbProdTemps' => $lbProdTemps,
            'lbProdTempVerifys' => $lbProdTempVerifys,
            'lfryers' => $lfryers,
            'lbProdQualities' => $lbProdQualities,
            'lbProdUsedOil' => $lbProdUsedOil,
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
            'title' => Lang::get('PRODUCTION PLANNING'),
            'data' => LbProdPlan::getDataReport($param->plant, $param->product, $param->from_date, $param->until_date)
        ];

        $path = 'reports/logbook/production-planning/pdf/';
        $filename = 'report-production-planning-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.pdf';
        $pdf = PDF::loadView('logbook.pdf.production-planning-pdf', $report_data)->setPaper('A2', 'landscape')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
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
