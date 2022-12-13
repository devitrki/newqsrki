<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Lang;
use App\Library\Helper;

use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\Inventory\OutstandingPostoExport;

use App\Services\GrPlantServiceAppsImpl;
use App\Services\GrPlantServiceSapImpl;

use App\Models\Plant;

class Posto extends Model
{
    use HasFactory;

    // report
    public static function getDataReport($plantId)
    {
        $header = [
            'plant' => Plant::getCodeById($plantId) . ' - ' . Plant::getShortNameById($plantId),
        ];

        $grPlantService = new GrPlantServiceSapImpl();
        $response = $grPlantService->getOutstandingPoPlant($plantId, false);
        $outstandingPosto = $response['data'];

        $count = 0;

        if( $outstandingPosto ){
            $count = sizeof($outstandingPosto);
        }

        return [
            'count' => $count,
            'header' => $header,
            'items' => $outstandingPosto,
        ];
    }

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
            'title' => Lang::get('Outstanding PO-STO Report'),
            'data' => Posto::getDataReport($param->plant)
        ];

        $path = 'reports/inventory/outstanding-posto/pdf/';
        $filename = 'report-outstanding-posto-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.pdf';
        $pdf = PDF::loadView('inventory.pdf.outstanding-posto-pdf', $report_data)->setPaper('A4', 'landscape')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
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
        $path = 'reports/inventory/outstanding-posto/excel/';
        $filename = 'report-outstanding-posto-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.xlsx';
        $report = [];
        if (Excel::store(new OutstandingPostoExport($param->plant), $path . $filename . $random . $typefile, 'public')) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        };
        return $report;
    }
}
