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
use App\Exports\Inventory\Usedoil\UoIncomeSalesDetailExport;

use App\Models\Plant;

class UoMovementItem extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public $timestamps = false;

    public static function getDataReport($companyId, $plantId, $dateFrom, $dateUntil, $userId)
    {
        $data = [];

        if($plantId != '0'){
            $plants = DB::table('plants')->where('id', $plantId)->select('id')->get();
        } else {
            $plants = Plant::getPlantAuthUser($companyId, $userId);
        }

        foreach ($plants as $plant) {

            $count = DB::table('uo_movements')
                        ->whereIn('type', [201])
                        ->where('plant_id_sender', $plant->id)
                        ->whereBetween('date', [$dateFrom . '  00:00:00', $dateUntil . '  23:59:59'])
                        ->count();

            if( $count < 1 && sizeof($plants) > 1 ){
                continue;
            }

            $movementItems = DB::table('uo_movement_items')
                                ->join('uo_movements', 'uo_movements.id', 'uo_movement_items.uo_movement_id')
                                ->join('plants', 'plants.id', 'uo_movements.plant_id_sender')
                                ->join('uo_vendors', 'uo_vendors.id', 'uo_movements.uo_vendor_id')
                                ->where('uo_movements.plant_id_sender', $plant->id)
                                ->where('uo_movements.type', 201)
                                ->whereBetween('uo_movements.date', [$dateFrom . '  00:00:00', $dateUntil . '  23:59:59'])
                                ->select([
                                    'uo_movements.date', 'uo_movements.document_number', 'uo_movement_items.material_name',
                                    'uo_movement_items.price', 'uo_movement_items.qty', 'plants.code', 'plants.short_name',
                                    'plants.initital', 'uo_vendors.name as vendor_name'
                                ]);

            $header = [
                'plant' => Plant::getCodeById($plant->id) . ' - ' . Plant::getShortNameById($plant->id),
                'date_from' => Helper::DateConvertFormat($dateFrom, 'Y/m/d', 'd/m/Y'),
                'date_until' => Helper::DateConvertFormat($dateUntil, 'Y/m/d', 'd/m/Y')
            ];

            $data[] = [
                'count' => $movementItems->count(),
                'header' => $header,
                'items' => $movementItems->get()
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
            'title' => Lang::get('Income Sales Detail Used Oil Report'),
            'data' => UoMovementItem::getDataReport($param->company_id, $param->plant, $param->from_date, $param->until_date, $param->user_id)
        ];

        $path = 'reports/inventory/uo-income-sales-detail/pdf/';
        $filename = 'report-income-sales-detail-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.pdf';
        $pdf = PDF::loadView('inventory.usedoil.pdf.uo-income-sales-detail-pdf', $report_data)->setPaper('A4', 'portrait')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
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
        $path = 'reports/inventory/uo-income-sales-detail/excel/';
        $filename = 'report-income-sales-detail-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.xlsx';
        $report = [];
        if (Excel::store(new UoIncomeSalesDetailExport($param->company_id, $param->plant, $param->from_date, $param->until_date, $param->user_id), $path . $filename . $random . $typefile, 'public')) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        };
        return $report;
    }
}
