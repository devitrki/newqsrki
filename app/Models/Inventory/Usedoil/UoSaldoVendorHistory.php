<?php

namespace App\Models\Inventory\Usedoil;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use App\Library\Helper;

use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Exports\Inventory\Usedoil\UoHistorySaldoVendorExport;

class UoSaldoVendorHistory extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getDataReport($companyId, $vendorId, $dateFrom, $dateUntil)
    {
        $data = [];

        if($vendorId != '0'){
            $vendors = DB::table('uo_vendors')
                            ->where('id', $vendorId)
                            ->get();
        } else {
            $vendors = DB::table('uo_vendors')
                            ->where('company_id', $companyId)
                            ->get();
        }

        foreach ($vendors as $vendor) {
            $header = [
                'vendor' => $vendor->name,
                'date_from' => Helper::DateConvertFormat($dateFrom, 'Y/m/d', 'd/m/Y'),
                'date_until' => Helper::DateConvertFormat($dateUntil, 'Y/m/d', 'd/m/Y')
            ];

            $historySaldoVendors = DB::table('uo_saldo_vendor_histories')
                                    ->select([
                                        'date', 'description', 'saldo', 'nominal',
                                    ])
                                    ->where('uo_vendor_id', $vendor->id)
                                    ->whereBetween('date', [$dateFrom . '  00:00:00', $dateUntil . '  23:59:59']);

            $header = [
                'vendor' => $vendor->name,
                'date_from' => Helper::DateConvertFormat($dateFrom, 'Y/m/d', 'd/m/Y'),
                'date_until' => Helper::DateConvertFormat($dateUntil, 'Y/m/d', 'd/m/Y')
            ];

            $data[] = [
                'count' => $historySaldoVendors->count(),
                'header' => $header,
                'items' => $historySaldoVendors->get()
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
            'title' => Lang::get('History Saldo Vendor Used Oil Report'),
            'data' => UoSaldoVendorHistory::getDataReport($param->company_id, $param->vendor, $param->from_date, $param->until_date)
        ];

        $path = 'reports/inventory/uo-history-saldo-vendor/pdf/';
        $filename = 'report-history-saldo-vendor-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.pdf';
        $pdf = PDF::loadView('inventory.usedoil.pdf.uo-history-saldo-vendor-pdf', $report_data)->setPaper('A4', 'portrait')->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
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
        $path = 'reports/inventory/uo-history-saldo-vendor/excel/';
        $filename = 'report-history-saldo-vendor-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.xlsx';
        $report = [];
        if (Excel::store(new UoHistorySaldoVendorExport($param->company_id, $param->vendor, $param->from_date, $param->until_date), $path . $filename . $random . $typefile, 'public')) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        };
        return $report;
    }
}
