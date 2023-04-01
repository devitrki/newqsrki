<?php

namespace App\Http\Controllers\Logbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

use App\Jobs\GenerateReportLogbook;
use App\Library\Helper;

use App\Models\Download;
use App\Models\Plant;
use App\Models\Logbook\LbDlyInvKitchen;
use App\Models\Logbook\LbDlyInvCashier;
use App\Models\Logbook\LbDlyInvWarehouse;
use App\Models\Logbook\LbStockCard;
use App\Models\Logbook\LbDlyWasted;
use App\Models\Logbook\LbRecMaterial;
use App\Models\Logbook\LbDlyClean;
use App\Models\Logbook\LbDlyDuties;
use App\Models\Logbook\LbCleanDuties;
use App\Models\Logbook\LbWaterMeter;
use App\Models\Logbook\LbElectricMeter;
use App\Models\Logbook\LbGasMeter;
use App\Models\Logbook\LbEnvPump;
use App\Models\Logbook\LbEnvWater;
use App\Models\Logbook\LbEnvSolid;
use App\Models\Logbook\LbTemperature;
use App\Models\Logbook\LbToilet;
use App\Models\Logbook\LbOrganoleptik;
use App\Models\Logbook\LbStorageTemp;
use App\Models\Logbook\LbProductProdPlan;

class ReportLogbookController extends Controller
{
    public function index($menu, Request $request)
    {
        $userAuth = $request->get('userAuth');

        switch ($menu) {
            case 'daily-inventory-kitchen':
                return $this->viewDailyInventoryKitchen($userAuth->company_id_selected, $request->all());
                break;
            case 'daily-inventory-cashier':
                return $this->viewDailyInventoryCashier($userAuth->company_id_selected, $request->all());
                break;
            case 'daily-inventory-warehouse':
                return $this->viewDailyInventoryWarehouse($userAuth->company_id_selected, $request->all());
                break;
            case 'stock-card':
                return $this->viewStockCard($userAuth->company_id_selected, $request->all());
                break;
            case 'daily-wasted':
                return $this->viewDailyWasted($userAuth->company_id_selected, $request->all());
                break;
            case 'reception-material':
                return $this->viewReceptionMaterial($userAuth->company_id_selected, $request->all());
                break;
            case 'daily-cleaning':
                return $this->viewDailyCleaning($userAuth->company_id_selected, $request->all());
                break;
            case 'duty-roster':
                return $this->viewDutyRoster($userAuth->company_id_selected, $request->all());
                break;
            case 'daily-duties':
                return $this->viewDailyDuties($userAuth->company_id_selected, $request->all());
                break;
            case 'cleaning-duties':
                return $this->viewCleaningDuties($userAuth->company_id_selected, $request->all());
                break;
            case 'water-meter':
                return $this->viewWaterMeter($userAuth->company_id_selected, $request->all());
                break;
            case 'electric-meter':
                return $this->viewElectricMeter($userAuth->company_id_selected, $request->all());
                break;
            case 'gas-meter':
                return $this->viewGasMeter($userAuth->company_id_selected, $request->all());
                break;
            case 'env-propump':
                return $this->viewEnvPropump($userAuth->company_id_selected, $request->all());
                break;
            case 'env-wastewater':
                return $this->viewEnvWastewater($userAuth->company_id_selected, $request->all());
                break;
            case 'env-solidwaste':
                return $this->viewEnvSolidwaste($userAuth->company_id_selected, $request->all());
                break;
            case 'temperature':
                return $this->viewTemperature($userAuth->company_id_selected, $request->all());
                break;
            case 'toilet':
                return $this->viewToilet($userAuth->company_id_selected, $request->all());
                break;
            case 'organoleptik':
                return $this->viewOrganoleptik($userAuth->company_id_selected, $request->all());
                break;
            case 'money-sales':
                return $this->viewMoneySales($userAuth->company_id_selected, $request->all());
                break;
            case 'production-planning':
                return $this->viewProductionPlanning($userAuth->company_id_selected, $request->all());
                break;
        }
    }

