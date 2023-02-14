<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\OrderModePos;

class OrderModePosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();

        $orderModePos = new OrderModePos;
        $orderModePos->company_id = 2;
        $orderModePos->order_mode_id = 1;
        $orderModePos->order_mode_name = "DINE IN";
        $orderModePos->sap_name = "DI";
        $orderModePos->save();

        $orderModePos = new OrderModePos;
        $orderModePos->company_id = 2;
        $orderModePos->order_mode_id = 2;
        $orderModePos->order_mode_name = "TAKE AWAY";
        $orderModePos->sap_name = "TA";
        $orderModePos->save();

        $orderModePos = new OrderModePos;
        $orderModePos->company_id = 2;
        $orderModePos->order_mode_id = 3;
        $orderModePos->order_mode_name = "DELIVERY";
        $orderModePos->sap_name = "DE";
        $orderModePos->save();

        $orderModePos = new OrderModePos;
        $orderModePos->company_id = 2;
        $orderModePos->order_mode_id = 4;
        $orderModePos->order_mode_name = "Drive Thru";
        $orderModePos->sap_name = "DT";
        $orderModePos->save();

        $orderModePos = new OrderModePos;
        $orderModePos->company_id = 2;
        $orderModePos->order_mode_id = 5;
        $orderModePos->order_mode_name = "GO FOOD";
        $orderModePos->sap_name = "GF";
        $orderModePos->save();

        $orderModePos = new OrderModePos;
        $orderModePos->company_id = 2;
        $orderModePos->order_mode_id = 6;
        $orderModePos->order_mode_name = "GRAB FOOD";
        $orderModePos->sap_name = "GB";
        $orderModePos->save();

        $orderModePos = new OrderModePos;
        $orderModePos->company_id = 2;
        $orderModePos->order_mode_id = 7;
        $orderModePos->order_mode_name = "BIG ORDER";
        $orderModePos->sap_name = "BO";
        $orderModePos->save();

        $orderModePos = new OrderModePos;
        $orderModePos->company_id = 2;
        $orderModePos->order_mode_id = 8;
        $orderModePos->order_mode_name = "CATERING";
        $orderModePos->sap_name = "CT";
        $orderModePos->save();

        $orderModePos = new OrderModePos;
        $orderModePos->company_id = 2;
        $orderModePos->order_mode_id = 9;
        $orderModePos->order_mode_name = "SOFT SERV";
        $orderModePos->sap_name = "SS";
        $orderModePos->save();

        $orderModePos = new OrderModePos;
        $orderModePos->company_id = 2;
        $orderModePos->order_mode_id = 10;
        $orderModePos->order_mode_name = "SHOPEE FOOD";
        $orderModePos->sap_name = "SF";
        $orderModePos->save();

        $orderModePos = new OrderModePos;
        $orderModePos->company_id = 2;
        $orderModePos->order_mode_id = 11;
        $orderModePos->order_mode_name = "FOOD PANDA";
        $orderModePos->sap_name = "FP";
        $orderModePos->save();

        $orderModePos = new OrderModePos;
        $orderModePos->company_id = 2;
        $orderModePos->order_mode_id = 51;
        $orderModePos->order_mode_name = "RB-DINE IN";
        $orderModePos->sap_name = "BI";
        $orderModePos->save();

        $orderModePos = new OrderModePos;
        $orderModePos->company_id = 2;
        $orderModePos->order_mode_id = 52;
        $orderModePos->order_mode_name = "RB-TAKE AWAY";
        $orderModePos->sap_name = "BT";
        $orderModePos->save();

        $orderModePos = new OrderModePos;
        $orderModePos->company_id = 2;
        $orderModePos->order_mode_id = 53;
        $orderModePos->order_mode_name = "RB-GO FOOD";
        $orderModePos->sap_name = "BF";
        $orderModePos->save();

        $orderModePos = new OrderModePos;
        $orderModePos->company_id = 2;
        $orderModePos->order_mode_id = 54;
        $orderModePos->order_mode_name = "RB-GRAB FOOD";
        $orderModePos->sap_name = "BB";
        $orderModePos->save();

        $orderModePos = new OrderModePos;
        $orderModePos->company_id = 2;
        $orderModePos->order_mode_id = 71;
        $orderModePos->order_mode_name = "KIOSK - DI";
        $orderModePos->sap_name = "KI";
        $orderModePos->save();

        $orderModePos = new OrderModePos;
        $orderModePos->company_id = 2;
        $orderModePos->order_mode_id = 72;
        $orderModePos->order_mode_name = "KIOSK - TA";
        $orderModePos->sap_name = "KT";
        $orderModePos->save();

        DB::commit();
    }
}
