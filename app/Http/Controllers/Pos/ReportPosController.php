<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use App\Library\Helper;

use App\Jobs\GenerateReportPos;

use App\Models\Pos\AllPos;
use App\Models\Plant;
use App\Models\Download;

class ReportPosController extends Controller
{
    public function index($menu, Request $request)
    {
        switch ($menu) {
            case 'payment-detail-pos':
                return $this->viewPaymentDetailPos($request->all());
                break;
            case 'payment-pos':
                return $this->viewPaymentPos($request->all());
                break;
            case 'promotion-type-pos':
                return $this->viewPromotionTypePos($request->all());
                break;
            case 'sales-by-menu-pos':
                return $this->viewSalesByMenuPos($request->all());
                break;
            case 'sales-by-inventory-pos':
                return $this->viewSalesByInventoryPos($request->all());
                break;
            case 'summary-payment-promotion-pos':
                return $this->viewSummaryPaymentPromotionPos($request->all());
                break;
            case 'sales-menu-per-hour-pos':
                return $this->viewSalesMenuPerHourPos($request->all());
                break;
            case 'sales-inventory-per-hour-pos':
                return $this->viewSalesInventoryPerHourPos($request->all());
                break;
            case 'void-pos':
                return $this->viewVoidPos($request->all());
                break;
            case 'sales-per-hour-pos':
                return $this->viewSalesPerHourPos($request->all());
                break;
        }
    }

    public function report($menu, Request $request)
    {
        switch ($menu) {
            case 'payment-detail-pos':
                return $this->reportPaymentDetailPos($request->all());
                break;
            case 'payment-pos':
                return $this->reportPaymentPos($request->all());
                break;
            case 'promotion-type-pos':
                return $this->reportPromotionTypePos($request->all());
                break;
            case 'sales-by-menu-pos':
                return $this->reportSalesByMenuPos($request->all());
                break;
            case 'sales-by-inventory-pos':
                return $this->reportSalesByInventoryPos($request->all());
                break;
            case 'summary-payment-promotion-pos':
                return $this->reportSummaryPaymentPromotionPos($request->all());
                break;
            case 'sales-menu-per-hour-pos':
                return $this->reportSalesMenuPerHourPos($request->all());
                break;
            case 'sales-inventory-per-hour-pos':
                return $this->reportSalesInventoryPerHourPos($request->all());
                break;
            case 'void-pos':
                return $this->reportVoidPos($request->all());
                break;
            case 'sales-per-hour-pos':
                return $this->reportSalesPerHourPos($request->all());
                break;
        }
    }

    public function export($menu, Request $request)
    {
        switch ($menu) {
            case 'payment-detail-pos':
                return $this->exportPaymentDetailPos($request);
                break;
            case 'payment-pos':
                return $this->exportPaymentPos($request);
                break;
            case 'promotion-type-pos':
                return $this->exportPromotionTypePos($request);
                break;
            case 'sales-by-menu-pos':
                return $this->exportSalesByMenuPos($request);
                break;
            case 'sales-by-inventory-pos':
                return $this->exportSalesByInventoryPos($request);
                break;
            case 'summary-payment-promotion-pos':
                return $this->exporttSummaryPaymentPromotionPos($request);
                break;
            case 'sales-menu-per-hour-pos':
                return $this->exporttSalesMenuPerHourPos($request);
                break;
            case 'sales-inventory-per-hour-pos':
                return $this->exporttSalesInventoryPerHourPos($request);
                break;
            case 'void-pos':
                return $this->exportVoidPos($request);
                break;
            case 'sales-per-hour-pos':
                return $this->exportSalesPerHourPos($request);
                break;
        }
    }

    // view
    public function viewPaymentDetailPos($request)
    {
        $dataview = [
            'menu_id' => $request['menuid'],
        ];
        return view('reports.pos.payment-detail-pos-view', $dataview)->render();
    }

    public function viewPaymentPos($request)
    {
        $dataview = [
            'menu_id' => $request['menuid'],
        ];
        return view('reports.pos.payment-pos-view', $dataview)->render();
    }

    public function viewPromotionTypePos($request)
    {
        $dataview = [
            'menu_id' => $request['menuid'],
        ];
        return view('reports.pos.promotion-type-pos-view', $dataview)->render();
    }

    public function viewSalesByMenuPos($request)
    {
        $dataview = [
            'menu_id' => $request['menuid'],
        ];
        return view('reports.pos.sales-by-menu-pos-view', $dataview)->render();
    }

    public function viewSalesByInventoryPos($request)
    {
        $dataview = [
            'menu_id' => $request['menuid'],
        ];
        return view('reports.pos.sales-by-inventory-pos-view', $dataview)->render();
    }

