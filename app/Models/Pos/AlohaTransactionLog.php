<?php

namespace App\Models\Pos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Library\Helper;
use App\Models\Plant;

class AlohaTransactionLog extends Model
{
    use HasFactory;

    public static function getDataReport($companyId, $storeID, $dateFrom, $dateUntil, $status)
    {
        $header = [
            'date_from' => Helper::DateConvertFormat($dateFrom, 'Y/m/d', 'd/m/Y'),
            'date_until' => Helper::DateConvertFormat($dateUntil, 'Y/m/d', 'd/m/Y'),
        ];

        $logs = [];

        $qTransactionLogs = DB::table('aloha_transaction_logs')
                            ->where('company_id', $companyId)
                            ->whereBetween('closing_date', [$dateFrom, $dateUntil]);

        if($storeID != '0'){
            $qTransactionLogs = $qTransactionLogs->where('plant_id', $storeID);
        }

        if($status != 'A'){
            $qTransactionLogs = $qTransactionLogs->where('status', $status);
        }

        $transactionLogs = $qTransactionLogs->get();

        foreach ($transactionLogs as $transactionLog) {
            $plant = Plant::getCodeById($transactionLog->plant_id) . ' - ' . Plant::getShortNameById($transactionLog->plant_id);
            $closingDate = Helper::DateConvertFormat($transactionLog->closing_date, 'Y-m-d', 'd/m/Y');

            $iconStatus = 'bx-x';
            $iconColor = 'red';
            if($transactionLog->status != 'E'){
                $iconStatus = 'bx-check';
                $iconColor = 'green';
            }

            $logs[$plant][$closingDate][] = [
                'status' => $transactionLog->status,
                'icon_status' => $iconStatus,
                'icon_color' => $iconColor,
                'message' => $transactionLog->message,
                'date_closing' => $transactionLog->closing_date,
                'created_at' => $transactionLog->created_at,
            ];
        }

        return [
            'count' => $qTransactionLogs->count(),
            'header' => $header,
            'items' => $logs,
        ];
    }

    public static function getDocumentNumberSalesSap($plantId, $salesDate){

        $query = DB::table('aloha_transaction_logs')
                    ->select(DB::raw('SUBSTRING(message, 6, 9) as document_number'))
                    ->where('plant_id', $plantId)
                    ->where('closing_date', $salesDate)
                    ->where('type', 1)
                    ->where('status', 'S')
                    ->whereRaw('LEFT(message, 4) = ?', ['FI S'])
                    ->orderByDesc('id');

        $documentNumber = '';
        if( $query->count() > 0 ){
            $data = $query->first();
            $documentNumber = $data->document_number;
        }
        return $documentNumber;
    }
}
