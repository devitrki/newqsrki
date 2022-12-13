<?php

namespace App\Models\Financeacc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use OwenIt\Auditing\Contracts\Auditable;
use Maatwebsite\Excel\Facades\Excel;

use App\Library\Helper;

use App\Exports\Financeacc\OutstandingMutationAsset;
use App\Exports\Financeacc\LogMutationAsset;
use App\Models\Plant;

class AssetMutation extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    /*
        status mutation
        1 = request
        2 = cancel request
        3 = approve approver 1
        4 = unapprove approver 2
        5 = confirmation validator
        6 = reject by validator
        7 = approve approver 2
        8 = unapprove approver 2
        9 = approve approver 3
        10 = unapprove approver 3
        11 = confirmation send sender
        12 = reject sender
        13 = accept receiver
        14 = reject receiver
    */

    public static function getStatusMutationAssetByAssetNumber($AssetNumber, $assetSubNumber) {
        $status = 0;

        $qAssetMutation = DB::table('asset_mutations')
                            ->whereNotIn('status_mutation', [2, 4, 6, 8, 10, 12, 13, 14])
                            ->where('number', $AssetNumber)
                            ->where('number_sub', $assetSubNumber)
                            ->where('status', 0)
                            ->select('status_mutation');

        if( $qAssetMutation->count() > 0 ){
            $assetMutation = $qAssetMutation->first();
            $status = $assetMutation->status_mutation;
        }

        return $status;
    }

    // report
    public static function getDataOutstandingReport($companyId, $plantId, $userID)
    {
        $query = DB::table('asset_mutations')
                    ->leftJoin('plants as plant_from', 'plant_from.id', '=', 'asset_mutations.from_plant_id')
                    ->leftJoin('plants as plant_to', 'plant_to.id', '=', 'asset_mutations.to_plant_id')
                    ->where('asset_mutations.company_id', $companyId)
                    ->select('asset_mutations.*', 'plant_from.initital as from_plant_initital',
                            'plant_from.short_name as from_plant_name', 'plant_from.code as from_plant_code',
                            'plant_to.initital as to_plant_initital',
                            'plant_to.short_name as to_plant_name', 'plant_to.code as to_plant_code')
                    ->where('asset_mutations.status', 0);

        if( $plantId != '0' ){
            $plant = DB::table('plants')->where('id', $plantId)->first();
            $header = [
                'plant' => $plant->code . ' - ' . $plant->initital . ' ' . $plant->short_name,
            ];
            $query = $query->where( function($q) use ($plantId)  {
                        $q->where('from_plant_id', $plantId);
                        $q->orWhere('to_plant_id', $plantId);
                    });
        } else {
            $plants_auth = Plant::getPlantsIdByUserId($userID);
            $plants = explode(',', $plants_auth);

            if (!in_array('0', $plants)) {
                $query = $query->where( function($q) use ($plants)  {
                                $q->whereIn('from_plant_id', $plants);
                                $q->orWhereIn('to_plant_id', $plants);
                            });
            }

            $header = [
                'plant' => "All",
            ];
        }

        $items = $query->get();

        /*
            status mutation
            1 = request
            2 = cancel request
            3 = approve approver 1
            4 = unapprove approver 2
            5 = confirmation validator
            6 = reject by validator
            7 = approve approver 2
            8 = unapprove approver 2
            9 = approve approver 3
            10 = unapprove approver 3
            11 = confirmation send sender
            12 = reject sender
            13 = accept receiver
            14 = reject receiver
        */

        foreach ($items as $item) {
            $type = '';
            switch ($item->status_mutation) {
                case '1':
                    $type = 'Approval Approver 1';
                    break;
                case '3':
                    $type = 'Confirmation Validator';
                    break;
                case '5':
                    $type = 'Approval Approver 2';
                    break;
                case '7':
                    if( $item->level_request_third_id != 0 ){
                        $type = 'Approval Approver 3';
                    } else {
                        $type = 'Confirmation Sender';
                    }
                    break;
                case '9':
                    $type = 'Confirmation Sender';
                    break;
                case '11':
                    $type = 'Accepted Receiver';
                    break;
            }
            $item->type = $type;
        }

        return [
            'count' => $query->count(),
            'header' => $header,
            'items' => $items
        ];
    }

    public static function GenerateOutstandingReport($type, $param)
    {
        $report = [];

        $report = Self::GenerateOutstandingReportExcel($param);

        return $report;
    }

    public static function GenerateOutstandingReportExcel($param)
    {
        $path = 'reports/financeacc/outstanding-mutation-asset/excel/';
        $filename = 'report-outstanding-mutation-';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.xlsx';
        $report = [];
        if (Excel::store(new OutstandingMutationAsset($param->company_id, $param->plant, $param->user_id), $path . $filename . $random . $typefile, 'public')) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        };
        return $report;
    }

    // report mutation asset log
    public static function getDataLogReport($companyId, $plantId, $userID, $dateFrom, $dateUntil)
    {

        $header = [
            'date_from' => Helper::DateConvertFormat($dateFrom, 'Y/m/d', 'd.m.Y'),
            'date_until' => Helper::DateConvertFormat($dateUntil, 'Y/m/d', 'd.m.Y'),
        ];

        $query = DB::table('asset_mutations')
                    ->leftJoin('plants as plant_from', 'plant_from.id', '=', 'asset_mutations.from_plant_id')
                    ->leftJoin('plants as plant_to', 'plant_to.id', '=', 'asset_mutations.to_plant_id')
                    ->where('asset_mutations.company_id', $companyId)
                    ->select('asset_mutations.*', 'plant_from.initital as from_plant_initital',
                            'plant_from.short_name as from_plant_name', 'plant_from.code as from_plant_code',
                            'plant_to.initital as to_plant_initital',
                            'plant_to.short_name as to_plant_name', 'plant_to.code as to_plant_code')
                    ->where('asset_mutations.status', 1)
                    ->whereBetween('asset_mutations.date_request', [$dateFrom . " 00:00:00", $dateUntil . " 23:59:59"]);

        if( $plantId != '0' ){
            $plant = DB::table('plants')->where('id', $plantId)->first();
            $header['plant'] = $plant->code . ' - ' . $plant->initital . ' ' . $plant->short_name;
            $query = $query->where( function($q) use ($plantId)  {
                        $q->where('from_plant_id', $plantId);
                        $q->orWhere('to_plant_id', $plantId);
                    });
        } else {
            $plants_auth = Plant::getPlantsIdByUserId($userID);
            $plants = explode(',', $plants_auth);

            if (!in_array('0', $plants)) {
                $query = $query->where( function($q) use ($plants)  {
                                $q->whereIn('from_plant_id', $plants);
                                $q->orWhereIn('to_plant_id', $plants);
                            });
            }
            $header['plant'] = "All";
        }

        $items = $query->get();

        return [
            'count' => $query->count(),
            'header' => $header,
            'items' => $items
        ];
    }

    public static function GenerateLogReport($type, $param)
    {
        $report = [];

        $report = Self::GenerateLogReportExcel($param);

        return $report;
    }

    public static function GenerateLogReportExcel($param)
    {
        $path = 'reports/financeacc/log-mutation-asset/excel/';
        $filename = 'report-log-mutation-asset';
        $random = strtolower(Helper::generateRandomStr(8));
        $typefile = '.xlsx';
        $report = [];
        if (Excel::store(new LogMutationAsset($param->company_id, $param->plant, $param->user_id, $param->from_date, $param->until_date), $path . $filename . $random . $typefile, 'public')) {
            $report = [
                'path' => $path,
                'filename' => $filename . $random . $typefile
            ];
        };
        return $report;
    }
}
