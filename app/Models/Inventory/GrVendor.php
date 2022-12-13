<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Lang;
use App\Library\Helper;
use App\Models\Plant;
use App\Models\Configuration;

// report
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\Inventory\GRVendorExport;

class GrVendor extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getOutstandingSapByPlantId($plant_id)
    {
        $plantCode = Plant::getCodeById($plant_id);
        $response = Http::get(config('qsrki.api.apps.url') . 'recheese/daily-sales/sap/grvendor/outstanding?plant=' . $plantCode);
        $outstanding = [];

        if ($response->ok()) {
            $outstanding_sap = $response->json();
            foreach ($outstanding_sap as $v) {
                $poDate = Helper::DateConvertFormat($v['EINDT'], 'Ymd', 'Y-m-d');
                $curDate = Date('Y-m-d');
                $diffDays = Helper::DateDifference($poDate, $curDate);
                $vendor_id = round($v['LIFNR']) . '';
                $vendor_allows_day = Configuration::getValueByKeyFor('inventory', 'vendor_allow');
                $vendor_allows_days = explode(',', str_replace(' ', '', $vendor_allows_day) );
                $qty_remaining_po = round($v['MENGE'],3) - round($v['WEMNG'], 3);
                if( ($diffDays > 130 && !in_array( $vendor_id , $vendor_allows_days )) ||  $qty_remaining_po <= 0){
                    continue;
                }

                if( is_numeric($v['MATNR'])){
                    $matCode = $v['MATNR'] + 0;
                } else {
                    $matCode = $v['MATNR'];
                }

                $outstanding[] = [
                    'mandt' => $v['MANDT'],
                    'doc_number' => round($v['EBELN']),
                    'vendor_id' => $vendor_id,
                    'vendor_name' => $v['NAME1'],
                    'item_number' => $v['EBELP'],
                    'material_code' => $matCode . "",
                    'material_desc' => $v['TXZ01'],
                    'po_date' => Helper::DateConvertFormat($v['EINDT'], 'Ymd', 'd-m-Y'),
                    'uom' => $v['MEINS'],
                    'qty_po' => round($v['MENGE'], 3),
                    'qty_remaining_po' => $qty_remaining_po,
                    'elikz' => $v['ELIKZ'],
                    'plant_id' => $plant_id,

                ];
            }
        }
        return $outstanding;
    }

    public static function getDataReport($plantId, $dateFrom, $dateUntil)
    {
        $plant = DB::table('plants')->where('id', $plantId)->first();

        $header = [
            'plant_code' => $plant->code,
            'plant_name' => $plant->initital . ' ' . $plant->short_name,
            'date_from' => Helper::DateConvertFormat($dateFrom, 'Y/m/d', 'd/m/Y'),
            'date_until' => Helper::DateConvertFormat($dateUntil, 'Y/m/d', 'd/m/Y')
        ];

        $grVendors = DB::table('gr_vendors')
            ->select([
                'gr_vendors.id', 'gr_vendors.gr_number', 'gr_vendors.po_number', 'gr_vendors.ref_number', 'gr_vendors.po_date',
                'gr_vendors.posting_date', 'gr_vendors.vendor_id', 'gr_vendors.vendor_name', 'gr_vendors.vendor_name',
                'gr_vendors.material_code', 'gr_vendors.material_desc', 'gr_vendors.qty_po', 'gr_vendors.qty_remaining_po',
                'gr_vendors.qty_gr', 'gr_vendors.qty_remaining', 'gr_vendors.batch', 'gr_vendors.uom', 'gr_vendors.recepient',
                'gr_vendors.created_at'
            ])
            ->where('gr_vendors.plant_id', $plantId)
            ->whereBetween('gr_vendors.posting_date', [$dateFrom, $dateUntil]);

        return [
            'count' => $grVendors->count(),
            'header' => $header,
            'items' => $grVendors->get(),
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
            'title' => Lang::get('GR PO Vendor Report'),
            'data' => GrVendor::getDataReport($param->plant, $param->from_date, $param->until_date)
        ];

        $path = 'reports/inventory/gr-vendor/pdf/';
        $filename = 'report-gr-po-vendor-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.pdf';
        $pdf = PDF::loadView('inventory.pdf.gr-vendor-pdf', $report_data)->setPaper('A4', 'landscape')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
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
        $path = 'reports/inventory/gr-vendor/excel/';
        $filename = 'report-gr-po-vendor-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.xlsx';
        $report = [];
        if (Excel::store(new GRVendorExport($param->plant, $param->from_date, $param->until_date), $path . $filename . $random . $typefile, 'public')) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        };
        return $report;
    }
}
