<?php

namespace App\Jobs\Logbook;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Carbon\Carbon;

use App\Models\Company;
use App\Models\Logbook\LbAppReview;
use App\Models\Logbook\LbDlyInvKitchen;
use App\Models\Logbook\LbDlyInvCashier;
use App\Models\Logbook\LbDlyInvWarehouse;
use App\Models\Logbook\LbDlyClean;
use App\Models\Logbook\LbBriefing;
use App\Models\Logbook\LbDutyRoster;
use App\Models\Logbook\LbDlyDuties;
use App\Models\Logbook\LbDlyDutiesDet;
use App\Models\Logbook\LbCleanDuties;
use App\Models\Logbook\LbCleanDutiesDly;
use App\Models\Logbook\LbCleanDutiesWly;
use App\Models\Logbook\LbToilet;
use App\Models\Logbook\LbTemperature;
use App\Models\Logbook\LbMonSls;
use App\Models\Logbook\LbMonSlsCas;
use App\Models\Logbook\LbMonSlsCasDet;
use App\Models\Logbook\LbProdPlan;
use App\Models\Logbook\LbProdTime;
use App\Models\Logbook\LbProdTimeDetail;
use App\Models\Logbook\LbProdTempVerify;
use App\Models\Logbook\LbProdQuality;
use App\Models\Logbook\LbProdUsedOil;

