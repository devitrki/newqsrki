<?php

namespace App\Models\Logbook;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LbProductProdPlan extends Model
{
    use HasFactory;

    public static function getFirstProduct($companyId)
    {
        $product = DB::table('lb_product_prod_plans')
                    ->where('company_id', $companyId)
                    ->select('product')
                    ->orderBy('product')
                    ->first();

        return $product->product;
    }
}
