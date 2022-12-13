<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

use App\Library\Helper;

use App\Repositories\SapRepositoryAppsImpl;

use App\Services\StockServiceAppsImpl;
use App\Services\StockServiceSapImpl;

// report
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\Inventory\CurrentStockExport;

class Stock extends Model
{
    const RAW = 'ZROH';
    const SEMI = 'ZHAL';
    const FINISH = 'ZFER';
    const PACK = 'ZVER';
    const PR = 'ZPRP';
    const OS = 'ZHIB';

    public static function getStockPlant($type, $plant_id){
        $plant = DB::table('plants')
                    ->where('id', $plant_id)
                    ->first();

        $param = [
            'type' => $type,
            'plant' => $plant->code
        ];

        $sapRepository = new SapRepositoryAppsImpl(true);
        $sapResponse = $sapRepository->getCurrentStockPlant($param);

        $stocks = [];

        if ($sapResponse['status']) {
            $stocks = $sapResponse['response'];
        }
        return $stocks;
    }

    public static function getMaterialTypeAll(){
        return [
            Stock::RAW,
            Stock::SEMI,
            Stock::FINISH,
            Stock::PACK,
            Stock::PR,
            Stock::OS
        ];
    }

    // report
    public static function getDataReport($plantId, $materialType)
    {
        $header = [
            'plant' => Plant::getCodeById($plantId) . ' - ' . Plant::getShortNameById($plantId),
            'material_type' => $materialType,
        ];

        $stockService = new StockServiceSapImpl();
        $response = $stockService->getCurrentStockPlant($plantId, $materialType);
        $stockSap = $response['data'];

        $size = 0;

        if( $stockSap ){
            $size = sizeof($stockSap);
        }

        return [
            'count' => $size,
            'header' => $header,
            'items' => $stockSap,
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
            'title' => Lang::get('Current Stock Report'),
            'data' => Stock::getDataReport($param->plant, $param->material_type)
        ];

        $path = 'reports/inventory/current-stock/pdf/';
        $filename = 'report-current-stock-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.pdf';
        $pdf = PDF::loadView('inventory.pdf.current-stock-pdf', $report_data)->setPaper('A4', 'portrait')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
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
        $path = 'reports/inventory/current-stock/excel/';
        $filename = 'report-current-stock-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.xlsx';
        $report = [];
        if (Excel::store(new CurrentStockExport($param->plant, $param->material_type), $path . $filename . $random . $typefile, 'public')) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        };
        return $report;
    }
}
