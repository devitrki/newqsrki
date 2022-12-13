<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use OwenIt\Auditing\Contracts\Auditable;

use App\Library\Helper;

use App\Models\Plant;

// report
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\Inventory\WasteExport;

class Waste extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getDataReport($companyId, $plantId, $hide, $dateFrom, $dateUntil, $userId)
    {
        $data = [];

        if($plantId != '0'){
            $plants = DB::table('plants')->where('id', $plantId)->select('id')->get();
        } else {
            $plants = Plant::getPlantAuthUser($companyId, $userId);
        }

        foreach ($plants as $plant) {

            $count = DB::table('wastes')
                        ->where('plant_id', $plant->id)
                        ->whereBetween('date', [$dateFrom . '  00:00:00', $dateUntil . '  23:59:59'])
                        ->count();

            if( $count < 1 && sizeof($plants) > 1 && $hide == 'true'){
                continue;
            }

            $wasteItems = DB::table('waste_items')
                            ->join('wastes', 'wastes.id', 'waste_items.waste_id')
                            ->where('wastes.company_id', $companyId)
                            ->where('wastes.plant_id', $plant->id)
                            ->whereBetween('wastes.date', [$dateFrom . '  00:00:00', $dateUntil . '  23:59:59'])
                            ->select([
                                'waste_items.material_code', 'waste_items.material_name', 'waste_items.qty',
                                'waste_items.uom'
                            ]);

            $header = [
                'plant_code' => Plant::getCodeById($plant->id),
                'plant_name' => Plant::getShortNameById($plant->id),
                'date_from' => Helper::DateConvertFormat($dateFrom, 'Y/m/d', 'd/m/Y'),
                'date_until' => Helper::DateConvertFormat($dateUntil, 'Y/m/d', 'd/m/Y')
            ];

            $data[] = [
                'count' => $wasteItems->count(),
                'header' => $header,
                'items' => $wasteItems->get()
            ];
        }

        return [
            'datas' => $data
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
            'title' => Lang::get('Waste / Scrap Report'),
            'data' => Waste::getDataReport($param->company_id, $param->plant, $param->hide, $param->from_date, $param->until_date, $param->user_id)
        ];

        $path = 'reports/inventory/waste/pdf/';
        $filename = 'report-waste-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.pdf';
        $pdf = PDF::loadView('inventory.pdf.waste-pdf', $report_data)->setPaper('A4', 'portrait')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
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
        $path = 'reports/inventory/waste/excel/';
        $filename = 'report-waste-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.xlsx';
        $report = [];
        if (Excel::store(new WasteExport($param->company_id, $param->plant, $param->hide, $param->from_date, $param->until_date, $param->user_id), $path . $filename . $random . $typefile, 'public')) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        };
        return $report;
    }
}
