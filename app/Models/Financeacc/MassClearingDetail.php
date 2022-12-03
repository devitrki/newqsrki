<?php

namespace App\Models\Financeacc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Str;

class MassClearingDetail extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getSalesDate($massClearingDetail){

        $salesDate = [];
        if( Str::contains($massClearingDetail->sales_date, ',') ){
            // multiple date
            $bankInSalesDates = explode(',', $massClearingDetail->sales_date);
            foreach ($bankInSalesDates as $date) {
                $salesDate[] = $massClearingDetail->sales_year . '-' . $massClearingDetail->sales_month . '-' . $date;
            }
        } else {
            // single date
            $salesDate[] = $massClearingDetail->sales_year . '-' . $massClearingDetail->sales_month . '-' . $massClearingDetail->sales_date;
        }

        return $salesDate;

    }

    public static function updateMassClearingDetail($column, $value, $id){
        $massClearingDetail = MassClearingDetail::find($id);
        $massClearingDetail->{$column} = $value;
        $massClearingDetail->save();
    }
}
