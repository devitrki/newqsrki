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
        $outstanding = [];
        if( $outstandingPosto ){

            foreach ($outstandingPosto as $v) {
                $remainingQty = $v['schedule_qty'] + $v['gr_qty'];

                $outstanding[] = [
                    'plant_from' => Plant::getShortNameByCode($v['supplying_plant_id']),
                    'sj' => $v['gi_number'],
                    'date' => Helper::DateConvertFormat($v['delivery_date'], 'Y-m-d', 'd/m/Y'),
                    'mat_code' => $v['material_id'],
                    'mat_desc' => $v['material_name'],
                    'uom' => $v['uom_id'],
                    'qty' => $v['schedule_qty'],
                    'qty_out' => $remainingQty,
                ];
            }

            $count = sizeof($outstandingPosto);
        }

        return [
            'count' => $count,
            'header' => $header,
            'items' => $outstanding,
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
