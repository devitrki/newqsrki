<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateReportInventory;
use App\Library\Helper;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        switch ($menu) {
            case 'gi-plant':
                return $this->viewGiPlant($request->all());
                break;
            case 'gr-plant':
                return $this->viewGrPlant($request->all());
                break;
            case 'gr-vendor':
                return $this->viewGrVendor($request->all());
                break;
            case 'waste':
                return $this->viewWaste($request->all());
                break;
            case 'current-stock':
                return $this->viewCurrentStock($request->all());
                break;
            case 'outstanding-posto':
                return $this->viewOutstandingPosto($request->all());
                break;

            // usedoil
            case 'uo-stock-material-plant':
                return $this->viewUoStockMaterialPlant($request->all());
                break;
            case 'uo-saldo-vendor':
                return $this->viewUoSaldoVendor($request->all());
                break;
            case 'uo-history-saldo-vendor':
                return $this->viewUoHistorySaldoVendor($request->all());
                break;
            case 'uo-income-sales-detail':
                return $this->viewUoIncomeSalesDetail($request->all());
                break;
            case 'uo-income-sales-summary':
                return $this->viewUoIncomeSalesSummary($request->all());
                break;
        }
    }

    public function report($menu, Request $request)
    {
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
                return $this->reportWaste($request->all());
                break;
            case 'current-stock':
                return $this->reportCurrentStock($request->all());
                break;
            case 'outstanding-posto':
                return $this->reportOutstandingPosto($request->all());
                break;

            // usedoil
            case 'uo-stock-material-plant':
                return $this->reportUoStockMaterialPlant($request->all());
                break;
            case 'uo-saldo-vendor':
                return $this->reportUoSaldoVendor($request->all());
                break;
            case 'uo-history-saldo-vendor':
                return $this->reportUoHistorySaldoVendor($request->all());
                break;
            case 'uo-income-sales-detail':
                return $this->reportUoIncomeSalesDetail($request->all());
                break;
            case 'uo-income-sales-summary':
                return $this->reportUoIncomeSalesSummary($request->all());
                break;
        }
    }

    public function export($menu, Request $request)
    {
        switch ($menu) {
            case 'gi-plant':
                return $this->exportGiPlant($request);
                break;
            case 'gr-plant':
                return $this->exportGrPlant($request);
                break;
            case 'gr-vendor':
                return $this->exportGrVendor($request);
                break;
            case 'waste':
                return $this->exportWaste($request);
                break;
            case 'current-stock':
                return $this->exportCurrentStock($request);
                break;
            case 'outstanding-posto':
                return $this->exportOutstandingPosto($request);
                break;

            // usedoil
            case 'uo-history-saldo-vendor':
                return $this->exportUoHistorySaldoVendor($request);
                break;
            case 'uo-income-sales-detail':
                return $this->exportUoIncomeSalesDetail($request);
                break;
            case 'uo-income-sales-summary':
                return $this->exportUoIncomeSalesSummary($request);
                break;
        }
    }

    // view
    public function viewGiPlant($request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect(true, 'outlet');
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'menu_id' => $request['menuid'],
        ];
        return view('reports.inventory.gi-plant-view', $dataview)->render();
    }

    public function viewGrPlant($request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect(true, 'outlet');
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'menu_id' => $request['menuid'],
        ];
        return view('reports.inventory.gr-plant-view', $dataview)->render();
    }

    public function viewGrVendor($request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect(true, 'all');
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'menu_id' => $request['menuid'],
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
        ];
        return view('reports.inventory.gr-vendor-view', $dataview)->render();
    }

    public function viewWaste($request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect(true, 'all');
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'menu_id' => $request['menuid'],
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
        ];
        return view('reports.inventory.waste-view', $dataview)->render();
    }

    public function viewCurrentStock($request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect(true, 'all');
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'menu_id' => $request['menuid'],
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
        ];
        return view('reports.inventory.current-stock-view', $dataview)->render();
    }

    public function viewOutstandingPosto($request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect(true, 'all');
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'menu_id' => $request['menuid'],
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
        ];
        return view('reports.inventory.outstanding-posto-view', $dataview)->render();
    }

    // usedoil
    public function viewUoStockMaterialPlant($request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect(true, 'all');
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'menu_id' => $request['menuid'],
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
        ];
        return view('reports.inventory.usedoil.uo-stock-material-plant-view', $dataview)->render();
    }

    public function viewUoSaldoVendor($request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect(true, 'all');
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'menu_id' => $request['menuid'],
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
        ];
        return view('reports.inventory.usedoil.uo-saldo-vendor-view', $dataview)->render();
    }

    public function viewUoHistorySaldoVendor($request)
    {
        $first_vendor_id = UoVendor::getFirstVendorIdSelect(true, 'all');
        $first_vendor_name = UoVendor::getNameVendorById($first_vendor_id);

        $dataview = [
            'menu_id' => $request['menuid'],
            'first_vendor_id' => $first_vendor_id,
            'first_vendor_name' => $first_vendor_name,
        ];
        return view('reports.inventory.usedoil.uo-history-saldo-vendor-view', $dataview)->render();
    }

    public function viewUoIncomeSalesDetail($request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect(true, 'all');
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'menu_id' => $request['menuid'],
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
        ];
        return view('reports.inventory.usedoil.uo-income-sales-detail-view', $dataview)->render();
    }

    public function viewUoIncomeSalesSummary($request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect(true, 'all');
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

    public function reportWaste($request)
    {
        $dataview = Waste::getDataReport($request['plant-id'], $request['hide'], $request['from-date'], $request['until-date'], Auth::id());
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
    public function reportUoStockMaterialPlant($request)
    {
        $dataview = UoStock::getDataReport($request['plant-id']);
        return view('reports.inventory.usedoil.uo-stock-material-plant-report', $dataview)->render();
    }

    public function reportUoSaldoVendor($request)
    {
        $dataview = UoSaldoVendor::getDataReport();
        return view('reports.inventory.usedoil.uo-saldo-vendor-report', $dataview)->render();
    }

    public function reportUoHistorySaldoVendor($request)
    {
        $dataview = UoSaldoVendorHistory::getDataReport($request['vendor-id'], $request['from-date'], $request['until-date']);
        return view('reports.inventory.usedoil.uo-history-saldo-vendor-report', $dataview)->render();
    }

    public function reportUoIncomeSalesDetail($request)
    {
        $dataview = UoMovementItem::getDataReport($request['plant-id'], $request['from-date'], $request['until-date'], Auth::id());
        return view('reports.inventory.usedoil.uo-income-sales-detail-report', $dataview)->render();
    }

    public function reportUoIncomeSalesSummary($request)
    {
        $dataview = UoMovement::getDataReport($request['from-date'], $request['until-date']);
        return view('reports.inventory.usedoil.uo-income-sales-summary-report', $dataview)->render();
    }

    // export
    public function exportGiPlant($request)
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
            $msg = \Lang::get("message.export.success", ["data" => \Lang::get("gi plant")]);
        } else {
            $stat = 'failed';
            $msg = \Lang::get("message.export.failed", ["data" => \Lang::get("gi plant")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportGrPlant($request)
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
            $msg = \Lang::get("message.export.success", ["data" => \Lang::get("gr plant")]);
        } else {
            $stat = 'failed';
            $msg = \Lang::get("message.export.failed", ["data" => \Lang::get("gr plant")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportGrVendor($request)
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
            $msg = \Lang::get("message.export.success", ["data" => \Lang::get("gr po vendor")]);
        } else {
            $stat = 'failed';
            $msg = \Lang::get("message.export.failed", ["data" => \Lang::get("gr po vendor")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportWaste($request)
    {
        $request->validate([
            'plant' => 'required',
            'hide' => 'required',
            'from_date' => 'required',
            'until_date' => 'required',
        ]);

        $param = [
            'plant' => $request->plant,
            'hide' => $request->hide,
            'from_date' => $request->from_date,
            'until_date' => $request->until_date,
            'user_id' => Auth::id()
        ];

        // insert to downloads
        $download = new Download;
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
            $msg = \Lang::get("message.export.success", ["data" => \Lang::get("waste")]);
        } else {
            $stat = 'failed';
            $msg = \Lang::get("message.export.failed", ["data" => \Lang::get("waste")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportCurrentStock($request)
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
            $msg = \Lang::get("message.export.success", ["data" => \Lang::get("current stock")]);
        } else {
            $stat = 'failed';
            $msg = \Lang::get("message.export.failed", ["data" => \Lang::get("current stock")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportOutstandingPosto($request)
    {
        $request->validate([
            'plant' => 'required',
        ]);

        $param = [
            'plant' => $request->plant,
        ];

        // insert to downloads
        $download = new Download;
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
            $msg = \Lang::get("message.export.success", ["data" => \Lang::get("outstanding posto")]);
        } else {
            $stat = 'failed';
            $msg = \Lang::get("message.export.failed", ["data" => \Lang::get("outstanding posto")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    // usedoil
    public function exportUoHistorySaldoVendor($request)
    {
        $request->validate([
            'vendor' => 'required',
            'from_date' => 'required',
            'until_date' => 'required',
        ]);

        $param = [
            'vendor' => $request->vendor,
            'from_date' => $request->from_date,
            'until_date' => $request->until_date,
        ];

        // insert to downloads
        $download = new Download;
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
            $msg = \Lang::get("message.export.success", ["data" => \Lang::get("history saldo vendor")]);
        } else {
            $stat = 'failed';
            $msg = \Lang::get("message.export.failed", ["data" => \Lang::get("history saldo vendor")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportUoIncomeSalesDetail($request)
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
            'user_id' => Auth::id()
        ];

        // insert to downloads
        $download = new Download;
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
            $msg = \Lang::get("message.export.success", ["data" => \Lang::get("income sales detail")]);
        } else {
            $stat = 'failed';
            $msg = \Lang::get("message.export.failed", ["data" => \Lang::get("income sales detail")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportUoIncomeSalesSummary($request)
    {
        $request->validate([
            'from_date' => 'required',
            'until_date' => 'required',
        ]);

        $param = [
            'from_date' => $request->from_date,
            'until_date' => $request->until_date,
        ];

        // insert to downloads
        $download = new Download;
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
            $msg = \Lang::get("message.export.success", ["data" => \Lang::get("income sales detail")]);
        } else {
            $stat = 'failed';
            $msg = \Lang::get("message.export.failed", ["data" => \Lang::get("income sales detail")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

}