    public function report($menu, Request $request)
    {
        switch ($menu) {
            case 'daily-inventory-kitchen':
                return $this->reportDailyInventoryKitchen($request->all());
                break;
            case 'daily-inventory-cashier':
                return $this->reportDailyInventoryCashier($request->all());
                break;
            case 'daily-inventory-warehouse':
                return $this->reportDailyInventoryWarehouse($request->all());
                break;
            case 'stock-card':
                return $this->reportStockCard($request->all());
                break;
            case 'daily-wasted':
                return $this->reportDailyWasted($request->all());
                break;
            case 'reception-material':
                return $this->reportReceptionMaterial($request->all());
                break;
            case 'daily-cleaning':
                return $this->reportDailyCleaning($request->all());
                break;
            case 'duty-roster':
                return $this->reportDutyRoster($request->all());
                break;
            case 'daily-duties':
                return $this->reportDailyDuties($request->all());
                break;
            case 'cleaning-duties':
                return $this->reportCleaningDuties($request->all());
                break;
            case 'water-meter':
                return $this->reportWaterMeter($request->all());
                break;
            case 'electric-meter':
                return $this->reportElectricMeter($request->all());
                break;
            case 'gas-meter':
                return $this->reportGasMeter($request->all());
                break;
            case 'env-propump':
                return $this->reportEnvPropump($request->all());
                break;
            case 'env-wastewater':
                return $this->reportEnvWastewater($request->all());
                break;
            case 'env-solidwaste':
                return $this->reportEnvSolidwaste($request->all());
                break;
            case 'temperature':
                return $this->reportTemperature($request->all());
                break;
            case 'toilet':
                return $this->reportToilet($request->all());
                break;
            case 'organoleptik':
                return $this->reportOrganoleptik($request->all());
                break;
            case 'money-sales':
                return $this->reportMoneySales($request->all());
                break;
            case 'production-planning':
                return $this->reportProductionPlanning($request->all());
                break;
        }
    }

    public function export($menu, Request $request)
    {
        $userAuth = $request->get('userAuth');

        switch ($menu) {
            case 'daily-inventory-kitchen':
                return $this->exportDailyInventoryKitchen($userAuth->company_id_selected, $request);
                break;
            case 'daily-inventory-cashier':
                return $this->exportDailyInventoryCashier($userAuth->company_id_selected, $request);
                break;
            case 'daily-inventory-warehouse':
                return $this->exportDailyInventoryWarehouse($userAuth->company_id_selected, $request);
                break;
            case 'stock-card':
                return $this->exportStockCard($userAuth->company_id_selected, $request);
                break;
            case 'daily-wasted':
                return $this->exportDailyWasted($userAuth->company_id_selected, $request);
                break;
            case 'reception-material':
                return $this->exportReceptionMaterial($userAuth->company_id_selected, $request);
                break;
            case 'daily-cleaning':
                return $this->exportDailyCleaning($userAuth->company_id_selected, $request);
                break;
            case 'duty-roster':
                return $this->exportDutyRoster($userAuth->company_id_selected, $request);
                break;
            case 'daily-duties':
                return $this->exportDailyDuties($userAuth->company_id_selected, $request);
                break;
            case 'cleaning-duties':
                return $this->exportCleaningDuties($userAuth->company_id_selected, $request);
                break;
            case 'water-meter':
                return $this->exportWaterMeter($userAuth->company_id_selected, $request);
                break;
            case 'gas-meter':
                return $this->exportGasMeter($userAuth->company_id_selected, $request);
                break;
            case 'electric-meter':
                return $this->exportElectricMeter($userAuth->company_id_selected, $request);
                break;
            case 'env-propump':
                return $this->exportEnvPropump($userAuth->company_id_selected, $request);
                break;
            case 'env-wastewater':
                return $this->exportEnvWastewater($userAuth->company_id_selected, $request);
                break;
            case 'env-solidwaste':
                return $this->exportEnvSolidwaste($userAuth->company_id_selected, $request);
                break;
            case 'temperature':
                return $this->exportTemperature($userAuth->company_id_selected, $request);
                break;
            case 'toilet':
                return $this->exportToilet($userAuth->company_id_selected, $request);
                break;
            case 'organoleptik':
                return $this->exportOrganoleptik($userAuth->company_id_selected, $request);
                break;
            case 'money-sales':
                return $this->exportMoneySales($userAuth->company_id_selected, $request);
                break;
            case 'production-planning':
                return $this->exportProductionPlanning($userAuth->company_id_selected, $request);
                break;
        }
    }

