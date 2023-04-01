<?php

namespace App\Http\Controllers\Logbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Plant;
use App\Models\Logbook\LbAppReview;
use App\Models\Logbook\LbDlyInvKitchen;
use App\Models\Logbook\LbDlyInvCashier;
use App\Models\Logbook\LbDlyInvWarehouse;
use App\Models\Logbook\LbDlyWasted;
use App\Models\Logbook\LbStockCard;
use App\Models\Logbook\LbRecMaterial;
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
use App\Models\Logbook\LbWaterMeter;
use App\Models\Logbook\LbElectricMeter;
use App\Models\Logbook\LbGasMeter;
use App\Models\Logbook\LbEnvPump;
use App\Models\Logbook\LbEnvWater;
use App\Models\Logbook\LbEnvSolid;
use App\Models\Logbook\LbOrganoleptik;
use App\Models\Logbook\LbProdPlan;
use App\Models\Logbook\LbProdTime;
use App\Models\Logbook\LbProdTimeDetail;
use App\Models\Logbook\LbProdTempVerify;
use App\Models\Logbook\LbProdQuality;
use App\Models\Logbook\LbProdUsedOil;
use App\Models\Logbook\LbProductProdPlan;

class ApplicationReviewController extends Controller
{
    public function index(Request $request){
        $userAuth = $request->get('userAuth');

        $first_plant_id = Plant::getFirstPlantIdSelect($userAuth->company_id_selected, 'outlet', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'menu_id' => $request->query('menuid')
        ];

        return view('logbook.application-review', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('lb_app_reviews')
                ->leftJoin('plants', 'plants.id', '=', 'lb_app_reviews.plant_id')
                ->where('lb_app_reviews.company_id', $userAuth->company_id_selected)
                ->select(['lb_app_reviews.id', 'lb_app_reviews.date', 'lb_app_reviews.mod_approval',
                          'lb_app_reviews.mod_pic', 'lb_app_reviews.plant_id',
                          DB::raw("CONCAT(plants.initital ,' ', plants.short_name) AS outlet"),
                        ]);

        if($request->has('plant-id') && $request->query('plant-id') != '0'){
            if($request->query('plant-id') != ''){
                $query = $query->where('lb_app_reviews.plant_id', $request->query('plant-id'));
            }
        }else {
            $plants_auth = Plant::getPlantsIdByUserId(Auth::id());
            $plants = explode(',', $plants_auth);
            if(!in_array('0', $plants)){
                $query = $query->whereIn('lb_app_reviews.plant_id', $plants);
            }
        }

        if($request->has('from') && $request->has('until')){
            if($request->query('from') != '' && $request->query('until') != ''){
                $query = $query->whereBetween('lb_app_reviews.date', [$request->query('from'), $request->query('until')]);
            }
        }

        $query = $query->orderBy('lb_app_reviews.date', 'desc');

        return Datatables::of($query)
                        ->addIndexColumn()
                        ->addColumn('mod_approval_desc', function ($data) {
                            if ($data->mod_approval != 0) {
                                return '<i class="bx bxs-check-circle text-success"></i>';
                            } else {
                                return '<i class="bx bxs-x-circle text-danger"></i>';
                            }
                        })
                        ->addColumn('date_desc', function ($data) {
                            return date("d-m-Y", strtotime($data->date));
                        })
                        ->filterColumn('outlet', function($query, $keyword) {
                            $query->whereRaw("LOWER(plants.short_name) like ?", ["%" . strtolower($keyword) . "%"]);
                        })
                        ->rawColumns(['mod_approval_desc'])
                        ->make();
    }

    public function previewDtble(Request $request)
    {
        $userAuth = $request->get('userAuth');
        $companyId = $userAuth->company_id_selected;
        $documents = [];

        $documents[] = [
            'document' => "Daily Inventory Kitchen",
            'last_update' => ( LbDlyInvKitchen::getLastDataByLbAppReviewId($request->query('id'), 'updated_at') == '' ) ? '-' : Helper::DateConvertFormatTz(LbDlyInvKitchen::getLastDataByLbAppReviewId($request->query('id'), 'updated_at'), 'Y-m-d H:i:s', 'UTC', 'd-m-Y H:i:s', $companyId),
            // 'last_update_by' => (LbDlyInvKitchen::getLastDataByLbAppReviewId($request->query('id'), 'last_update') == '') ? '-' : LbDlyInvKitchen::getLastDataByLbAppReviewId($request->query('id'), 'last_update'),
            'url_preview' => 'logbook/daily-inventory/kitchen/' . $request->query('id') . '/preview'
        ];

        $documents[] = [
            'document' => "Daily Inventory Cashier",
            'last_update' => (LbDlyInvCashier::getLastDataByLbAppReviewId($request->query('id'), 'updated_at') == '') ? '-' : Helper::DateConvertFormatTz(LbDlyInvCashier::getLastDataByLbAppReviewId($request->query('id'), 'updated_at'), 'Y-m-d H:i:s', 'UTC', 'd-m-Y H:i:s', $companyId),
            // 'last_update_by' => (LbDlyInvCashier::getLastDataByLbAppReviewId($request->query('id'), 'last_update') == '') ? '-' : LbDlyInvCashier::getLastDataByLbAppReviewId($request->query('id'), 'last_update'),
            'url_preview' => 'logbook/daily-inventory/cashier/' . $request->query('id') . '/preview'
        ];

        $documents[] = [
            'document' => "Daily Inventory Warehouse",
            'last_update' => (LbDlyInvWarehouse::getLastDataByLbAppReviewId($request->query('id'), 'updated_at') == '') ? '-' : Helper::DateConvertFormatTz(LbDlyInvWarehouse::getLastDataByLbAppReviewId($request->query('id'), 'updated_at'), 'Y-m-d H:i:s', 'UTC', 'd-m-Y H:i:s', $companyId),
            // 'last_update_by' => (LbDlyInvWarehouse::getLastDataByLbAppReviewId($request->query('id'), 'last_update') == '') ? '-' : LbDlyInvWarehouse::getLastDataByLbAppReviewId($request->query('id'), 'last_update'),
            'url_preview' => 'logbook/daily-inventory/warehouse/' . $request->query('id') . '/preview'
        ];

        // $documents[] = [
        //     'document' => "Stock Card",
        //     'last_update' => (LbStockCard::getLastDataByLbAppReviewId($request->query('id'), 'updated_at') == '') ? '-' : Helper::DateConvertFormatTz(LbStockCard::getLastDataByLbAppReviewId($request->query('id'), 'updated_at'), 'Y-m-d H:i:s', 'UTC', 'd-m-Y H:i:s', $companyId),
            // 'last_update_by' => (LbStockCard::getLastDataByLbAppReviewId($request->query('id'), 'pic') == '') ? '-' : LbStockCard::getLastDataByLbAppReviewId($request->query('id'), 'pic'),
        //     'url_preview' => 'logbook/stock-card/' . $request->query('id') . '/preview'
        // ];

        $documents[] = [
            'document' => "Daily Wasted",
            'last_update' => (LbDlyWasted::getLastDataByLbAppReviewId($request->query('id'), 'updated_at') == '') ? '-' : Helper::DateConvertFormatTz(LbDlyWasted::getLastDataByLbAppReviewId($request->query('id'), 'updated_at'), 'Y-m-d H:i:s', 'UTC', 'd-m-Y H:i:s', $companyId),
            'last_update_by' => (LbDlyWasted::getLastDataByLbAppReviewId($request->query('id'), 'last_update') == '') ? '-' : LbDlyWasted::getLastDataByLbAppReviewId($request->query('id'), 'last_update'),
            'url_preview' => 'logbook/daily-wasted/' . $request->query('id') . '/preview'
        ];

        $documents[] = [
            'document' => "Reception Material / Product",
            'last_update' => (LbRecMaterial::getLastDataByLbAppReviewId($request->query('id'), 'updated_at') == '') ? '-' : Helper::DateConvertFormatTz(LbRecMaterial::getLastDataByLbAppReviewId($request->query('id'), 'updated_at'), 'Y-m-d H:i:s', 'UTC', 'd-m-Y H:i:s', $companyId),
            'last_update_by' => (LbRecMaterial::getLastDataByLbAppReviewId($request->query('id'), 'pic') == '') ? '-' : LbRecMaterial::getLastDataByLbAppReviewId($request->query('id'), 'pic'),
            'url_preview' => 'logbook/reception-material/' . $request->query('id') . '/preview'
        ];

        $documents[] = [
            'document' => "Cleaning & Sanitation (Shift 1)",
            'last_update' => (LbDlyClean::getLastDataByLbAppReviewId($request->query('id'), 'updated_at') == '') ? '-' : Helper::DateConvertFormatTz(LbDlyClean::getLastDataByLbAppReviewId($request->query('id'), 'updated_at'), 'Y-m-d H:i:s', 'UTC', 'd-m-Y H:i:s', $companyId),
            // 'last_update_by' => (LbDlyClean::getLastDataByLbAppReviewId($request->query('id'), 'pic') == '') ? '-' : LbDlyClean::getLastDataByLbAppReviewId($request->query('id'), 'pic'),
            'url_preview' => 'logbook/daily-cleaning/' . $request->query('id') . '/preview?shift=' . 1
        ];

        $documents[] = [
            'document' => "Cleaning & Sanitation (Shift 2)",
            'last_update' => (LbDlyClean::getLastDataByLbAppReviewId($request->query('id'), 'updated_at') == '') ? '-' : Helper::DateConvertFormatTz(LbDlyClean::getLastDataByLbAppReviewId($request->query('id'), 'updated_at'), 'Y-m-d H:i:s', 'UTC', 'd-m-Y H:i:s', $companyId),
            // 'last_update_by' => (LbDlyClean::getLastDataByLbAppReviewId($request->query('id'), 'pic') == '') ? '-' : LbDlyClean::getLastDataByLbAppReviewId($request->query('id'), 'pic'),
            'url_preview' => 'logbook/daily-cleaning/' . $request->query('id') . '/preview?shift=' . 2
        ];

        $documents[] = [
            'document' => "Cleaning & Sanitation (Shift 3)",
            'last_update' => (LbDlyClean::getLastDataByLbAppReviewId($request->query('id'), 'updated_at') == '') ? '-' : Helper::DateConvertFormatTz(LbDlyClean::getLastDataByLbAppReviewId($request->query('id'), 'updated_at'), 'Y-m-d H:i:s', 'UTC', 'd-m-Y H:i:s', $companyId),
            // 'last_update_by' => (LbDlyClean::getLastDataByLbAppReviewId($request->query('id'), 'pic') == '') ? '-' : LbDlyClean::getLastDataByLbAppReviewId($request->query('id'), 'pic'),
            'url_preview' => 'logbook/daily-cleaning/' . $request->query('id') . '/preview?shift=' . 3
        ];

        $documents[] = [
            'document' => "Daily Briefing & Duty Roster",
            'last_update' => (LbBriefing::getLastDataByLbAppReviewId($request->query('id'), 'updated_at') == '') ? '-' : Helper::DateConvertFormatTz(LbBriefing::getLastDataByLbAppReviewId($request->query('id'), 'updated_at'), 'Y-m-d H:i:s', 'UTC', 'd-m-Y H:i:s', $companyId),
            // 'last_update_by' => '-',
            'url_preview' => 'logbook/operational/duty-roster/' . $request->query('id') . '/preview'
        ];

        $documents[] = [
            'document' => "Daily Duties (Cashier Section)",
            'last_update' => (LbDlyDuties::getLastDataByLbAppReviewId($request->query('id'), 'updated_at', 'Cashier') == '') ? '-' : Helper::DateConvertFormatTz(LbDlyDuties::getLastDataByLbAppReviewId($request->query('id'), 'updated_at', 'Cashier'), 'Y-m-d H:i:s', 'UTC', 'd-m-Y H:i:s', $companyId),
            // 'last_update_by' => '-',
            'url_preview' => 'logbook/operational/daily-duties/' . $request->query('id') . '/preview?section=Cashier'
        ];

        $documents[] = [
            'document' => "Daily Duties (Lobby Section)",
            'last_update' => (LbDlyDuties::getLastDataByLbAppReviewId($request->query('id'), 'updated_at', 'Lobby') == '') ? '-' : Helper::DateConvertFormatTz(LbDlyDuties::getLastDataByLbAppReviewId($request->query('id'), 'updated_at', 'Lobby'), 'Y-m-d H:i:s', 'UTC', 'd-m-Y H:i:s', $companyId),
            // 'last_update_by' => '-',
            'url_preview' => 'logbook/operational/daily-duties/' . $request->query('id') . '/preview?section=Lobby'
        ];

        $documents[] = [
            'document' => "Daily Duties (Kitchen Section)",
            'last_update' => (LbDlyDuties::getLastDataByLbAppReviewId($request->query('id'), 'updated_at', 'Kitchen') == '') ? '-' : Helper::DateConvertFormatTz(LbDlyDuties::getLastDataByLbAppReviewId($request->query('id'), 'updated_at', 'Kitchen'), 'Y-m-d H:i:s', 'UTC', 'd-m-Y H:i:s', $companyId),
            // 'last_update_by' => '-',
            'url_preview' => 'logbook/operational/daily-duties/' . $request->query('id') . '/preview?section=Kitchen'
        ];

        $documents[] = [
            'document' => "Cleaning Duties (Cashier Section)",
            'last_update' => (LbCleanDuties::getLastDataByLbAppReviewId($request->query('id'), 'updated_at', 'Cashier') == '') ? '-' : Helper::DateConvertFormatTz(LbCleanDuties::getLastDataByLbAppReviewId($request->query('id'), 'updated_at', 'Cashier'), 'Y-m-d H:i:s', 'UTC', 'd-m-Y H:i:s', $companyId),
            // 'last_update_by' => '-',
            'url_preview' => 'logbook/operational/cleaning-duties/' . $request->query('id') . '/preview?section=Cashier'
        ];

        $documents[] = [
            'document' => "Cleaning Duties (Lobby Section)",
            'last_update' => (LbCleanDuties::getLastDataByLbAppReviewId($request->query('id'), 'updated_at', 'Lobby') == '') ? '-' : Helper::DateConvertFormatTz(LbCleanDuties::getLastDataByLbAppReviewId($request->query('id'), 'updated_at', 'Lobby'), 'Y-m-d H:i:s', 'UTC', 'd-m-Y H:i:s', $companyId),
            // 'last_update_by' => '-',
            'url_preview' => 'logbook/operational/cleaning-duties/' . $request->query('id') . '/preview?section=Lobby'
        ];

        $documents[] = [
            'document' => "Cleaning Duties (Kitchen Section)",
            'last_update' => (LbCleanDuties::getLastDataByLbAppReviewId($request->query('id'), 'updated_at', 'Kitchen') == '') ? '-' : Helper::DateConvertFormatTz(LbCleanDuties::getLastDataByLbAppReviewId($request->query('id'), 'updated_at', 'Kitchen'), 'Y-m-d H:i:s', 'UTC', 'd-m-Y H:i:s', $companyId),
            // 'last_update_by' => '-',
            'url_preview' => 'logbook/operational/cleaning-duties/' . $request->query('id') . '/preview?section=Kitchen'
        ];

        $documents[] = [
            'document' => "Water Meter Form",
            'last_update' => (LbWaterMeter::getLastDataByLbAppReviewId($request->query('id'), 'updated_at') == '') ? '-' : Helper::DateConvertFormatTz(LbWaterMeter::getLastDataByLbAppReviewId($request->query('id'), 'updated_at'), 'Y-m-d H:i:s', 'UTC', 'd-m-Y H:i:s', $companyId),
            // 'last_update_by' => (LbWaterMeter::getLastDataByLbAppReviewId($request->query('id'), 'pic') == '') ? '-' : LbWaterMeter::getLastDataByLbAppReviewId($request->query('id'), 'pic'),
            'url_preview' => 'logbook/operational/water-meter/' . $request->query('id') . '/preview'
        ];

        $documents[] = [
            'document' => "Electric Meter Form",
            'last_update' => (LbElectricMeter::getLastDataByLbAppReviewId($request->query('id'), 'updated_at') == '') ? '-' : Helper::DateConvertFormatTz(LbElectricMeter::getLastDataByLbAppReviewId($request->query('id'), 'updated_at'), 'Y-m-d H:i:s', 'UTC', 'd-m-Y H:i:s', $companyId),
            // 'last_update_by' => (LbElectricMeter::getLastDataByLbAppReviewId($request->query('id'), 'pic') == '') ? '-' : LbElectricMeter::getLastDataByLbAppReviewId($request->query('id'), 'pic'),
            'url_preview' => 'logbook/operational/electric-meter/' . $request->query('id') . '/preview'
        ];

        $documents[] = [
            'document' => "Gas Meter Form",
            'last_update' => (LbGasMeter::getLastDataByLbAppReviewId($request->query('id'), 'updated_at') == '') ? '-' : Helper::DateConvertFormatTz(LbElectricMeter::getLastDataByLbAppReviewId($request->query('id'), 'updated_at'), 'Y-m-d H:i:s', 'UTC', 'd-m-Y H:i:s', $companyId),
            // 'last_update_by' => (LbElectricMeter::getLastDataByLbAppReviewId($request->query('id'), 'pic') == '') ? '-' : LbElectricMeter::getLastDataByLbAppReviewId($request->query('id'), 'pic'),
            'url_preview' => 'logbook/operational/gas-meter/' . $request->query('id') . '/preview'
        ];

        $documents[] = [
            'document' => "Env Control (Pro Pump)",
            'last_update' => (LbEnvPump::getLastDataByLbAppReviewId($request->query('id'), 'updated_at') == '') ? '-' : Helper::DateConvertFormatTz(LbEnvPump::getLastDataByLbAppReviewId($request->query('id'), 'updated_at'), 'Y-m-d H:i:s', 'UTC', 'd-m-Y H:i:s', $companyId),
            // 'last_update_by' => (LbEnvPump::getLastDataByLbAppReviewId($request->query('id'), 'pic') == '') ? '-' : LbEnvPump::getLastDataByLbAppReviewId($request->query('id'), 'pic'),
            'url_preview' => 'logbook/operational/env-propump/' . $request->query('id') . '/preview'
        ];

        $documents[] = [
            'document' => "Env Control (Wastewater)",
            'last_update' => (LbEnvWater::getLastDataByLbAppReviewId($request->query('id'), 'updated_at') == '') ? '-' : Helper::DateConvertFormatTz(LbEnvWater::getLastDataByLbAppReviewId($request->query('id'), 'updated_at'), 'Y-m-d H:i:s', 'UTC', 'd-m-Y H:i:s', $companyId),
            // 'last_update_by' => (LbEnvWater::getLastDataByLbAppReviewId($request->query('id'), 'pic') == '') ? '-' : LbEnvWater::getLastDataByLbAppReviewId($request->query('id'), 'pic'),
            'url_preview' => 'logbook/operational/env-wastewater/' . $request->query('id') . '/preview'
        ];

        $documents[] = [
            'document' => "Env Control (Solid Waste)",
            'last_update' => (LbEnvSolid::getLastDataByLbAppReviewId($request->query('id'), 'updated_at') == '') ? '-' : Helper::DateConvertFormatTz(LbEnvSolid::getLastDataByLbAppReviewId($request->query('id'), 'updated_at'), 'Y-m-d H:i:s', 'UTC', 'd-m-Y H:i:s', $companyId),
            // 'last_update_by' => (LbEnvSolid::getLastDataByLbAppReviewId($request->query('id'), 'pic') == '') ? '-' : LbEnvSolid::getLastDataByLbAppReviewId($request->query('id'), 'pic'),
            'url_preview' => 'logbook/operational/env-solidwaste/' . $request->query('id') . '/preview'
        ];

        $documents[] = [
            'document' => "Temperature Form",
            'last_update' => (LbTemperature::getLastDataByLbAppReviewId($request->query('id'), 'updated_at') == '') ? '-' : Helper::DateConvertFormatTz(LbTemperature::getLastDataByLbAppReviewId($request->query('id'), 'updated_at'), 'Y-m-d H:i:s', 'UTC', 'd-m-Y H:i:s', $companyId),
            'url_preview' => 'logbook/operational/temperature/' . $request->query('id') . '/preview'
        ];

        $documents[] = [
            'document' => "Toilet Checklist (Opening Shift)",
            'last_update' => (LbToilet::getLastDataByLbAppReviewId($request->query('id'), 'updated_at') == '') ? '-' : Helper::DateConvertFormatTz(LbToilet::getLastDataByLbAppReviewId($request->query('id'), 'updated_at'), 'Y-m-d H:i:s', 'UTC', 'd-m-Y H:i:s', $companyId),
            // 'last_update_by' => '-',
            'url_preview' => 'logbook/operational/toilet/' . $request->query('id') . '/preview?shift=1'
        ];

        $documents[] = [
            'document' => "Toilet Checklist (Closing Shift)",
            'last_update' => (LbToilet::getLastDataByLbAppReviewId($request->query('id'), 'updated_at') == '') ? '-' : Helper::DateConvertFormatTz(LbToilet::getLastDataByLbAppReviewId($request->query('id'), 'updated_at'), 'Y-m-d H:i:s', 'UTC', 'd-m-Y H:i:s', $companyId),
            // 'last_update_by' => '-',
            'url_preview' => 'logbook/operational/toilet/' . $request->query('id') . '/preview?shift=2'
        ];

        $documents[] = [
            'document' => "Toilet Checklist (Midnite Shift)",
            'last_update' => (LbToilet::getLastDataByLbAppReviewId($request->query('id'), 'updated_at') == '') ? '-' : Helper::DateConvertFormatTz(LbToilet::getLastDataByLbAppReviewId($request->query('id'), 'updated_at'), 'Y-m-d H:i:s', 'UTC', 'd-m-Y H:i:s', $companyId),
            // 'last_update_by' => '-',
            'url_preview' => 'logbook/operational/toilet/' . $request->query('id') . '/preview?shift=3'
        ];

        $documents[] = [
            'document' => "Organoleptik",
            'last_update' => (LbOrganoleptik::getLastDataByLbAppReviewId($request->query('id'), 'updated_at') == '') ? '-' : Helper::DateConvertFormatTz(LbOrganoleptik::getLastDataByLbAppReviewId($request->query('id'), 'updated_at'), 'Y-m-d H:i:s', 'UTC', 'd-m-Y H:i:s', $companyId),
            // 'last_update_by' => (LbOrganoleptik::getLastDataByLbAppReviewId($request->query('id'), 'pic') == '') ? '-' : LbOrganoleptik::getLastDataByLbAppReviewId($request->query('id'), 'pic'),
            'url_preview' => 'logbook/operational/organoleptik/' . $request->query('id') . '/preview'
        ];

        $documents[] = [
            'document' => "Money Sales Handling",
            'last_update' => (LbMonSls::getLastDataByLbAppReviewId($request->query('id'), 'updated_at') == '') ? '-' : Helper::DateConvertFormatTz(LbMonSls::getLastDataByLbAppReviewId($request->query('id'), 'updated_at'), 'Y-m-d H:i:s', 'UTC', 'd-m-Y H:i:s', $companyId),
            // 'last_update_by' => (LbMonSls::getLastDataByLbAppReviewId($request->query('id'), 'pic') == '') ? '-' : LbMonSls::getLastDataByLbAppReviewId($request->query('id'), 'pic'),
            'url_preview' => 'logbook/money-sales/' . $request->query('id') . '/preview'
        ];

        // production planning per product

        $lbProductProdPlans = LbProductProdPlan::select('product')->get();
        foreach ($lbProductProdPlans as $lbProductProdPlan) {
            $documents[] = [
                'document' => "Production Planning (" . $lbProductProdPlan->product . ")",
                'last_update' => (LbProdPlan::getLastDataByLbAppReviewId($request->query('id'), 'updated_at') == '') ? '-' : Helper::DateConvertFormatTz(LbProdPlan::getLastDataByLbAppReviewId($request->query('id'), 'updated_at'), 'Y-m-d H:i:s', 'UTC', 'd-m-Y H:i:s', $companyId),
                // 'last_update_by' => (LbMonSls::getLastDataByLbAppReviewId($request->query('id'), 'pic') == '') ? '-' : LbMonSls::getLastDataByLbAppReviewId($request->query('id'), 'pic'),
                'url_preview' => 'logbook/production-planning/' . $request->query('id') . '/preview?product=' . $lbProductProdPlan->product
            ];
        }

        return Datatables::of($documents)
                        ->addIndexColumn()
                        ->make();
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                        'pic_mod' => 'required',
                    ]);

