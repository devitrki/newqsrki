<?php

namespace App\Models\Financeacc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Facades\Excel;

use App\Library\Helper;

use App\Exports\Financeacc\SelisihAssetSoExport;
use App\Exports\Financeacc\SelisihAssetSoReportExport;
use App\Exports\Financeacc\AssetSoReportExport;

use App\Models\Plant;

class AssetSo extends Model
{
    use HasFactory;

    public static function generateUploadCode($plant, $costCenterCode)
    {
        $plantCode = Plant::getCodeById($plant);
        $dateCode = Date('nY');
        $randomCode = rand(1000, 9999);
        return $plantCode . $costCenterCode . $dateCode . $randomCode;
    }

    public static function GenerateSelisihSoExcel($assetSoId, $typePlant, $purposeToId)
    {
        $assetSo = DB::table('asset_sos')
                    ->where('id', $assetSoId)
                    ->first();

        $path = 'reports/financeacc/selisih-asset-so/excel/';

        if ($typePlant != 'dc') {
            if($purposeToId != '0'){
                // am
                $filename = 'am-sel-ast-so-outlet-' . $assetSo->month . '-' . $assetSo->year;
            } else {
                // depart asset
                $filename = 'da-sel-ast-so-outlet-' . $assetSo->month . '-' . $assetSo->year;
            }
        } else {
            if ($purposeToId != '0') {
                // am
                $filename = 'am-sel-ast-so-dc-' . $assetSo->month . '-' . $assetSo->year;
            } else {
                // depart asset
                $filename = 'da-sel-ast-so-dc-' . $assetSo->month . '-' . $assetSo->year;
            }
        }

        $typefile = '.xlsx';

        $report = [];
        if (Excel::store(new SelisihAssetSoExport($assetSoId, $typePlant, $purposeToId), $path . $filename . $typefile, 'public')) {
            $report = [
                'path' => $path,
                'filename' => $filename . $typefile
            ];
        };
        return $report;
    }

    // report
    public static function getDataAssetSoReport($companyId, $plantId, $costCenter, $periode)
    {
        $query = DB::table('asset_so_details')
                    ->join('asset_so_plants', 'asset_so_plants.id', 'asset_so_details.asset_so_plant_id')
                    ->where('asset_so_plants.company_id', $companyId)
                    ->where('asset_so_plants.plant_id', $plantId)
                    ->where('asset_so_plants.cost_center_code', $costCenter)
                    ->select('asset_so_details.*')
                    ->where('asset_so_plants.asset_so_id', $periode);

        $plant = DB::table('plants')
                    ->where('id', $plantId)
                    ->first();

        $assetSo = DB::table('asset_sos')
                    ->where('company_id', $companyId)
                    ->where('id', $periode)
                    ->first();

        $qAssetSoPlant = DB::table('asset_so_plants')
                            ->where('company_id', $companyId)
                            ->where('asset_so_id', $periode)
                            ->where('plant_id', $plantId)
                            ->where('cost_center_code', $costCenter);

        $apCostCenterCode = '';
        $apCostCenter = '';
        $apNote = '';

        if($qAssetSoPlant->count() > 0){
            $assetSoPlant = $qAssetSoPlant->first();

            $apCostCenterCode = $assetSoPlant->cost_center_code;
            $apCostCenter = $assetSoPlant->cost_center;
            $apNote = $assetSoPlant->note;
        }

        $header = [
            'plant' => $plant->code . ' - ' . $plant->initital . ' ' . $plant->short_name,
            'costcenter' => $apCostCenterCode . ' - ' . $apCostCenter,
            'periode' => $assetSo->month_label . ' ' . $assetSo->year,
            'note' => $apNote,
        ];

        return [
            'count' => $query->count(),
            'header' => $header,
            'items' => $query->get()
        ];
    }

    public static function GenerateAssetSoReport($type, $param)
    {
        $report = [];

        $report = Self::GenerateAssetSoReportExcel($param);

        return $report;
    }

    public static function GenerateAssetSoReportExcel($param)
    {
        $path = 'reports/financeacc/asset-so/excel/';
        $filename = 'report-asset-so-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.xlsx';
        $report = [];
        if (Excel::store(new AssetSoReportExport($param->company_id, $param->plant, $param->costcenter, $param->periode), $path . $filename . $random . $typefile, 'public')) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        };
        return $report;
    }

    // report selisih
    public static function getDataSelisihAssetSoReport($companyId, $plantId, $periode, $userID)
    {
        $query = DB::table('asset_so_details')
                    ->join('asset_so_plants', 'asset_so_plants.id', 'asset_so_details.asset_so_plant_id')
                    ->join('asset_sos', 'asset_sos.id', 'asset_so_plants.asset_so_id')
                    ->join('plants', 'plants.id', 'asset_so_plants.plant_id')
                    ->select('asset_so_details.*', 'asset_so_plants.cost_center', 'asset_so_plants.cost_center_code', 'plants.initital', 'plants.short_name')
                    ->where('asset_so_plants.company_id', $companyId)
                    ->where('asset_sos.id', $periode)
                    ->whereRaw('CASE WHEN plants.type = 1 THEN asset_sos.status_submit_outlet = 1 ELSE asset_sos.status_submit_dc = 1 END')
                    ->where('asset_so_details.qty_selisih', '<>', '0');

        $assetSo = DB::table('asset_sos')->where('id', $periode)->first();

        if ($plantId != '0') {
            $plant = DB::table('plants')->where('id', $plantId)->first();
            $header = [
                'plant' => $plant->code . ' - ' . $plant->initital . ' ' . $plant->short_name,
                'periode' => $assetSo->month_label . ' ' . $assetSo->year
            ];
            $query = $query->where('asset_so_plants.plant_id', $plantId);
        } else {
            $plants_auth = Plant::getPlantsIdByUserId($userID);
            $plants = explode(',', $plants_auth);

            if (!in_array('0', $plants)) {
                $query = $query->whereIn('asset_so_plants.plant_id', $plants);
            }

            $header = [
                'plant' => "All",
                'periode' => $assetSo->month_label . ' ' . $assetSo->year
            ];
        }

        return [
            'count' => $query->count(),
            'header' => $header,
            'items' => $query->get()
        ];
    }

    public static function GenerateSelisihAssetSoReport($type, $param)
    {
        $report = [];

        $report = Self::GenerateSelisihAssetSoReportExcel($param);

        return $report;
    }

    public static function GenerateSelisihAssetSoReportExcel($param)
    {
        $path = 'reports/financeacc/selisih-asset-so/excel/';
        $filename = 'report-selisih-asset-so-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.xlsx';
        $report = [];
        if (Excel::store(new SelisihAssetSoReportExport($param->company_id, $param->plant, $param->periode, $param->user_id), $path . $filename . $random . $typefile, 'public')) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        };
        return $report;
    }
}
