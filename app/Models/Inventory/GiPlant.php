<?php

namespace App\Models\Inventory;

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
use App\Exports\Inventory\GIPlantExport;

use App\Models\Inventory\GrPlant;
use App\Models\Plant;

class GiPlant extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getDataDetailById($id)
    {
        $giPlant = DB::table('gi_plants')
                    ->leftJoin('plants as issuing_plant', 'issuing_plant.id', '=', 'gi_plants.issuing_plant_id')
                    ->leftJoin('plants as receiving_plant', 'receiving_plant.id', '=', 'gi_plants.receiving_plant_id')
                    ->select('issuing_plant.code as issuing_plant_code', 'issuing_plant.type as issuing_plant_type',
                            'receiving_plant.code as receiving_plant_code', 'gi_plants.issuer', 'gi_plants.date', 'gi_plants.requester',
                            'issuing_plant.description as issuing_plant_desc', 'receiving_plant.description as receiving_plant_desc',
                            'issuing_plant.address as issuing_plant_address', 'receiving_plant.address as receiving_plant_address',
                            'gi_plants.document_number', 'gi_plants.document_posto', 'gi_plants.company_id', 'gi_plants.receiving_plant_id')
                    ->where('gi_plants.id', $id)
                    ->first();

        $giPlantItem = DB::table('gi_plant_items')
                    ->leftJoin('materials', 'materials.id', '=', 'gi_plant_items.material_id')
                    ->select('materials.code as material_code', 'materials.description as material_desc', 'gi_plant_items.qty',
                            'gi_plant_items.uom', 'gi_plant_items.note')
                    ->where('gi_plant_items.gi_plant_id', $id)
                    ->get();

        return [
            'header' => $giPlant,
            'items' => $giPlantItem
        ];
    }

    public static function getDataReport($plantId, $dateFrom, $dateUntil)
    {
        $plant = DB::table('plants')->where('id', $plantId)->first();

        $header = [
            'plant' => $plant->code . ' - ' . $plant->initital . ' ' . $plant->short_name,
            'date_from' => Helper::DateConvertFormat($dateFrom, 'Y/m/d', 'd/m/Y'),
            'date_until' => Helper::DateConvertFormat($dateUntil, 'Y/m/d', 'd/m/Y')
        ];

        $giPlantItems = DB::table('gi_plant_items')
                    ->leftJoin('gi_plants', 'gi_plants.id', '=', 'gi_plant_items.gi_plant_id')
                    ->leftJoin('plants as receiving_plant', 'receiving_plant.id', '=', 'gi_plants.receiving_plant_id')
                    ->leftJoin('materials', 'materials.id', '=', 'gi_plant_items.material_id')
                    ->select('gi_plants.document_number', 'gi_plants.document_posto', 'materials.code as material_code',
                            'materials.description as material_desc', 'gi_plant_items.qty as gi_qty', 'receiving_plant.initital',
                            'receiving_plant.short_name', 'gi_plants.date as gi_date', 'gi_plant_items.material_id', 'gi_plants.date',
                            'gi_plant_items.uom')
                    ->where('gi_plants.issuing_plant_id', $plantId)
                    ->where('gi_plants.document_number','<>', '')
                    ->where('gi_plants.document_posto','<>', '')
                    ->whereBetween('gi_plants.date', [$dateFrom, $dateUntil]);

        $items = $giPlantItems->get();
        foreach ($items as $item) {
            $gr_qty = GrPlant::getQtyMatByGi($item->document_number, $item->document_posto, $item->material_id);
            $item->gr_qty = $gr_qty;
            $item->gr_outstanding = $item->gi_qty - $gr_qty;
        }

        return [
            'count' => $giPlantItems->count(),
            'header' => $header,
            'items' => $items,
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
            'title' => Lang::get('GI Plant Report'),
            'data' => GiPlant::getDataReport($param->plant, $param->from_date, $param->until_date)
        ];

        $path = 'reports/inventory/gi-plant/pdf/';
        $filename = 'report-gi-plant-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.pdf';
        $pdf = PDF::loadView('inventory.pdf.gi-plant-pdf', $report_data)->setPaper('A4', 'landscape')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
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
        $path = 'reports/inventory/gi-plant/excel/';
        $filename = 'report-gi-plant-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.xlsx';
        $report = [];
        if (Excel::store(new GIPlantExport($param->plant, $param->from_date, $param->until_date), $path . $filename . $random . $typefile, 'public')) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        };
        return $report;
    }
}
