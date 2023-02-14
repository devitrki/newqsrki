<?php

namespace App\Models\Pos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

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
}
