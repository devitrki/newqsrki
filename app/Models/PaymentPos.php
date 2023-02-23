<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PaymentPos extends Model
{
    use HasFactory;

    public static function getListPayments()
    {
        $list = DB::table('payment_pos')
                    ->select('method_payment_name', 'title')
                    ->orderBy('sort_order')
                    ->get();

        return $list;
    }

    public static function getRangeTenderByMethodName($methodName)
    {
        $rangeTender = "";
        $q = DB::table('payment_pos')
                ->select('range_tender')
                ->where('method_payment_name', $methodName);

        if($q->count() > 0){
            $data = $q->first();
            $rangeTender = $data->range_tender;
        }

        return $rangeTender;
    }

    public static function getQueryCondtionAloha($companyId, $column, $type = 'name')
    {
        $paymentPos = DB::table('payment_pos')
                            ->where('company_id', $companyId)
                            ->whereNotNull('range_tender')
                            ->select('method_payment_name', 'range_tender', 'sort_order')
                            ->get();

        $condition = 'CASE';

        foreach ($paymentPos as $pp) {
            $whenCondition = $column . " = " . $pp->range_tender;

            $rangeTenders = explode(',', $pp->range_tender);
            if (sizeof($rangeTenders) > 1) {
                $whenCondition = $column . " >= " . $rangeTenders[0] . " AND ";
                $whenCondition .= $column . " <= " . $rangeTenders[1];
            }

            $then = "'" . $pp->method_payment_name . "'";
            if ($type != 'name') {
                $then = $pp->sort_order;
            }

            $condition .= " WHEN " . $whenCondition . " THEN " . $then;
        }

        if ($type != 'name') {
            $condition .= " ELSE 7 ";
        } else {
            $condition .= " ELSE 'YY' ";
        }

        $condition .= "END";

        return $condition;
    }
}
