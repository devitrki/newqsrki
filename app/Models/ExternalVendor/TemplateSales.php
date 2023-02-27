<?php

namespace App\Models\ExternalVendor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class TemplateSales extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getTemplateSalesFieldNames() {
        $templateSalesFieldNames = [
            'receipt_number' => 'Receive Number',
            'receipt_date' => 'Receive Date',
            'receipt_time' => 'Receive Time',
            'sub_total_amount' => 'Sub Total',
            'discount_percent' => 'Discount Percent',
            'discount_amount' => 'Discount Amount',
            'tax_percent' => 'Tax Percent',
            'tax_amount' => 'Tax Amount',
            'service_charge_percent' => 'Service Charge Percent',
            'service_charge_amount' => 'Service Charge Amount',
            'grand_total_amount' => 'Grand Total',
            'is_void' => 'Is Void',
            'is_test' => 'Is Test',
        ];

        return $templateSalesFieldNames;
    }

    public static function getTemplateSalesFieldNumbers() {
        $templateSalesFieldNumbers = [
            'sub_total_amount',
            'discount_amount',
            'tax_amount',
            'service_charge_amount',
            'grand_total_amount',
        ];

        return $templateSalesFieldNumbers;
    }
}
