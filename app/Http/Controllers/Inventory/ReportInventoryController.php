<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateReportInventory;
use App\Library\Helper;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

use App\Models\Download;
use App\Models\Plant;
use App\Models\Inventory\GiPlant;
use App\Models\Inventory\GrPlant;
use App\Models\Inventory\GrVendor;
use App\Models\Inventory\Waste;
use App\Models\Inventory\Posto;
use App\Models\Stock;

use App\Models\Inventory\Usedoil\UoStock;
use App\Models\Inventory\Usedoil\UoSaldoVendor;
use App\Models\Inventory\Usedoil\UoMovement;
use App\Models\Inventory\Usedoil\UoMovementItem;
use App\Models\Inventory\Usedoil\UoSaldoVendorHistory;
use App\Models\Inventory\Usedoil\UoVendor;

class ReportInventoryController extends Controller
{
    public function index($menu, Request $request)
    {
        $userAuth = $request->get('userAuth');

        switch ($menu) {
            case 'gi-plant':
                return $this->viewGiPlant($userAuth->company_id_selected, $request->all());
                break;
            case 'gr-plant':
                return $this->viewGrPlant($userAuth->company_id_selected, $request->all());
                break;
            case 'gr-vendor':
                return $this->viewGrVendor($userAuth->company_id_selected, $request->all());
                break;
            case 'waste':
                return $this->viewWaste($userAuth->company_id_selected, $request->all());
                break;
            case 'current-stock':
                return $this->viewCurrentStock($userAuth->company_id_selected, $request->all());
                break;
            case 'outstanding-posto':
                return $this->viewOutstandingPosto($userAuth->company_id_selected, $request->all());
                break;

            // usedoil
            case 'uo-stock-material-plant':
                return $this->viewUoStockMaterialPlant($userAuth->company_id_selected, $request->all());
                break;
            case 'uo-saldo-vendor':
                return $this->viewUoSaldoVendor($userAuth->company_id_selected, $request->all());
                break;
            case 'uo-history-saldo-vendor':
                return $this->viewUoHistorySaldoVendor($userAuth->company_id_selected, $request->all());
                break;
            case 'uo-income-sales-detail':
                return $this->viewUoIncomeSalesDetail($userAuth->company_id_selected, $request->all());
                break;
            case 'uo-income-sales-summary':
                return $this->viewUoIncomeSalesSummary($userAuth->company_id_selected, $request->all());
                break;
        }
    }

    public function report($menu, Request $request)
    {
        $userAuth = $request->get('userAuth');

        switch ($menu) {
            case 'gi-plant':
                return $this->reportGiPlant($request->all());
                break;
            case 'gr-plant':
                return $this->reportGrPlant($request->all());
                break;
            case 'gr-vendor':
                return $this->reportGrVendor($request->all());
                break;
            case 'waste':
                return $this->reportWaste($userAuth->company_id_selected, $request->all());
                break;
            case 'current-stock':
                return $this->reportCurrentStock($request->all());
                break;
            case 'outstanding-posto':
                return $this->reportOutstandingPosto($request->all());
                break;

            // usedoil
            case 'uo-stock-material-plant':
                return $this->reportUoStockMaterialPlant($userAuth->company_id_selected, $request->all());
                break;
            case 'uo-saldo-vendor':
                return $this->reportUoSaldoVendor($userAuth->company_id_selected, $request->all());
                break;
            case 'uo-history-saldo-vendor':
                return $this->reportUoHistorySaldoVendor($userAuth->company_id_selected, $request->all());
                break;
            case 'uo-income-sales-detail':
                return $this->reportUoIncomeSalesDetail($userAuth->company_id_selected, $request->all());
                break;
            case 'uo-income-sales-summary':
                return $this->reportUoIncomeSalesSummary($userAuth->company_id_selected, $request->all());
                break;
        }
    }

    public function export($menu, Request $request)
    {
        $userAuth = $request->get('userAuth');

        switch ($menu) {
            case 'gi-plant':
                return $this->exportGiPlant($userAuth->company_id_selected, $request);
                break;
            case 'gr-plant':
                return $this->exportGrPlant($userAuth->company_id_selected, $request);
                break;
            case 'gr-vendor':
                return $this->exportGrVendor($userAuth->company_id_selected, $request);
                break;
            case 'waste':
                return $this->exportWaste($userAuth->company_id_selected, $request);
                break;
            case 'current-stock':
                return $this->exportCurrentStock($userAuth->company_id_selected, $request);
                break;
            case 'outstanding-posto':
                return $this->exportOutstandingPosto($userAuth->company_id_selected, $request);
                break;

            // usedoil
            case 'uo-history-saldo-vendor':
                return $this->exportUoHistorySaldoVendor($userAuth->company_id_selected, $request);
                break;
            case 'uo-income-sales-detail':
                return $this->exportUoIncomeSalesDetail($userAuth->company_id_selected, $request);
                break;
            case 'uo-income-sales-summary':
                return $this->exportUoIncomeSalesSummary($userAuth->company_id_selected, $request);
                break;
        }
    }