    // view
    public function viewDailyInventoryKitchen($companyId, $request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect($companyId, 'outlet', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'menu_id' => $request['menuid'],
        ];
        return view('reports.logbook.daily-inventory-kitchen-view', $dataview)->render();
    }

    public function viewDailyInventoryCashier($companyId, $request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect($companyId, 'outlet', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'menu_id' => $request['menuid'],
        ];
        return view('reports.logbook.daily-inventory-cashier-view', $dataview)->render();
    }

    public function viewDailyInventoryWarehouse($companyId, $request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect($companyId, 'outlet', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'menu_id' => $request['menuid'],
        ];
        return view('reports.logbook.daily-inventory-warehouse-view', $dataview)->render();
    }

    public function viewStockCard($companyId, $request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect($companyId, 'outlet', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'months' => Helper::getListMonth(),
            'years' => Helper::getListYear(5),
            'menu_id' => $request['menuid'],
        ];
        return view('reports.logbook.stock-card-view', $dataview)->render();
    }

    public function viewDailyWasted($companyId, $request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect($companyId, 'outlet', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'menu_id' => $request['menuid'],
        ];
        return view('reports.logbook.daily-wasted-view', $dataview)->render();
    }

    public function viewReceptionMaterial($companyId, $request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect($companyId, 'outlet', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'menu_id' => $request['menuid'],
        ];
        return view('reports.logbook.reception-material-view', $dataview)->render();
    }

    public function viewDailyCleaning($companyId, $request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect($companyId, 'outlet', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'menu_id' => $request['menuid'],
        ];
        return view('reports.logbook.daily-cleaning-view', $dataview)->render();
    }

    public function viewDutyRoster($companyId, $request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect($companyId, 'outlet', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'menu_id' => $request['menuid'],
        ];
        return view('reports.logbook.duty-roster-view', $dataview)->render();
    }

    public function viewDailyDuties($companyId, $request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect($companyId, 'outlet', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'menu_id' => $request['menuid'],
        ];
        return view('reports.logbook.daily-duties-view', $dataview)->render();
    }

    public function viewWaterMeter($companyId, $request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect($companyId, 'outlet', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'months' => Helper::getListMonth(),
            'years' => Helper::getListYear(5),
            'menu_id' => $request['menuid'],
        ];
        return view('reports.logbook.water-meter-view', $dataview)->render();
    }

    public function viewElectricMeter($companyId, $request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect($companyId, 'outlet', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'months' => Helper::getListMonth(),
            'years' => Helper::getListYear(5),
            'menu_id' => $request['menuid'],
        ];
        return view('reports.logbook.electric-meter-view', $dataview)->render();
    }

    public function viewGasMeter($companyId, $request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect($companyId, 'outlet', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'months' => Helper::getListMonth(),
            'years' => Helper::getListYear(5),
            'menu_id' => $request['menuid'],
        ];
        return view('reports.logbook.gas-meter-view', $dataview)->render();
    }

    public function viewCleaningDuties($companyId, $request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect($companyId, 'outlet', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'menu_id' => $request['menuid'],
        ];
        return view('reports.logbook.cleaning-duties-view', $dataview)->render();
    }

    public function viewEnvPropump($companyId, $request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect($companyId, 'outlet', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'months' => Helper::getListMonth(),
            'years' => Helper::getListYear(5),
            'menu_id' => $request['menuid'],
        ];
        return view('reports.logbook.env-propump-view', $dataview)->render();
    }