class GenerateTaskLogbook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $companyId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($companyId)
    {
        $this->companyId = $companyId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $companyTimezone = Company::getConfigByKey($this->companyId, 'TIMEZONE');
        $date = Carbon::now($companyTimezone)->format('Y-m-d');
        $dateYesterday = Carbon::now($companyTimezone)->subDay()->format('Y/m/d');

        $stores = DB::table('plants')
                    ->where('company_id', $this->companyId)
                    ->where('type', 1)
                    ->where('status', 1)
                    ->select('id')
                    ->get();

        DB::beginTransaction();
        $error = '';
        $run = false;

        LbAppReview::disableAuditing();
        LbDlyInvKitchen::disableAuditing();
        LbDlyInvCashier::disableAuditing();
        LbDlyInvWarehouse::disableAuditing();
        LbDlyClean::disableAuditing();
        LbBriefing::disableAuditing();
        LbDutyRoster::disableAuditing();
        LbDlyDuties::disableAuditing();
        LbDlyDutiesDet::disableAuditing();
        LbCleanDuties::disableAuditing();
        LbCleanDutiesDly::disableAuditing();
        LbCleanDutiesWly::disableAuditing();
        LbToilet::disableAuditing();
        LbTemperature::disableAuditing();
        LbMonSls::disableAuditing();
        LbMonSlsCas::disableAuditing();
        LbMonSlsCasDet::disableAuditing();
        LbProdPlan::disableAuditing();
        LbProdTime::disableAuditing();
        LbProdTimeDetail::disableAuditing();
        LbProdTempVerify::disableAuditing();
        LbProdQuality::disableAuditing();
        LbProdUsedOil::disableAuditing();

        foreach ($stores as $store) {
            $lbAppReviewId = 0;

            // check app review have already created ?
            $checkAppReview = DB::table('lb_app_reviews')
                                ->where('company_id', $this->companyId)
                                ->where('plant_id', $store->id)
                                ->where('date', $date);

            if($checkAppReview->count() > 0){
                // already created
                $lbAppReview = $checkAppReview->select('id')->first();
                $lbAppReviewId = $lbAppReview->id;
            } else {
                // not yet created
                $lbAppReview = new LbAppReview;
                $lbAppReview->company_id = $this->companyId;
                $lbAppReview->plant_id = $store->id;
                $lbAppReview->date = $date;
                if( $lbAppReview->save() ){
                    $lbAppReviewId = $lbAppReview->id;
                }
            }

            // check app review yesterday have already created ? for daily inventory
            $lbAppReviewIdYesterday = 0;
            $checkAppReviewYesterday = DB::table('lb_app_reviews')
                                        ->where('company_id', $this->companyId)
                                        ->where('plant_id', $store->id)
                                        ->where('date', $dateYesterday);

            if($checkAppReviewYesterday->count() > 0){
                // already created
                $lbAppReviewYesterday = $checkAppReviewYesterday->select('id')->first();
                $lbAppReviewIdYesterday = $lbAppReviewYesterday->id;
            }

            if( $lbAppReviewId > 0 ){

                try {

                    $run = ($this->generateDailyInventoryKitchen($lbAppReview->id, $lbAppReviewIdYesterday) && !$run) ? true : false ;
                    $run = ($this->generateDailyInventoryCashier($lbAppReview->id, $lbAppReviewIdYesterday) && !$run) ? true : false ;
                    $run = ($this->generateDailyInventoryWarehouse($lbAppReview->id, $lbAppReviewIdYesterday) && !$run) ? true : false ;
                    $run = ($this->generateDailyCleaning($lbAppReview->id) && !$run) ? true : false ;
                    $run = ($this->generateBriefing($lbAppReview->id) && !$run) ? true : false ;
                    $run = ($this->generateDailyDuties($lbAppReview->id) && !$run) ? true : false ;
                    $run = ($this->generateCleaningDuties($lbAppReview->id) && !$run) ? true : false ;
                    $run = ($this->generateToiletChecklist($lbAppReview->id) && !$run) ? true : false ;
                    $run = ($this->generateTemperature($lbAppReview->id) && !$run) ? true : false ;
                    $run = ($this->generateMoneySales($lbAppReview->id) && !$run) ? true : false ;
                    $run = ($this->generateProductionPlanning($lbAppReview->id) && !$run) ? true : false ;

                } catch (\Throwable $th) {
                    $error = 'Generate logbook application error : ' . $th->getMessage();
                }

            } else {
                $error = 'Generate logbook application error : failed to get app review id';
                break;
            }

        }

        LbAppReview::enableAuditing();
        LbDlyInvKitchen::enableAuditing();
        LbDlyInvCashier::enableAuditing();
        LbDlyInvWarehouse::enableAuditing();
        LbDlyClean::enableAuditing();
        LbBriefing::enableAuditing();
        LbDutyRoster::enableAuditing();
        LbDlyDuties::enableAuditing();
        LbDlyDutiesDet::enableAuditing();
        LbCleanDuties::enableAuditing();
        LbCleanDutiesDly::enableAuditing();
        LbCleanDutiesWly::enableAuditing();
        LbToilet::enableAuditing();
        LbTemperature::enableAuditing();
        LbMonSls::enableAuditing();
        LbMonSlsCas::enableAuditing();
        LbMonSlsCasDet::enableAuditing();
        LbProdPlan::enableAuditing();
        LbProdTime::enableAuditing();
        LbProdTimeDetail::enableAuditing();
        LbProdTempVerify::enableAuditing();
        LbProdQuality::enableAuditing();
        LbProdUsedOil::enableAuditing();

        if($error == ''){
            DB::commit();
        } else {
            DB::rollBack();
            Log::alert($error);
        }

    }

    public function generateDailyInventoryKitchen($lbAppReviewId, $lbAppReviewIdYesterday){

        $countCheck  = DB::table('lb_dly_inv_kitchens')
                        ->where('lb_app_review_id', $lbAppReviewId)
                        ->count();

        $run = false;

        if($countCheck <= 0){
            $run = true;

            $invKitchens = DB::table('lb_inv_kitchens')
                            ->leftJoin('material_logbooks', 'material_logbooks.id', 'lb_inv_kitchens.material_logbook_id')
                            ->where('lb_inv_kitchens.status', 1)
                            ->select('material_logbooks.name', 'material_logbooks.uom', 'lb_inv_kitchens.frekuensi')
                            ->get();

            foreach ($invKitchens as $invKitchen) {

                $checkStockYes = DB::table('lb_dly_inv_kitchens')
                                    ->where('lb_app_review_id', $lbAppReviewIdYesterday)
                                    ->where('product_name', $invKitchen->name)
                                    ->where('uom', $invKitchen->uom)
                                    ->where('frekuensi', $invKitchen->frekuensi)
                                    ->select('stock_opening', 'stock_closing');

                $stock_closing = 0;

                if($checkStockYes->count() > 0){
                    $stockYes = $checkStockYes->first();
                    $stock_closing = $stockYes->stock_closing;
                }

                $lbDlyInvKitchen = new LbDlyInvKitchen;
                $lbDlyInvKitchen->lb_app_review_id = $lbAppReviewId;
                $lbDlyInvKitchen->product_name = $invKitchen->name;
                $lbDlyInvKitchen->uom = $invKitchen->uom;
                $lbDlyInvKitchen->frekuensi = $invKitchen->frekuensi;
                $lbDlyInvKitchen->last_update = '-';
                $lbDlyInvKitchen->stock_opening = $stock_closing;
                $lbDlyInvKitchen->save();
            }
        }

        return $run;
    }

    public function generateDailyInventoryCashier($lbAppReviewId, $lbAppReviewIdYesterday){

        $countCheck  = DB::table('lb_dly_inv_cashiers')
                    ->where('lb_app_review_id', $lbAppReviewId)
                    ->count();

        $run = false;

        if($countCheck <= 0){
            $run = true;

            $invCashiers = DB::table('lb_inv_cashiers')
                            ->leftJoin('material_logbooks', 'material_logbooks.id', 'lb_inv_cashiers.material_logbook_id')
                            ->where('lb_inv_cashiers.status', 1)
                            ->select('material_logbooks.name', 'material_logbooks.uom', 'lb_inv_cashiers.frekuensi')
                            ->get();

            foreach ($invCashiers as $invCashier) {

                $checkStockYes = DB::table('lb_dly_inv_cashiers')
                                    ->where('lb_app_review_id', $lbAppReviewIdYesterday)
                                    ->where('product_name', $invCashier->name)
                                    ->where('uom', $invCashier->uom)
                                    ->where('frekuensi', $invCashier->frekuensi)
                                    ->select('stock_opening', 'stock_closing');

                $stock_closing = 0;

                if($checkStockYes->count() > 0){
                    $stockYes = $checkStockYes->first();
                    $stock_closing = $stockYes->stock_closing;
                }

                $lbDlyInvCashier = new LbDlyInvCashier;
                $lbDlyInvCashier->lb_app_review_id = $lbAppReviewId;
                $lbDlyInvCashier->product_name = $invCashier->name;
                $lbDlyInvCashier->uom = $invCashier->uom;
                $lbDlyInvCashier->frekuensi = $invCashier->frekuensi;
                $lbDlyInvCashier->last_update = '-';
                $lbDlyInvCashier->stock_opening = $stock_closing;
                $lbDlyInvCashier->save();
            }
        }

        return $run;
    }

    public function generateDailyInventoryWarehouse($lbAppReviewId, $lbAppReviewIdYesterday){

        $countCheck  = DB::table('lb_dly_inv_warehouses')
                    ->where('lb_app_review_id', $lbAppReviewId)
                    ->count();

        $run = false;

        if($countCheck <= 0){
            $run = true;
            $invWarehouses = DB::table('lb_inv_warehouses')
                            ->leftJoin('material_logbooks', 'material_logbooks.id', 'lb_inv_warehouses.material_logbook_id')
                            ->where('lb_inv_warehouses.status', 1)
                            ->select('material_logbooks.name', 'material_logbooks.uom', 'lb_inv_warehouses.frekuensi')
                            ->get();

            foreach ($invWarehouses as $invWarehouse) {

                $checkStockYes = DB::table('lb_dly_inv_warehouses')
                                    ->where('lb_app_review_id', $lbAppReviewIdYesterday)
                                    ->where('product_name', $invWarehouse->name)
                                    ->where('uom', $invWarehouse->uom)
                                    ->where('frekuensi', $invWarehouse->frekuensi)
                                    ->select('stock_opening', 'stock_closing');

                $stock_closing = 0;

                if($checkStockYes->count() > 0){
                    $stockYes = $checkStockYes->first();
                    $stock_closing = $stockYes->stock_closing;
                }

                $lbDlyInvWarehouse = new LbDlyInvWarehouse;
                $lbDlyInvWarehouse->lb_app_review_id = $lbAppReviewId;
                $lbDlyInvWarehouse->product_name = $invWarehouse->name;
                $lbDlyInvWarehouse->uom = $invWarehouse->uom;
                $lbDlyInvWarehouse->frekuensi = $invWarehouse->frekuensi;
                $lbDlyInvWarehouse->last_update = '-';
                $lbDlyInvWarehouse->stock_opening = $stock_closing;
                $lbDlyInvWarehouse->save();
            }
        }

        return $run;
    }

    public function generateDailyCleaning($lbAppReviewId){

        $countCheck  = DB::table('lb_dly_cleans')
                    ->where('lb_app_review_id', $lbAppReviewId)
                    ->count();

        $run = false;

        if($countCheck <= 0){
            $run = true;
            $shifts = [1,2,3];
            foreach ($shifts as $shift) {
                $taskCleans = DB::table('lb_task_cleans')
                                ->where('status', 1)
                                ->select('task', 'section', 'frekuensi')
                                ->get();

                foreach ($taskCleans as $taskClean) {
                    $lbDlyClean = new LbDlyClean;
                    $lbDlyClean->lb_app_review_id = $lbAppReviewId;
                    $lbDlyClean->task = $taskClean->task;
                    $lbDlyClean->section = $taskClean->section;
                    $lbDlyClean->frekuensi = $taskClean->frekuensi;
                    $lbDlyClean->shift = $shift;
                    $lbDlyClean->pic = '';
                    $lbDlyClean->save();
                }
            }
        }

        return $run;
    }

    public function generateBriefing($lbAppReviewId){

        $countCheck  = DB::table('lb_briefings')
                    ->where('lb_app_review_id', $lbAppReviewId)
                    ->count();

        $run = false;

        if($countCheck <= 0){
            $run = true;
            $shifts = ['Morning','Afternoon','Midnite'];
            foreach ($shifts as $shift) {
                $lbBriefing = new LbBriefing;
                $lbBriefing->lb_app_review_id = $lbAppReviewId;
                $lbBriefing->shift = $shift;
                if($lbBriefing->save()){
                    for ($i=0; $i < 4; $i++) {
                        $lbDutyRoster = new LbDutyRoster;
                        $lbDutyRoster->lb_briefing_id = $lbBriefing->id;
                        $lbDutyRoster->save();
                    }
                }
            }
        }

        return $run;
    }

    public function generateDailyDuties($lbAppReviewId){

        $countCheck  = DB::table('lb_dly_duties')
                    ->where('lb_app_review_id', $lbAppReviewId)
                    ->count();

        $run = false;

        if($countCheck <= 0){
            $run = true;
            $shifts = ['Cashier','Lobby','Kitchen'];
            foreach ($shifts as $shift) {

                if( $shift == 'Cashier' ){
                    $tasks = DB::table('lb_dut_cashiers')
                            ->where('status', 1)
                            ->select('task')
                            ->get();
                } else if( $shift == 'Lobby' ){
                    $tasks = DB::table('lb_dut_lobbies')
                            ->where('status', 1)
                            ->select('task')
                            ->get();
                } else {
                    $tasks = DB::table('lb_dut_kitchens')
                            ->where('status', 1)
                            ->select('task')
                            ->get();
                }

                foreach ($tasks as $task) {
                    $lbDlyDuties = new LbDlyDuties;
                    $lbDlyDuties->lb_app_review_id = $lbAppReviewId;
                    $lbDlyDuties->section = $shift;
                    $lbDlyDuties->task = $task->task;
                    if($lbDlyDuties->save()){
                        $lbDlyDutiesDet = new LbDlyDutiesDet;
                        $lbDlyDutiesDet->lb_dly_duties_id = $lbDlyDuties->id;
                        $lbDlyDutiesDet->save();
                    }
                }
            }
        }

        return $run;
    }

    public function generateCleaningDuties($lbAppReviewId){

        $countCheck  = DB::table('lb_clean_duties')
                    ->where('lb_app_review_id', $lbAppReviewId)
                    ->count();

        $run = false;

        if($countCheck <= 0){
            $run = true;
            $shifts = ['Cashier','Lobby','Kitchen'];
            foreach ($shifts as $shift) {

                $lbCleanDuties = new LbCleanDuties;
                $lbCleanDuties->lb_app_review_id = $lbAppReviewId;
                $lbCleanDuties->section = $shift;
                if($lbCleanDuties->save()){
                    if( $shift == 'Cashier' ){
                        $tasks = DB::table('lb_clean_cashiers')
                                ->where('status', 1)
                                ->select('task', 'frekuensi', 'day')
                                ->get();
                    } else if( $shift == 'Lobby' ){
                        $tasks = DB::table('lb_clean_lobbies')
                                ->where('status', 1)
                                ->select('task', 'frekuensi', 'day')
                                ->get();
                    } else {
                        $tasks = DB::table('lb_clean_kitchens')
                                ->where('status', 1)
                                ->select('task', 'frekuensi', 'day')
                                ->get();
                    }

                    foreach ($tasks as $task) {
                        if($task->frekuensi == 'Daily'){
                            $lbCleanDutiesDly = new LbCleanDutiesDly;
                            $lbCleanDutiesDly->lb_clean_duties_id = $lbCleanDuties->id;
                            $lbCleanDutiesDly->task = $task->task;
                            $lbCleanDutiesDly->save();
                        } else {
                            $lbCleanDutiesWly = new LbCleanDutiesWly;
                            $lbCleanDutiesWly->lb_clean_duties_id = $lbCleanDuties->id;
                            $lbCleanDutiesWly->task = $task->task;
                            $lbCleanDutiesWly->day = $task->day;
                            $lbCleanDutiesWly->save();
                        }
                    }
                }
            }
        }

        return $run;
    }

    public function generateToiletChecklist($lbAppReviewId){

        $countCheck  = DB::table('lb_toilets')
                    ->where('lb_app_review_id', $lbAppReviewId)
                    ->count();

        $run = false;

        if($countCheck <= 0){
            $run = true;
            $shifts = [1,2,3];
            foreach ($shifts as $shift) {
                $taskToilets = DB::table('lb_task_toilets')
                                ->where('status', 1)
                                ->select('task')
                                ->get();

                foreach ($taskToilets as $taskToilet) {
                    $lbToilet = new LbToilet;
                    $lbToilet->lb_app_review_id = $lbAppReviewId;
                    $lbToilet->task = $taskToilet->task;
                    $lbToilet->shift = $shift;
                    $lbToilet->save();
                }
            }
        }

        return $run;
    }

    public function generateTemperature($lbAppReviewId){

        $countCheck  = DB::table('lb_temperatures')
                    ->where('lb_app_review_id', $lbAppReviewId)
                    ->count();

        $run = false;

        if($countCheck <= 0){
            $run = true;
            $storageTemps = DB::table('lb_storage_temps')
                            ->where('status', 1)
                            ->orderBy('name')
                            ->get();

            foreach ($storageTemps as $storage) {
                $lbTemperature = new LbTemperature;
                $lbTemperature->lb_app_review_id = $lbAppReviewId;
                $lbTemperature->name = $storage->name;
                $lbTemperature->top_value = $storage->top_value;
                $lbTemperature->bottom_value = $storage->bottom_value;
                $lbTemperature->top_value_center = $storage->top_value_center;
                $lbTemperature->bottom_value_center = $storage->bottom_value_center;
                $lbTemperature->interval = $storage->interval;
                $lbTemperature->uom = $storage->uom;
                $lbTemperature->note = '';
                $lbTemperature->save();
            }
        }

        return $run;
    }

    public function generateMoneySales($lbAppReviewId){

        $countCheck  = DB::table('lb_mon_sls')
                    ->where('lb_app_review_id', $lbAppReviewId)
                    ->count();

        $run = false;

        if($countCheck <= 0){
            $run = true;
            $lbMonSls = new LbMonSls;
            $lbMonSls->lb_app_review_id = $lbAppReviewId;
            if($lbMonSls->save()){
                $shifts = ['Opening','Closing','Midnite'];
                foreach ($shifts as $shift) {
                    $lbMonSlsCas = new LbMonSlsCas;
                    $lbMonSlsCas->lb_mon_sls_id = $lbMonSls->id;
                    $lbMonSlsCas->shift = $shift;
                    if($lbMonSlsCas->save()){

                        for ($i=1; $i <= 4; $i++) {
                            $lbMonSlsCasDet = new LbMonSlsCasDet;
                            $lbMonSlsCasDet->lb_mon_sls_cas_id = $lbMonSlsCas->id;
                            $lbMonSlsCasDet->cashier_no = 'Cashier ' . $i;
                            $lbMonSlsCasDet->save();
                        }

                    }
                }
            }
        }

        return $run;
    }

    public function generateProductionPlanning($lbAppReviewId){
        $countCheck  = DB::table('lb_prod_plans')
                        ->where('lb_app_review_id', $lbAppReviewId)
                        ->count();

        $run = false;

        if($countCheck <= 0){
            $run = true;

            $products = DB::table('lb_product_prod_plans')->get();
            $times = [6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 0, 1, 2, 3, 4, 5];
            $fryers = ['A', 'B', 'C', 'D'];

            foreach ($products as $product) {

                // create prod plan
                $lbProdPlan = new LbProdPlan;
                $lbProdPlan->lb_app_review_id = $lbAppReviewId;
                $lbProdPlan->product = $product->product;
                if( $lbProdPlan->save() ){

                    // create prod time
                    foreach ($times as $time) {
                        $lbProdTime = new LbProdTime;
                        $lbProdTime->lb_prod_plan_id = $lbProdPlan->id;
                        $lbProdTime->time = $time . ':00';
                        if( $lbProdTime->save() ){

                            for ($i=0; $i < 4; $i++) {
                                $lbProdTimeDetail = new LbProdTimeDetail;
                                $lbProdTimeDetail->lb_prod_time_id = $lbProdTime->id;
                                $lbProdTimeDetail->save();
                            }

                        }
                    }

                    foreach ($fryers as $fryer) {
                        // create prod temp verify
                        $lbProdTempVerify = new LbProdTempVerify;
                        $lbProdTempVerify->lb_prod_plan_id = $lbProdPlan->id;
                        $lbProdTempVerify->fryer = $fryer;
                        $lbProdTempVerify->save();

                        // create prod quality
                        $lbProdQuality = new LbProdQuality;
                        $lbProdQuality->lb_prod_plan_id = $lbProdPlan->id;
                        $lbProdQuality->fryer = $fryer;
                        $lbProdQuality->save();
                    }

                    // create prod used oil
                    $lbProdUsedOil = new LbProdUsedOil;
                    $lbProdUsedOil->lb_prod_plan_id = $lbProdPlan->id;
                    $lbProdUsedOil->save();

                }

            }

        }

        return $run;

    }
}