    // view
    public function viewGiPlant($companyId, $request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect($companyId, 'outlet', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'menu_id' => $request['menuid'],
        ];
        return view('reports.inventory.gi-plant-view', $dataview)->render();
    }

    public function viewGrPlant($companyId, $request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect($companyId, 'outlet', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'menu_id' => $request['menuid'],
        ];
        return view('reports.inventory.gr-plant-view', $dataview)->render();
    }

    public function viewGrVendor($companyId, $request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect($companyId, 'all', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'menu_id' => $request['menuid'],
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
        ];
        return view('reports.inventory.gr-vendor-view', $dataview)->render();
    }

    public function viewWaste($companyId, $request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect($companyId, 'all', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'menu_id' => $request['menuid'],
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
        ];
        return view('reports.inventory.waste-view', $dataview)->render();
    }

    public function viewCurrentStock($companyId, $request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect($companyId, 'all', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'menu_id' => $request['menuid'],
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
        ];
        return view('reports.inventory.current-stock-view', $dataview)->render();
    }

    public function viewOutstandingPosto($companyId, $request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect($companyId, 'all', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'menu_id' => $request['menuid'],
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
        ];
        return view('reports.inventory.outstanding-posto-view', $dataview)->render();
    }

    // usedoil
    public function viewUoStockMaterialPlant($companyId, $request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect($companyId, 'all', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'menu_id' => $request['menuid'],
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
        ];
        return view('reports.inventory.usedoil.uo-stock-material-plant-view', $dataview)->render();
    }

    public function viewUoSaldoVendor($companyId, $request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect($companyId, 'all', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'menu_id' => $request['menuid'],
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
        ];
        return view('reports.inventory.usedoil.uo-saldo-vendor-view', $dataview)->render();
    }

    public function viewUoHistorySaldoVendor($companyId, $request)
    {
        $first_vendor_id = UoVendor::getFirstVendorIdSelect($companyId, 'all', true);
        $first_vendor_name = UoVendor::getNameVendorById($first_vendor_id);

        $dataview = [
            'menu_id' => $request['menuid'],
            'first_vendor_id' => $first_vendor_id,
            'first_vendor_name' => $first_vendor_name,
        ];
        return view('reports.inventory.usedoil.uo-history-saldo-vendor-view', $dataview)->render();
    }

    public function viewUoIncomeSalesDetail($companyId, $request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect($companyId, 'all', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'menu_id' => $request['menuid'],
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
        ];
        return view('reports.inventory.usedoil.uo-income-sales-detail-view', $dataview)->render();
    }

    public function viewUoIncomeSalesSummary($companyId, $request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect($companyId, 'all', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'menu_id' => $request['menuid'],
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
        ];
        return view('reports.inventory.usedoil.uo-income-sales-summary-view', $dataview)->render();
    }

    // report
    public function reportGiPlant($request)
    {
        $dataview = GiPlant::getDataReport($request['plant-id'], $request['from-date'], $request['until-date']);
        return view('reports.inventory.gi-plant-report', $dataview)->render();
    }

    public function reportGrPlant($request)
    {
        $dataview = GrPlant::getDataReport($request['plant-id'], $request['from-date'], $request['until-date']);
        return view('reports.inventory.gr-plant-report', $dataview)->render();
    }

    public function reportGrVendor($request)
    {
        $dataview = GrVendor::getDataReport($request['plant-id'], $request['from-date'], $request['until-date']);
        return view('reports.inventory.gr-vendor-report', $dataview)->render();
    }

    public function reportWaste($companyId, $request)
    {
        $dataview = Waste::getDataReport($companyId, $request['plant-id'], $request['hide'], $request['from-date'], $request['until-date'], Auth::id());
        return view('reports.inventory.waste-report', $dataview)->render();
    }

    public function reportCurrentStock($request)
    {
        $dataview = Stock::getDataReport($request['plant-id'], $request['material-type']);
        return view('reports.inventory.current-stock-report', $dataview)->render();
    }

    public function reportOutstandingPosto($request)
    {
        $dataview = Posto::getDataReport($request['plant-id']);
        return view('reports.inventory.outstanding-posto-report', $dataview)->render();
    }

    // usedoil
    public function reportUoStockMaterialPlant($companyId, $request)
    {
        $dataview = UoStock::getDataReport($companyId, $request['plant-id']);
        return view('reports.inventory.usedoil.uo-stock-material-plant-report', $dataview)->render();
    }

    public function reportUoSaldoVendor($companyId, $request)
    {
        $dataview = UoSaldoVendor::getDataReport($companyId);
        return view('reports.inventory.usedoil.uo-saldo-vendor-report', $dataview)->render();
    }

