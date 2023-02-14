<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderModePos extends Model
{
    use HasFactory;

    public static function getQueryCondtionAloha($companyId, $column, $type = 'name')
    {
        $orderModePos = DB::table('order_mode_pos')
                            ->where('company_id', $companyId)
                            ->select('order_mode_id', 'order_mode_name', 'sap_name')
                            ->get();

        $condition = 'CASE';

        foreach ($orderModePos as $om) {
            $orderModeCase = $om->order_mode_name;
            if ($type != 'name') {
                $orderModeCase = $om->order_mode_id;
            }

            $condition .= " WHEN " . $column . " = '" . $orderModeCase . "' THEN '" . $om->sap_name . "' ";
        }

        $condition .= 'END';

        return $condition;
    }
}
