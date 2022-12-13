<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Lang;

use OwenIt\Auditing\Contracts\Auditable;

use App\Library\Helper;

use App\Models\Plant;
use App\Models\Material;

// report
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\Inventory\GRPlantExport;

class GrPlant extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getOutstandingSapByPlantId($plant_id){
        $plant = DB::table('plants')->where('id', $plant_id)->first();
        $response = Http::get(config('qsrki.api.apps.url') . 'recheese/daily-sales/sap/gr/outstanding?plant=' . $plant->code);
        $outstanding = [];
        if ($response->ok()) {
            $outstanding_sap = $response->json();
            if($outstanding_sap){
                foreach ($outstanding_sap as $v) {
                    $plant_from = Plant::getShortNameByCode($v['code_from']);
                    $plant_to = Plant::getShortNameByCode($v['code_to']);
                    $outstanding[] = [
                        'code_from' => $v['code_from'],
                        'code_to' => $v['code_to'],
                        'plant_from' => $plant_from,
                        'plant_to' => $plant_to,
                        'document_number' => $v['document_number'],
                        'mutation_date' => $v['mutation_date'],
                    ];
                }
            }
        }
        return $outstanding;
    }

    public static function getOutstandingDetailByDocNumber($doc_number){
        $response = Http::get(config('qsrki.api.apps.url') . 'recheese/daily-sales/sap/gr/outstanding/detail?doc_number=' . $doc_number);
        $detailOutstanding = [];
        if ($response->ok()) {
            $detailOutstandingSap = $response->json();
            if($detailOutstandingSap['success']){
                $detailOutstanding['header'] = $detailOutstandingSap['header'];
                foreach ($detailOutstandingSap['detail'] as $v) {
                    $material_id = Material::getIdByCode($v['material_code']);
                    $material_desc = Material::getDescByCode($v['material_code']);
                    $detailOutstanding['detail'][] = [
                        'material_code' => $v['material_code'],
                        'material_desc' => $material_desc,
                        'qty_po' => round(Helper::replaceDelimiterNumber($v['qty']), 3),
                        'qty_remaining' => round(Helper::replaceDelimiterNumber($v['qty_outstanding']), 3),
                        'qty_gr' => 0,
                        'uom' => $v['uom'],
                        'material_id' => $material_id,
                        'item_number' => $v['item_number'],
                    ];
                }
            }
        }
        return $detailOutstanding;
    }

    public static function getDataDetailById($id)
    {
        $grPlant = DB::table('gr_plants')
            ->leftJoin('plants as issuing_plant', 'issuing_plant.id', '=', 'gr_plants.issuing_plant_id')
            ->leftJoin('plants as receiving_plant', 'receiving_plant.id', '=', 'gr_plants.receiving_plant_id')
            ->select(
                'issuing_plant.code as issuing_plant_code',
                'issuing_plant.type as issuing_plant_type',
                'receiving_plant.code as receiving_plant_code',
                'gr_plants.date',
                'gr_plants.recepient',
                'issuing_plant.description as issuing_plant_desc',
                'receiving_plant.description as receiving_plant_desc',
                'gr_plants.document_number',
                'gr_plants.delivery_number',
                'gr_plants.posto_number',
            )
            ->where('gr_plants.id', $id)
            ->first();
        $grPlantItem = DB::table('gr_plant_items')
            ->leftJoin('materials', 'materials.id', '=', 'gr_plant_items.material_id')
            ->select(
                'materials.code as material_code',
                'materials.description as material_desc',
                'gr_plant_items.qty_gr',
                'gr_plant_items.qty_b4_gr',
                'gr_plant_items.qty_po',
                'gr_plant_items.qty_remaining',
                'gr_plant_items.uom',
            )
            ->where('gr_plant_items.gr_plant_id', $id)
            ->get();

        return [
            'header' => $grPlant,
            'items' => $grPlantItem
        ];
    }

    public static function getQtyMatByGi($giNumber, $giPosto, $material_id)
    {
        $grPlantItem = DB::table('gr_plant_items')
                        ->leftJoin('gr_plants', 'gr_plants.id', '=', 'gr_plant_items.gr_plant_id')
                        ->select('gr_plant_items.qty_gr')
                        ->where('gr_plants.delivery_number', $giNumber)
                        ->where('gr_plants.posto_number', $giPosto)
                        ->where('gr_plant_items.material_id', $material_id);
        $qty = 0;
        if($grPlantItem->count() > 0){
            $item = $grPlantItem->first();
            $qty = $item->qty_gr;
        }
        return $qty;
    }

    public static function getDataReport($plantId, $dateFrom, $dateUntil)
    {
        $plant = DB::table('plants')->where('id', $plantId)->first();

        $header = [
            'plant' => $plant->code . ' - ' . $plant->initital . ' ' . $plant->short_name,
            'date_from' => Helper::DateConvertFormat($dateFrom, 'Y/m/d', 'd/m/Y'),
            'date_until' => Helper::DateConvertFormat($dateUntil, 'Y/m/d', 'd/m/Y')
        ];

        $grPlantItems = DB::table('gr_plant_items')
                        ->leftJoin('gr_plants', 'gr_plants.id', '=', 'gr_plant_items.gr_plant_id')
                        ->leftJoin('materials', 'materials.id', '=', 'gr_plant_items.material_id')
                        ->leftJoin('plants as issuing_plant', 'issuing_plant.id', '=', 'gr_plants.issuing_plant_id')
                        ->select(
                            'gr_plants.document_number',
                            'gr_plants.delivery_number',
                            'gr_plants.posto_number',
                            'gr_plants.date as receive_date',
                            'materials.code as material_code',
                            'materials.description as material_desc',
                            'gr_plant_items.qty_gr',
                            'gr_plant_items.qty_b4_gr',
                            'gr_plant_items.qty_po',
                            'gr_plant_items.qty_remaining',
                            'issuing_plant.initital',
                            'issuing_plant.short_name',
                            'gr_plant_items.material_id',
                            'gr_plant_items.uom',
                            'gr_plants.recepient',
                        )
                        ->where('gr_plants.receiving_plant_id', $plantId)
                        ->where('gr_plants.delivery_number', '<>', '')
                        ->where('gr_plants.posto_number', '<>', '')
                        ->whereBetween('gr_plants.date', [$dateFrom, $dateUntil]);

        return [
            'count' => $grPlantItems->count(),
            'header' => $header,
            'items' => $grPlantItems->get(),
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
            'title' => Lang::get('GR Plant Report'),
            'data' => GrPlant::getDataReport($param->plant, $param->from_date, $param->until_date)
        ];

        $path = 'reports/inventory/gr-plant/pdf/';
        $filename = 'report-gr-plant-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.pdf';
        $pdf = Pdf::loadView('inventory.pdf.gr-plant-pdf', $report_data)->setPaper('A4', 'landscape')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
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
        $path = 'reports/inventory/gr-plant/excel/';
        $filename = 'report-gr-plant-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.xlsx';
        $report = [];
        if (Excel::store(new GRPlantExport($param->plant, $param->from_date, $param->until_date), $path . $filename . $random . $typefile, 'public')) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        };
        return $report;
    }
}
