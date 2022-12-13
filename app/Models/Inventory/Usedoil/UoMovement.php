<?php

namespace App\Models\Inventory\Usedoil;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use OwenIt\Auditing\Contracts\Auditable;

use App\Library\Helper;

// report
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\Inventory\Usedoil\UoIncomeSalesSummaryExport;

use App\Models\Plant;


class UoMovement extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getDocumentNumberReverse($id){
        $qMovement = DB::table('uo_movements')
                        ->where('id', $id)
                        ->select('document_number');

        $documentNumber = '';
        if ( $qMovement->count() > 0 ) {
            $movement = $qMovement->first();
            $documentNumber = $movement->document_number;
        }
        return $documentNumber;
    }

    // report

    public static function getDataReport($companyId, $dateFrom, $dateUntil)
    {
        $movements = DB::table('uo_movements')
                        ->where('uo_movements.company_id', $companyId)
                        ->where('uo_movements.type', 201)
                        ->whereBetween('uo_movements.date', [$dateFrom . '  00:00:00', $dateUntil . '  23:59:59'])
                        ->select( 'plant_id_sender', DB::raw('SUM( subtotal ) as total_sales'))
                        ->groupBy('plant_id_sender');

        $header = [
            'date_from' => Helper::DateConvertFormat($dateFrom, 'Y/m/d', 'd/m/Y'),
            'date_until' => Helper::DateConvertFormat($dateUntil, 'Y/m/d', 'd/m/Y')
        ];

        return [
            'count' => $movements->count(),
            'header' => $header,
            'items' => $movements->get()
        ];
    }

    //  report
    public static function GenerateReport($type, $param)
    {
        $report = [];

        if ($type == 'pdf') {
            $report = Self::GenerateReportPdf($param);
        } else {
            $report = Self::GenerateReportExcel($param);
        }

        return $report;
    }

    public static function GenerateReportPdf($param)
    {
        $report_data = [
            'title' => Lang::get('Income Sales Summary Used Oil Report'),
            'data' => UoMovement::getDataReport($param->company_id, $param->from_date, $param->until_date)
        ];

        $path = 'reports/inventory/uo-income-sales-summary/pdf/';
        $filename = 'report-income-sales-summary-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.pdf';
        $pdf = PDF::loadView('inventory.usedoil.pdf.uo-income-sales-summary-pdf', $report_data)->setPaper('A4', 'portrait')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        $report = [];
        if (Storage::disk('public')->put($path . $filename . $random . $typefile, $pdf->output())) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        }
        return $report;
    }

    public static function GenerateReportExcel($param)
    {
        $path = 'reports/inventory/uo-income-sales-summary/excel/';
        $filename = 'report-income-sales-summary-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.xlsx';
        $report = [];
        if (Excel::store(new UoIncomeSalesSummaryExport($param->company_id, $param->from_date, $param->until_date), $path . $filename . $random . $typefile, 'public')) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        };
        return $report;
    }
}
