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
}