    public function viewSummaryPaymentPromotionPos($request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect(true, 'outlet');
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'menu_id' => $request['menuid'],
        ];
        return view('reports.pos.summary-payment-promotion-pos-view', $dataview)->render();
    }

    public function viewSalesMenuPerHourPos($request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect(true, 'outlet');
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'menu_id' => $request['menuid'],
        ];
        return view('reports.pos.sales-menu-per-hour-pos-view', $dataview)->render();
    }

    public function viewSalesInventoryPerHourPos($request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect(true, 'outlet');
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'menu_id' => $request['menuid'],
        ];
        return view('reports.pos.sales-inventory-per-hour-pos-view', $dataview)->render();
    }

    public function viewVoidPos($request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect(true, 'outlet');
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'menu_id' => $request['menuid'],
        ];
        return view('reports.pos.void-pos-view', $dataview)->render();
    }

    public function viewSalesPerHourPos($request)
    {
        $first_plant_id = Plant::getFirstPlantIdSelect(true, 'outlet');
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'menu_id' => $request['menuid'],
        ];
        return view('reports.pos.sales-per-hour-pos-view', $dataview)->render();
    }

    // report
    public function reportPaymentDetailPos($request)
    {
        $dataview = AllPos::getDataPaymentDetailReport($request['date']);
        return view('reports.pos.payment-detail-pos-report', $dataview)->render();
    }

    public function reportPaymentPos($request)
    {
        $dataview = AllPos::getDataPaymentReport($request['store'], $request['from-date'], $request['until-date']);
        return view('reports.pos.payment-pos-report', $dataview)->render();
    }

    public function reportPromotionTypePos($request)
    {
        $dataview = AllPos::getDataPromotionTypeReport($request['from-date'], $request['until-date']);
        return view('reports.pos.promotion-type-pos-report', $dataview)->render();
    }

    public function reportSalesByMenuPos($request)
    {
        $dataview = AllPos::getDataSalesByMenuReport($request['store'], $request['pos'], $request['from-date'], $request['until-date']);
        return view('reports.pos.sales-by-menu-pos-report', $dataview)->render();
    }

    public function reportSalesByInventoryPos($request)
    {
        $dataview = AllPos::getDataSalesByInventoryReport($request['store'], $request['pos'], $request['from-date'], $request['until-date']);
        return view('reports.pos.sales-by-inventory-pos-report', $dataview)->render();
    }

    public function reportSummaryPaymentPromotionPos($request)
    {
        $dataview = AllPos::getDataSummaryPaymentPromotionReport($request['store'], $request['pos'], $request['date']);
        return view('reports.pos.summary-payment-promotion-pos-report', $dataview)->render();
    }

    public function reportSalesMenuPerHourPos($request)
    {
        $dataview = AllPos::getDataSalesMenuPerHourReport($request['store'], $request['pos'], $request['from-date'], $request['until-date']);
        return view('reports.pos.sales-menu-per-hour-pos-report', $dataview)->render();
    }

    public function reportSalesInventoryPerHourPos($request)
    {
        $dataview = AllPos::getDataSalesInventoryPerHourReport($request['store'], $request['pos'], $request['from-date'], $request['until-date']);
        return view('reports.pos.sales-inventory-per-hour-pos-report', $dataview)->render();
    }

    public function reportVoidPos($request)
    {
        $dataview = AllPos::getDataVoidReport($request['store'], $request['pos'], $request['from-date'], $request['until-date']);
        return view('reports.pos.void-pos-report', $dataview)->render();
    }

    public function reportSalesPerHourPos($request)
    {
        $dataview = AllPos::getDataSalesPerHourReport($request['store'], $request['pos'], $request['from-date'], $request['until-date']);
        return view('reports.pos.sales-per-hour-pos-report', $dataview)->render();
    }

    // export
    public function exportPaymentDetailPos($request)
    {
        $request->validate([
            'date' => 'required',
        ]);

        $param = [
            'date' => $request->date
        ];

        // insert to downloads
        $download = new Download;
        $download->name = 'Payment Detail POS';
        $download->module = 'POS';
        $download->type = 'payment-detail-pos';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportPos::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("Payment Detail All Pos")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("Payment Detail All Pos")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportPaymentPos($request)
    {
        $request->validate([
            'store' => 'required',
            'from_date' => 'required',
            'until_date' => 'required',
        ]);

        $param = [
            'store' => $request->store,
            'from_date' => $request->from_date,
            'until_date' => $request->until_date
        ];

        // insert to downloads
        $download = new Download;
        $download->name = 'Payment POS';
        $download->module = 'POS';
        $download->type = 'payment-pos';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportPos::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("Payment Pos")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("Payment Pos")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportPromotionTypePos($request)
    {
        $request->validate([
            'from_date' => 'required',
            'until_date' => 'required',
        ]);

        $param = [
            'from_date' => $request->from_date,
            'until_date' => $request->until_date
        ];

        // insert to downloads
        $download = new Download;
        $download->name = 'Promotion Type POS';
        $download->module = 'POS';
        $download->type = 'promotion-type-pos';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportPos::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("Payment Pos")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("Payment Pos")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportSalesByMenuPos($request)
    {
        $request->validate([
            'store' => 'required',
            'pos' => 'required',
            'from_date' => 'required',
            'until_date' => 'required',
        ]);

        $param = [
            'store' => $request->store,
            'pos' => $request->pos,
            'from_date' => $request->from_date,
            'until_date' => $request->until_date
        ];

        // insert to downloads
        $download = new Download;
        $download->name = 'Sales by Menu Pos';
        $download->module = 'POS';
        $download->type = 'sales-by-menu-pos';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportPos::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("Sales by Menu Pos")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("Sales by Menu Pos")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportSalesByInventoryPos($request)
    {
        $request->validate([
            'store' => 'required',
            'pos' => 'required',
            'from_date' => 'required',
            'until_date' => 'required',
        ]);

        $param = [
            'store' => $request->store,
            'pos' => $request->pos,
            'from_date' => $request->from_date,
            'until_date' => $request->until_date
        ];

        // insert to downloads
        $download = new Download;
        $download->name = 'Sales by Inventory Pos';
        $download->module = 'POS';
        $download->type = 'sales-by-inventory-pos';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportPos::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("Sales by Inventory Pos")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("Sales by Inventory Pos")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exporttSummaryPaymentPromotionPos($request)
    {
        $request->validate([
            'store' => 'required',
            'pos' => 'required',
            'date' => 'required',
        ]);

        $param = [
            'store' => $request->store,
            'pos' => $request->pos,
            'date' => $request->date
        ];

        // insert to downloads
        $download = new Download;
        $download->name = 'Summary Payment and Promotion Pos';
        $download->module = 'POS';
        $download->type = 'summary-payment-promotion-pos';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportPos::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("Summary Payment and Promotion Pos")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("Summary Payment and Promotion Pos")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exporttSalesMenuPerHourPos($request)
    {
        $request->validate([
            'store' => 'required',
            'pos' => 'required',
            'from_date' => 'required',
            'until_date' => 'required',
        ]);

        $param = [
            'store' => $request->store,
            'pos' => $request->pos,
            'from_date' => $request->from_date,
            'until_date' => $request->until_date
        ];

        // insert to downloads
        $download = new Download;
        $download->name = 'Sales by Menu Per Hour Pos';
        $download->module = 'POS';
        $download->type = 'sales-menu-per-hour-pos';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportPos::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("Sales by Menu Per Hour Pos")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("Sales by Menu Per Hour Pos")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exporttSalesInventoryPerHourPos($request)
    {
        $request->validate([
            'store' => 'required',
            'pos' => 'required',
            'from_date' => 'required',
            'until_date' => 'required',
        ]);

        $param = [
            'store' => $request->store,
            'pos' => $request->pos,
            'from_date' => $request->from_date,
            'until_date' => $request->until_date
        ];

        // insert to downloads
        $download = new Download;
        $download->name = 'Sales by Inventory Per Hour Pos';
        $download->module = 'POS';
        $download->type = 'sales-inventory-per-hour-pos';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportPos::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("Sales by Inventory Per Hour Pos")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("Sales by Inventory Per Hour Pos")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportVoidPos($request)
    {
        $request->validate([
            'store' => 'required',
            'pos' => 'required',
            'from_date' => 'required',
            'until_date' => 'required',
        ]);

        $param = [
            'store' => $request->store,
            'pos' => $request->pos,
            'from_date' => $request->from_date,
            'until_date' => $request->until_date
        ];

        // insert to downloads
        $download = new Download;
        $download->name = 'Void (Refund) Pos';
        $download->module = 'POS';
        $download->type = 'void-pos';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportPos::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("Sales by Inventory Per Hour Pos")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("Sales by Inventory Per Hour Pos")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportSalesPerHourPos($request)
    {
        $request->validate([
            'store' => 'required',
            'pos' => 'required',
            'from_date' => 'required',
            'until_date' => 'required',
        ]);

        $param = [
            'store' => $request->store,
            'pos' => $request->pos,
            'from_date' => $request->from_date,
            'until_date' => $request->until_date
        ];

        // insert to downloads
        $download = new Download;
        $download->name = 'Sales Per Hour Pos';
        $download->module = 'POS';
        $download->type = 'sales-per-hour-pos';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportPos::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("Sales Per Hour Pos")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("Sales Per Hour Pos")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

}
