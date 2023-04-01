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

class LbBriefing extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getLastDataByLbAppReviewId($lbAppReviewId, $column)
    {
        $query = DB::table('lb_briefings')
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

            $qAppReview = DB::table('lb_briefings as lb')
                            ->join('lb_app_reviews as ar', 'ar.id', 'lb.lb_app_review_id')
                            ->select('ar.id')
                            ->where('ar.plant_id', $plantId)
                            ->where('ar.date', $date->format('Y-m-d'));

            $appReview = [];
            $item = [];

            if($qAppReview->count() > 0){
                $appReview = $qAppReview->first();
                $item = LbBriefing::GetDataRowReport($appReview->id);
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
        $shifts = ['Morning', 'Afternoon', 'Midnite'];
        $data = [];
        foreach ($shifts as $shift) {

            $briefings = DB::table('lb_briefings')
                            ->where('shift', $shift)
                            ->where('lb_app_review_id', $appReviewId)
                            ->select('id', 'sales_target', 'highlight', 'mtd_sales', 'rf_updates')
                            ->first();

            $duties = DB::table('lb_duty_rosters')
                        ->where('lb_briefing_id', $briefings->id)
                        ->select('shift', 'mod', 'cashier', 'kitchen', 'lobby')
                        ->get();

            $rows = [];

            $rows[] = [
                'col1' => 'SALES TARGET',
                'col2' => Helper::convertNumberToInd($briefings->sales_target, '', 0),
                'col3' => 'SHIFT',
                'col4' => $duties[0]->shift,
                'col5' => $duties[1]->shift,
                'col6' => $duties[2]->shift,
                'col7' => $duties[3]->shift,
            ];

            $rows[] = [
                'col1' => 'MTD SALES',
                'col2' => Helper::convertNumberToInd($briefings->mtd_sales, '', 0),
                'col3' => 'MOD',
                'col4' => $duties[0]->mod,
                'col5' => $duties[1]->mod,
                'col6' => $duties[2]->mod,
                'col7' => $duties[3]->mod,
            ];

            $rows[] = [
                'col1' => 'TODAY HIGHLIGHT',
                'col2' => $briefings->highlight,
                'col3' => 'CASHIER',
                'col4' => $duties[0]->cashier,
                'col5' => $duties[1]->cashier,
                'col6' => $duties[2]->cashier,
                'col7' => $duties[3]->cashier,
            ];

            $rows[] = [
                'col1' => 'RF UPDATES',
                'col2' => $briefings->highlight,
                'col3' => 'KITCHEN',
                'col4' => $duties[0]->kitchen,
                'col5' => $duties[1]->kitchen,
                'col6' => $duties[2]->kitchen,
                'col7' => $duties[3]->kitchen,
            ];

            $rows[] = [
                'col1' => '',
                'col2' => '',
                'col3' => 'LOBBY',
                'col4' => $duties[0]->lobby,
                'col5' => $duties[1]->lobby,
                'col6' => $duties[2]->lobby,
                'col7' => $duties[3]->lobby,
            ];

            $data[] = [
                'shift' => $shift,
                'rows' => $rows
            ];
        }

        return $data;
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
            'title' => Lang::get('DAILY BRIEFING & DUTY ROSTER'),
            'data' => LbBriefing::getDataReport($param->plant, $param->from_date, $param->until_date)
        ];

        $path = 'reports/logbook/duty-roster/pdf/';
        $filename = 'report-duty-roster-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.pdf';
        $pdf = PDF::loadView('logbook.pdf.duty-roster-pdf', $report_data)->setPaper('A4', 'portrait')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
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