    public function viewEnvWastewater($companyId, $request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect($companyId, 'outlet', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'months' => Helper::getListMonth(),
            'years' => Helper::getListYear(5),
            'menu_id' => $request['menuid'],
        ];
        return view('reports.logbook.env-wastewater-view', $dataview)->render();
    }

    public function viewEnvSolidwaste($companyId, $request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect($companyId, 'outlet', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'months' => Helper::getListMonth(),
            'years' => Helper::getListYear(5),
            'menu_id' => $request['menuid'],
        ];
        return view('reports.logbook.env-solidwaste-view', $dataview)->render();
    }

    public function viewToilet($companyId, $request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect($companyId, 'outlet', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'menu_id' => $request['menuid'],
        ];
        return view('reports.logbook.toilet-view', $dataview)->render();
    }

    public function viewOrganoleptik($companyId, $request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect($companyId, 'outlet', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'menu_id' => $request['menuid'],
        ];
        return view('reports.logbook.organoleptik-view', $dataview)->render();
    }

    public function viewTemperature($companyId, $request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect($companyId, 'outlet', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $first_storage_id = LbStorageTemp::getFirstId($companyId);
        $first_storage_name = LbStorageTemp::getNameById($first_storage_id);

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'first_storage_name' => $first_storage_name,
            'menu_id' => $request['menuid'],
        ];
        return view('reports.logbook.temperature-view', $dataview)->render();
    }

    public function viewMoneySales($companyId, $request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect($companyId, 'outlet', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'menu_id' => $request['menuid'],
        ];
        return view('reports.logbook.money-sales-view', $dataview)->render();
    }

