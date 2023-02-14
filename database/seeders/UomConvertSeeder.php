<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\UomConvert;

class UomConvertSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();

        $uomConvert = new UomConvert;
        $uomConvert->company_id = 2;
        $uomConvert->base_uom = "PAC";
        $uomConvert->send_sap_uom = "PAK";
        $uomConvert->save();

        $uomConvert = new UomConvert;
        $uomConvert->company_id = 2;
        $uomConvert->base_uom = "CAR";
        $uomConvert->send_sap_uom = "KAR";
        $uomConvert->save();

        DB::commit();
    }
}
