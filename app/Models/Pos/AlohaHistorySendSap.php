<?php

namespace App\Models\Pos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

use App\Library\Helper;

use App\Models\Plant;

class AlohaHistorySendSap extends Model
{
    use HasFactory;

    public static function getStatusSendSap($dateWH, $SecondaryStoreID)
    {
        $statusSendSap = Lang::get("Not Yet");

        $date = date("Y-m-d", strtotime($dateWH));
        $plantId = Plant::getIdByCustomerCode($SecondaryStoreID);

        $query = DB::table('aloha_history_send_saps')
                    ->where('plant_id', $plantId)
                    ->where('date', $date)
                    ->select('send')
                    ->orderBy('created_at', 'desc')
                    ->limit(1);

        if ($query->count()) {
            $data = $query->first();
            if($data->send != 1){
                $statusSendSap = 'Not Send';
            } else {
                $statusSendSap = 'Send';
            }

        }

        return $statusSendSap;
    }

    public static function getDataReport($companyId, $storeID, $dateFrom, $dateUntil, $status)
    {
        $header = [
            'date_from' => Helper::DateConvertFormat($dateFrom, 'Y/m/d', 'd/m/Y'),
            'date_until' => Helper::DateConvertFormat($dateUntil, 'Y/m/d', 'd/m/Y'),
        ];

        $qHistories = DB::table('aloha_history_send_saps')
                        ->where('company_id', $companyId)
                        ->whereBetween('date', [$dateFrom, $dateUntil]);

        if ($storeID != '0') {
            $qHistories = $qHistories->where('plant_id', $storeID);
        }

        if ($status != '2') {
            $qHistories = $qHistories->where('send', $status);
        }

        $items = $qHistories->get();

        return [
            'count' => $qHistories->count(),
            'header' => $header,
            'items' => $items,
        ];
    }
}