    public function viewProductionPlanning($companyId, $request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect($companyId, 'outlet', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);
        $first_product = LbProductProdPlan::getFirstProduct($companyId);

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'first_product' => $first_product,
            'menu_id' => $request['menuid'],
        ];
        return view('reports.logbook.production-planning-view', $dataview)->render();
    }

    // report
    public function reportDailyInventoryKitchen($request)
    {
        $dataview = LbDlyInvKitchen::getDataReport($request['plant-id'], $request['date']);
        return view('reports.logbook.daily-inventory-kitchen-report', $dataview)->render();
    }

    public function reportDailyInventoryCashier($request)
    {
        $dataview = LbDlyInvCashier::getDataReport($request['plant-id'], $request['date']);
        return view('reports.logbook.daily-inventory-cashier-report', $dataview)->render();
    }

    public function reportDailyInventoryWarehouse($request)
    {
        $dataview = LbDlyInvWarehouse::getDataReport($request['plant-id'], $request['date']);
        return view('reports.logbook.daily-inventory-warehouse-report', $dataview)->render();
    }

    public function reportStockCard($request)
    {
        $dataview = LbStockCard::getDataReport($request['plant-id'], $request['year'], $request['month'], $request['material']);
        return view('reports.logbook.stock-card-report', $dataview)->render();
    }

    public function reportDailyWasted($request)
    {
        $dataview = LbDlyWasted::getDataReport($request['plant-id'], $request['date']);
        return view('reports.logbook.daily-wasted-report', $dataview)->render();
    }

    public function reportReceptionMaterial($request)
    {
        $dataview = LbRecMaterial::getDataReport($request['plant-id'], $request['from-date'], $request['until-date']);
        return view('reports.logbook.reception-material-report', $dataview)->render();
    }

    public function reportDailyCleaning($request)
    {
        $dataview = LbDlyClean::getDataReport($request['plant-id'], $request['date'], $request['shift']);
        return view('reports.logbook.daily-cleaning-report', $dataview)->render();
    }

    public function reportDailyDuties($request)
    {
        $dataview = LbDlyDuties::getDataReport($request['plant-id'], $request['date'], $request['section']);
        return view('reports.logbook.daily-duties-report', $dataview)->render();
    }

    public function reportCleaningDuties($request)
    {
        $dataview = LbCleanDuties::getDataReport($request['plant-id'], $request['date'], $request['section']);
        return view('reports.logbook.cleaning-duties-report', $dataview)->render();
    }

    public function reportWaterMeter($request)
    {
        $dataview = LbWaterMeter::getDataReport($request['plant-id'], $request['year'], $request['month']);
        return view('reports.logbook.water-meter-report', $dataview)->render();
    }

    public function reportElectricMeter($request)
    {
        $dataview = LbElectricMeter::getDataReport($request['plant-id'], $request['year'], $request['month']);
        return view('reports.logbook.electric-meter-report', $dataview)->render();
    }

    public function reportGasMeter($request)
    {
        $dataview = LbGasMeter::getDataReport($request['plant-id'], $request['year'], $request['month']);
        return view('reports.logbook.gas-meter-report', $dataview)->render();
    }

    public function reportEnvPropump($request)
    {
        $dataview = LbEnvPump::getDataReport($request['plant-id'], $request['year'], $request['month']);
        return view('reports.logbook.env-propump-report', $dataview)->render();
    }

    public function reportEnvWastewater($request)
    {
        $dataview = LbEnvWater::getDataReport($request['plant-id'], $request['year'], $request['month']);
        return view('reports.logbook.env-wastewater-report', $dataview)->render();
    }

    public function reportEnvSolidwaste($request)
    {
        $dataview = LbEnvSolid::getDataReport($request['plant-id'], $request['year'], $request['month']);
        return view('reports.logbook.env-solidwaste-report', $dataview)->render();
    }

    public function reportTemperature($request)
    {
        $dataview = LbTemperature::getDataReport($request['plant-id'], $request['from-date'], $request['until-date'], $request['storage']);
        return view('reports.logbook.temperature-report', $dataview)->render();
    }

    public function reportToilet($request)
    {
        $dataview = LbToilet::getDataReport($request['plant-id'], $request['date'], $request['shift']);
        return view('reports.logbook.toilet-report', $dataview)->render();
    }

    public function reportOrganoleptik($request)
    {
        $dataview = LbOrganoleptik::getDataReport($request['plant-id'], $request['from-date'], $request['until-date']);
        return view('reports.logbook.organoleptik-report', $dataview)->render();
    }

    // export
    public function exportDailyInventoryKitchen($companyId, $request)
    {
        $request->validate([
            'plant' => 'required',
            'from_date' => 'required',
            'until_date' => 'required',
        ]);

        $param = [
            'plant' => $request->plant,
            'from_date' => $request->from_date,
            'until_date' => $request->until_date
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
        $download->name = 'Daily inventory kitchen';
        $download->module = 'logbook';
        $download->type = 'daily-inventory-kitchen';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportLogbook::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("daily inventory kitchen")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("daily inventory kitchen")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportDailyInventoryCashier($companyId, $request)
    {
        $request->validate([
            'plant' => 'required',
            'from_date' => 'required',
            'until_date' => 'required',
        ]);

        $param = [
            'plant' => $request->plant,
            'from_date' => $request->from_date,
            'until_date' => $request->until_date
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
        $download->name = 'Daily inventory cashier';
        $download->module = 'logbook';
        $download->type = 'daily-inventory-cashier';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportLogbook::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("daily inventory cashier")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("daily inventory cashier")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportDailyInventoryWarehouse($companyId, $request)
    {
        $request->validate([
            'plant' => 'required',
            'from_date' => 'required',
            'until_date' => 'required',
        ]);

        $param = [
            'plant' => $request->plant,
            'from_date' => $request->from_date,
            'until_date' => $request->until_date
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
        $download->name = 'Daily inventory warehouse';
        $download->module = 'logbook';
        $download->type = 'daily-inventory-warehouse';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportLogbook::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("daily inventory warehouse")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("daily inventory warehouse")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportStockCard($companyId, $request)
    {
        $request->validate([
            'plant' => 'required',
            'month' => 'required',
            'year' => 'required',
            'material' => 'required',
        ]);

        $param = [
            'plant' => $request->plant,
            'month' => $request->month,
            'year' => $request->year,
            'material' => $request->material
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
        $download->name = 'Stock Card';
        $download->module = 'logbook';
        $download->type = 'stock-card';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportLogbook::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("stock card")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("stock card")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportDailyWasted($companyId, $request)
    {
        $request->validate([
            'plant' => 'required',
            'from_date' => 'required',
            'until_date' => 'required',
        ]);

        $param = [
            'plant' => $request->plant,
            'from_date' => $request->from_date,
            'until_date' => $request->until_date
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
        $download->name = 'Daily wasted';
        $download->module = 'logbook';
        $download->type = 'daily-wasted';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportLogbook::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("daily inventory warehouse")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("daily inventory warehouse")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportReceptionMaterial($companyId, $request)
    {
        $request->validate([
            'plant' => 'required',
            'from_date' => 'required',
            'until_date' => 'required',
        ]);

        $param = [
            'plant' => $request->plant,
            'from_date' => $request->from_date,
            'until_date' => $request->until_date
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
        $download->name = 'Reception Material / Product Outlet';
        $download->module = 'logbook';
        $download->type = 'reception-material';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportLogbook::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("daily inventory warehouse")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("daily inventory warehouse")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportDailyCleaning($companyId, $request)
    {
        $request->validate([
            'plant' => 'required',
            'date' => 'required',
        ]);

        $param = [
            'plant' => $request->plant,
            'date' => $request->date,
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
        $download->name = 'Cleaning & Sanitation';
        $download->module = 'logbook';
        $download->type = 'daily-cleaning';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportLogbook::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("daily cleaning & sanitation")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("daily cleaning & sanitation")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportDutyRoster($companyId, $request)
    {
        $request->validate([
            'plant' => 'required',
            'from_date' => 'required',
            'until_date' => 'required',
        ]);

        $param = [
            'plant' => $request->plant,
            'from_date' => $request->from_date,
            'until_date' => $request->until_date
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
        $download->name = 'Daily Briefing & Duty Roster';
        $download->module = 'logbook';
        $download->type = 'duty-roster';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportLogbook::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("briefing & duty roster")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("briefing & duty roster")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportDailyDuties($companyId, $request)
    {
        $request->validate([
            'plant' => 'required',
            'date' => 'required',
        ]);

        $param = [
            'plant' => $request->plant,
            'date' => $request->date,
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
        $download->name = 'Daily Duties';
        $download->module = 'logbook';
        $download->type = 'daily-duties';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportLogbook::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("daily duties")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("daily duties")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportCleaningDuties($companyId, $request)
    {
        $request->validate([
            'plant' => 'required',
            'date' => 'required',
        ]);

        $param = [
            'plant' => $request->plant,
            'date' => $request->date,
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
        $download->name = 'Cleaning Duties';
        $download->module = 'logbook';
        $download->type = 'cleaning-duties';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportLogbook::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("cleaning duties")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("cleaning duties")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportWaterMeter($companyId, $request)
    {
        $request->validate([
            'plant' => 'required',
            'month' => 'required',
            'year' => 'required',
        ]);

        $param = [
            'plant' => $request->plant,
            'month' => $request->month,
            'year' => $request->year
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
        $download->name = 'Water Meter Form';
        $download->module = 'logbook';
        $download->type = 'water-meter';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportLogbook::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("water meter form")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("water meter form")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportElectricMeter($companyId, $request)
    {
        $request->validate([
            'plant' => 'required',
            'month' => 'required',
            'year' => 'required'
        ]);

        $param = [
            'plant' => $request->plant,
            'month' => $request->month,
            'year' => $request->year
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
        $download->name = 'Electric Meter Form';
        $download->module = 'logbook';
        $download->type = 'electric-meter';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportLogbook::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("electric meter form")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("electric meter form")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportGasMeter($companyId, $request)
    {
        $request->validate([
            'plant' => 'required',
            'month' => 'required',
            'year' => 'required'
        ]);

        $param = [
            'plant' => $request->plant,
            'month' => $request->month,
            'year' => $request->year
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
        $download->name = 'Gas Meter Form';
        $download->module = 'logbook';
        $download->type = 'gas-meter';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportLogbook::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("gas meter form")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("gas meter form")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportEnvPropump($companyId, $request)
    {
        $request->validate([
            'plant' => 'required',
            'month' => 'required',
            'year' => 'required',
        ]);

        $param = [
            'plant' => $request->plant,
            'month' => $request->month,
            'year' => $request->year
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
        $download->name = 'Env Control (Pro Pump)';
        $download->module = 'logbook';
        $download->type = 'env-propump';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportLogbook::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("env control (pro pump)")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("env control (pro pump)")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportEnvWastewater($companyId, $request)
    {
        $request->validate([
            'plant' => 'required',
            'month' => 'required',
            'year' => 'required'
        ]);

        $param = [
            'plant' => $request->plant,
            'month' => $request->month,
            'year' => $request->year
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
        $download->name = 'Env Control (Wastewater)';
        $download->module = 'logbook';
        $download->type = 'env-wastewater';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportLogbook::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("env control (wastewater)")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("env control (wastewater)")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportEnvSolidwaste($companyId, $request)
    {
        $request->validate([
            'plant' => 'required',
            'month' => 'required',
            'year' => 'required'
        ]);

        $param = [
            'plant' => $request->plant,
            'month' => $request->month,
            'year' => $request->year
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
        $download->name = 'Env Control (Solid Waste)';
        $download->module = 'logbook';
        $download->type = 'env-solidwaste';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportLogbook::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("env control (solid waste)")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("env control (solid waste)")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportTemperature($companyId, $request)
    {
        $request->validate([
            'plant' => 'required',
            'from_date' => 'required',
            'until_date' => 'required',
            'storage' => 'required',
        ]);

        $param = [
            'plant' => $request->plant,
            'from_date' => $request->from_date,
            'until_date' => $request->until_date,
            'storage' => $request->storage,
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
        $download->name = 'Temperature Form';
        $download->module = 'logbook';
        $download->type = 'temperature';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportLogbook::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("temperature form")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("temperature form")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportToilet($companyId, $request)
    {
        $request->validate([
            'plant' => 'required',
            'date' => 'required',
        ]);

        $param = [
            'plant' => $request->plant,
            'date' => $request->date,
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
        $download->name = 'Toilet Checklist';
        $download->module = 'logbook';
        $download->type = 'toilet';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportLogbook::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("toilet checklist")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("toilet checklist")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportOrganoleptik($companyId, $request)
    {
        $request->validate([
            'plant' => 'required',
            'from_date' => 'required',
            'until_date' => 'required',
        ]);

        $param = [
            'plant' => $request->plant,
            'from_date' => $request->from_date,
            'until_date' => $request->until_date
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
        $download->name = 'Organoleptik';
        $download->module = 'logbook';
        $download->type = 'organoleptik';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportLogbook::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("organoleptik")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("organoleptik")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportMoneySales($companyId, $request)
    {
        $request->validate([
            'plant' => 'required',
            'from_date' => 'required',
            'until_date' => 'required',
        ]);

        $param = [
            'plant' => $request->plant,
            'from_date' => $request->from_date,
            'until_date' => $request->until_date
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
        $download->name = 'Money and Sales Handling';
        $download->module = 'logbook';
        $download->type = 'money-sales';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportLogbook::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("money and sales handling")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("money and sales handling")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportProductionPlanning($companyId, $request)
    {
        $request->validate([
            'plant' => 'required',
            'product' => 'required',
            'from_date' => 'required',
            'until_date' => 'required',
        ]);

        $param = [
            'plant' => $request->plant,
            'product' => $request->product,
            'from_date' => $request->from_date,
            'until_date' => $request->until_date
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
        $download->name = 'Production Planning';
        $download->module = 'logbook';
        $download->type = 'production-planning';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportLogbook::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("production planning")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("production planning")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }
}