        $lbAppReview = LbAppReview::find($request->id);
        $lbAppReview->mod_pic = $request->pic_mod;
        $lbAppReview->mod_approval = 1;
        if ($lbAppReview->save()) {
            $stat = 'success';
            $msg = Lang::get("message.approve.success", ["data" => Lang::get("application review logbook")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.approve.failed", ["data" => Lang::get("application review logbook")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function unapprove($id)
    {
        $lbAppReview = LbAppReview::find($id);
        $lbAppReview->mod_approval = 0;
        if ($lbAppReview->save()) {
            $stat = 'success';
            $msg = Lang::get("message.unapprove.success", ["data" => Lang::get("application review logbook")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.unapprove.failed", ["data" => Lang::get("application review logbook")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function create(Request $request){

        if(  $request->has('date') ){
            $date = Date('Y-m-d');
        } else {
            $date = Date('Y-m-d');
            $dateYesterday = Date('Y/m/d', strtotime('-1 days'));
        }

        $stores = DB::table('plants')
                    ->where('type', 1)
                    ->where('status', 1)
                    ->select('id')
                    ->get();

        DB::beginTransaction();
        $success = false;
        $error = '';

        LbAppReview::disableAuditing();
        LbDlyInvKitchen::disableAuditing();
        LbDlyInvCashier::disableAuditing();
        LbDlyInvWarehouse::disableAuditing();
        LbDlyWasted::disableAuditing();
        LbStockCard::disableAuditing();
        LbRecMaterial::disableAuditing();
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
        LbWaterMeter::disableAuditing();
        LbElectricMeter::disableAuditing();
        LbGasMeter::disableAuditing();
        LbEnvPump::disableAuditing();
        LbEnvWater::disableAuditing();
        LbEnvSolid::disableAuditing();
        LbOrganoleptik::disableAuditing();

        foreach ($stores as $store) {

            $lbAppReviewId = 0;

            // check app review have already created ?
            $checkAppReview = DB::table('lb_app_reviews')
                                ->where('plant_id', $store->id)
                                ->where('date', $date);

            if($checkAppReview->count() > 0){
                // already created
                $lbAppReview = $checkAppReview->select('id')->first();
                $lbAppReviewId = $lbAppReview->id;
            } else {
                // not yet created
                $lbAppReview = new LbAppReview;
                $lbAppReview->plant_id = $store->id;
                $lbAppReview->date = $date;
                if( $lbAppReview->save() ){
                    $lbAppReviewId = $lbAppReview->id;
                }
            }

            // check app review yesterday have already created ? for daily inventory
            $lbAppReviewIdYesterday = 0;
            $checkAppReviewYesterday = DB::table('lb_app_reviews')
                                ->where('plant_id', $store->id)
                                ->where('date', $dateYesterday);

            if($checkAppReviewYesterday->count() > 0){
                // already created
                $lbAppReviewYesterday = $checkAppReviewYesterday->select('id')->first();
                $lbAppReviewIdYesterday = $lbAppReviewYesterday->id;
            }

            if( $lbAppReviewId > 0 ){
                if( $this->generateDailyInventoryKitchen($lbAppReview->id, $lbAppReviewIdYesterday) ){
                    if( $this->generateDailyInventoryCashier($lbAppReview->id, $lbAppReviewIdYesterday) ){
                        if( $this->generateDailyInventoryWarehouse($lbAppReview->id, $lbAppReviewIdYesterday) ){
                            if( $this->generateDailyCleaning($lbAppReview->id) ){
                                if( $this->generateBriefing($lbAppReview->id) ){
                                    if( $this->generateDailyDuties($lbAppReview->id) ){
                                        if( $this->generateCleaningDuties($lbAppReview->id) ){
                                            if( $this->generateToiletChecklist($lbAppReview->id) ){
                                                if( $this->generateTemperature($lbAppReview->id) ){
                                                    if( $this->generateMoneySales($lbAppReview->id) ){
                                                        if( $this->generateProductionPlanning($lbAppReview->id) ){
                                                            $success = true;
                                                        } else {
                                                            $success = false;
                                                            $error = 'Generate logbook application error : failed to generate production planning';
                                                            break;
                                                        }
                                                    } else {
                                                        $success = false;
                                                        $error = 'Generate logbook application error : failed to generate money sales handling';
                                                        break;
                                                    }
                                                } else {
                                                    $success = false;
                                                    $error = 'Generate logbook application error : failed to generate temperature';
                                                    break;
                                                }
                                            } else {
                                                $success = false;
                                                $error = 'Generate logbook application error : failed to generate toilet checklist';
                                                break;
                                            }
                                        } else {
                                            $success = false;
                                            $error = 'Generate logbook application error : failed to generate cleaning duties';
                                            break;
                                        }
                                    } else {
                                        $success = false;
                                        $error = 'Generate logbook application error : failed to generate daily duties';
                                        break;
                                    }
                                } else {
                                    $success = false;
                                    $error = 'Generate logbook application error : failed to generate daily briefing dut roster';
                                    break;
                                }
                            } else {
                                $success = false;
                                $error = 'Generate logbook application error : failed to generate daily cleaning';
                                break;
                            }
                        } else {
                            $success = false;
                            $error = 'Generate logbook application error : failed to generate daily inventory warehouse';
                            break;
                        }
                    } else {
                        $success = false;
                        $error = 'Generate logbook application error : failed to generate daily inventory cashier';
                        break;
                    }
                } else {
                    $success = false;
                    $error = 'Generate logbook application error : failed to generate daily inventory kitchen';
                    break;
                }
            } else {
                $success = false;
                $error = 'Generate logbook application error : failed to get app review id';
                break;
            }
        }

        LbAppReview::enableAuditing();
        LbDlyInvKitchen::enableAuditing();
        LbDlyInvCashier::enableAuditing();
        LbDlyInvWarehouse::enableAuditing();
        LbDlyWasted::enableAuditing();
        LbStockCard::enableAuditing();
        LbRecMaterial::enableAuditing();
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
        LbWaterMeter::enableAuditing();
        LbElectricMeter::enableAuditing();
        LbGasMeter::enableAuditing();
        LbEnvPump::enableAuditing();
        LbEnvWater::enableAuditing();
        LbEnvSolid::enableAuditing();
        LbOrganoleptik::enableAuditing();

        if($success){
            DB::commit();
        } else {
            DB::rollBack();
            Log::alert($error);
        }

        !dd('success');
    }

    public function generateDailyInventoryKitchen($lbAppReviewId, $lbAppReviewIdYesterday){

        $countCheck  = DB::table('lb_dly_inv_kitchens')
                    ->where('lb_app_review_id', $lbAppReviewId)
                    ->count();

        $result = true;

        if($countCheck <= 0){

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
                if($lbDlyInvKitchen->save()){
                    $result = true;
                }else{
                    $result = false;
                    break;
                }
            }
        }

        return $result;
    }

    public function generateDailyInventoryCashier($lbAppReviewId, $lbAppReviewIdYesterday){

        $countCheck  = DB::table('lb_dly_inv_cashiers')
                    ->where('lb_app_review_id', $lbAppReviewId)
                    ->count();

        $result = true;

        if($countCheck <= 0){
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
                if($lbDlyInvCashier->save()){
                    $result = true;
                }else{
                    $result = false;
                    break;
                }
            }
        }

        return $result;
    }

    public function generateDailyInventoryWarehouse($lbAppReviewId, $lbAppReviewIdYesterday){

        $countCheck  = DB::table('lb_dly_inv_warehouses')
                    ->where('lb_app_review_id', $lbAppReviewId)
                    ->count();

        $result = true;

        if($countCheck <= 0){
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
                if($lbDlyInvWarehouse->save()){
                    $result = true;
                }else{
                    $result = false;
                    break;
                }
            }
        }

        return $result;
    }

    public function generateDailyCleaning($lbAppReviewId){

        $countCheck  = DB::table('lb_dly_cleans')
                    ->where('lb_app_review_id', $lbAppReviewId)
                    ->count();

        $result = true;

        if($countCheck <= 0){
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
                    if($lbDlyClean->save()){
                        $result = true;
                    }else{
                        $result = false;
                        break;
                    }
                }
            }
        }

        return $result;
    }

    public function generateBriefing($lbAppReviewId){

        $countCheck  = DB::table('lb_briefings')
                    ->where('lb_app_review_id', $lbAppReviewId)
                    ->count();

        $result = true;

        if($countCheck <= 0){
            $shifts = ['Morning','Afternoon','Midnite'];
            foreach ($shifts as $shift) {
                $lbBriefing = new LbBriefing;
                $lbBriefing->lb_app_review_id = $lbAppReviewId;
                $lbBriefing->shift = $shift;
                if($lbBriefing->save()){
                    for ($i=0; $i < 4; $i++) {
                        $lbDutyRoster = new LbDutyRoster;
                        $lbDutyRoster->lb_briefing_id = $lbBriefing->id;
                        if ($lbDutyRoster->save()) {
                            $result = true;
                        }else{
                            $result = false;
                            break;
                        }
                    }
                }else{
                    $result = false;
                    break;
                }
            }
        }

        return $result;
    }

    public function generateDailyDuties($lbAppReviewId){

        $countCheck  = DB::table('lb_dly_duties')
                    ->where('lb_app_review_id', $lbAppReviewId)
                    ->count();

        $result = true;

        if($countCheck <= 0){
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
                        if($lbDlyDutiesDet->save()){
                            $result = true;
                        } else{
                            $result = false;
                            break;
                        }
                    }else{
                        $result = false;
                        break;
                    }
                }
            }
        }

        return $result;
    }

    public function generateCleaningDuties($lbAppReviewId){

        $countCheck  = DB::table('lb_clean_duties')
                    ->where('lb_app_review_id', $lbAppReviewId)
                    ->count();

        $result = true;

        if($countCheck <= 0){
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
                            if($lbCleanDutiesDly->save()){
                                $result = true;
                            } else{
                                $result = false;
                                break;
                            }
                        } else {
                            $lbCleanDutiesWly = new LbCleanDutiesWly;
                            $lbCleanDutiesWly->lb_clean_duties_id = $lbCleanDuties->id;
                            $lbCleanDutiesWly->task = $task->task;
                            $lbCleanDutiesWly->day = $task->day;
                            if($lbCleanDutiesWly->save()){
                                $result = true;
                            } else{
                                $result = false;
                                break;
                            }
                        }
                    }
                }else{
                    $result = false;
                    break;
                }

            }
        }

        return $result;
    }

    public function generateToiletChecklist($lbAppReviewId){

        $countCheck  = DB::table('lb_toilets')
                    ->where('lb_app_review_id', $lbAppReviewId)
                    ->count();

        $result = true;

        if($countCheck <= 0){
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
                    if($lbToilet->save()){
                        $result = true;
                    }else{
                        $result = false;
                        break;
                    }
                }
            }
        }

        return $result;
    }

    public function generateTemperature($lbAppReviewId){

        $countCheck  = DB::table('lb_temperatures')
                    ->where('lb_app_review_id', $lbAppReviewId)
                    ->count();

        $result = true;

        if($countCheck <= 0){
            $storageTemps = DB::table('lb_storage_temps')
                            ->where('status', 1)
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
                if($lbTemperature->save()){
                    $result = true;
                }else{
                    $result = false;
                    break;
                }
            }
        }

        return $result;
    }

    public function generateMoneySales($lbAppReviewId){

        $countCheck  = DB::table('lb_mon_sls')
                    ->where('lb_app_review_id', $lbAppReviewId)
                    ->count();

        $result = true;

        if($countCheck <= 0){
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
                            if($lbMonSlsCasDet->save()){
                                $result = true;
                            }else{
                                $result = false;
                                break;
                            }
                        }


                    }else{
                        $result = false;
                        break;
                    }
                }
            }else{
                $result = false;
            }
        }

        return $result;
    }

    public function generateProductionPlanning($lbAppReviewId){

        $products = DB::table('lb_product_prod_plans')->get();
        $times = [6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 0, 1, 2, 3, 4, 5];
        $fryers = ['A', 'B', 'C', 'D'];
        $result = true;

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
                            if( !$lbProdTimeDetail->save() ){
                                $result = false;
                                break;
                            }
                        }

                    }else{
                        $result = false;
                        break;
                    }
                }

                foreach ($fryers as $fryer) {

                    // create prod temp verify
                    $lbProdTempVerify = new LbProdTempVerify;
                    $lbProdTempVerify->lb_prod_plan_id = $lbProdPlan->id;
                    $lbProdTempVerify->fryer = $fryer;
                    if( !$lbProdTempVerify->save() ){
                        $result = false;
                        break;
                    }

                    // create prod quality
                    $lbProdQuality = new LbProdQuality;
                    $lbProdQuality->lb_prod_plan_id = $lbProdPlan->id;
                    $lbProdQuality->fryer = $fryer;
                    if( !$lbProdQuality->save() ){
                        $result = false;
                        break;
                    }

                }

                // create prod used oil
                $lbProdUsedOil = new LbProdUsedOil;
                $lbProdUsedOil->lb_prod_plan_id = $lbProdPlan->id;
                if( !$lbProdUsedOil->save() ){
                    $result = false;
                }

            }else{
                $result = false;
                break;
            }

        }

        return $result;

    }
}