    public function reportUoHistorySaldoVendor($companyId, $request)
    {
        $dataview = UoSaldoVendorHistory::getDataReport($companyId, $request['vendor-id'], $request['from-date'], $request['until-date']);
        return view('reports.inventory.usedoil.uo-history-saldo-vendor-report', $dataview)->render();
    }

    public function reportUoIncomeSalesDetail($companyId, $request)
    {
        $dataview = UoMovementItem::getDataReport($companyId, $request['plant-id'], $request['from-date'], $request['until-date'], Auth::id());
        return view('reports.inventory.usedoil.uo-income-sales-detail-report', $dataview)->render();
    }

    public function reportUoIncomeSalesSummary($companyId, $request)
    {
        $dataview = UoMovement::getDataReport($companyId, $request['from-date'], $request['until-date']);
        return view('reports.inventory.usedoil.uo-income-sales-summary-report', $dataview)->render();
    }

    // export
    public function exportGiPlant($companyId, $request)
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
        $download->name = 'GI Plant';
        $download->module = 'Inventory';
        $download->type = 'gi-plant';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportInventory::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("gi plant")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("gi plant")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportGrPlant($companyId, $request)
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
        $download->name = 'GR Plant';
        $download->module = 'Inventory';
        $download->type = 'gr-plant';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportInventory::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("gr plant")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("gr plant")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportGrVendor($companyId, $request)
    {
        $request->validate([
            'plant' => 'required',
            'from_date' => 'required',
            'until_date' => 'required',
        ]);

        $param = [
            'plant' => $request->plant,
            'from_date' => $request->from_date,
            'until_date' => $request->until_date,
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
        $download->name = 'GR PO Vendor';
        $download->module = 'Inventory';
        $download->type = 'gr-vendor';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportInventory::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("gr po vendor")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("gr po vendor")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportWaste($companyId, $request)
    {
        $request->validate([
            'plant' => 'required',
            'hide' => 'required',
            'from_date' => 'required',
            'until_date' => 'required',
        ]);

        $param = [
            'company_id' => $companyId,
            'plant' => $request->plant,
            'hide' => $request->hide,
            'from_date' => $request->from_date,
            'until_date' => $request->until_date,
            'user_id' => Auth::id()
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
        $download->name = 'Waste / Scrap';
        $download->module = 'Inventory';
        $download->type = 'waste';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportInventory::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("waste")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("waste")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportCurrentStock($companyId, $request)
    {
        $request->validate([
            'plant' => 'required',
            'material_type' => 'required',
        ]);

        $param = [
            'plant' => $request->plant,
            'material_type' => $request->material_type
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
        $download->name = 'Current Stock';
        $download->module = 'Inventory';
        $download->type = 'current-stock';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportInventory::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("current stock")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("current stock")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportOutstandingPosto($companyId, $request)
    {
        $request->validate([
            'plant' => 'required',
        ]);

        $param = [
            'plant' => $request->plant,
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
        $download->name = 'Outstanding PO-STO';
        $download->module = 'Inventory';
        $download->type = 'outstanding-posto';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportInventory::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("outstanding posto")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("outstanding posto")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    // usedoil
    public function exportUoHistorySaldoVendor($companyId, $request)
    {
        $request->validate([
            'vendor' => 'required',
            'from_date' => 'required',
            'until_date' => 'required',
        ]);

        $param = [
            'company_id' => $companyId,
            'vendor' => $request->vendor,
            'from_date' => $request->from_date,
            'until_date' => $request->until_date,
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
        $download->name = 'History Saldo Vendor';
        $download->module = 'Used Oil';
        $download->type = 'uo-history-saldo-vendor';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportInventory::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("history saldo vendor")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("history saldo vendor")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportUoIncomeSalesDetail($companyId, $request)
    {
        $request->validate([
            'plant' => 'required',
            'from_date' => 'required',
            'until_date' => 'required',
        ]);

        $param = [
            'company_id' => $companyId,
            'plant' => $request->plant,
            'from_date' => $request->from_date,
            'until_date' => $request->until_date,
            'user_id' => Auth::id()
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
        $download->name = 'Income Sales Detail';
        $download->module = 'Used Oil';
        $download->type = 'uo-income-sales-detail';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportInventory::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("income sales detail")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("income sales detail")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportUoIncomeSalesSummary($companyId, $request)
    {
        $request->validate([
            'from_date' => 'required',
            'until_date' => 'required',
        ]);

        $param = [
            'company_id' => $companyId,
            'from_date' => $request->from_date,
            'until_date' => $request->until_date,
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
        $download->name = 'Income Sales Summary';
        $download->module = 'Used Oil';
        $download->type = 'uo-income-sales-summary';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportInventory::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("income sales detail")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("income sales detail")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

}
