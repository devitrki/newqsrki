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
        $userAuth = $request->get('userAuth');

        switch ($menu) {
            case 'payment-detail-pos':
                return $this->reportPaymentDetailPos($userAuth->company_id_selected, $request->all());
                break;
            case 'payment-pos':
                return $this->reportPaymentPos($userAuth->company_id_selected, $request->all());
                break;
            case 'promotion-type-pos':
                return $this->reportPromotionTypePos($userAuth->company_id_selected, $request->all());
                break;
            case 'sales-by-menu-pos':
                return $this->reportSalesByMenuPos($userAuth->company_id_selected, $request->all());
                break;
            case 'sales-by-inventory-pos':
                return $this->reportSalesByInventoryPos($userAuth->company_id_selected, $request->all());
                break;
            case 'summary-payment-promotion-pos':
                return $this->reportSummaryPaymentPromotionPos($userAuth->company_id_selected, $request->all());
                break;
            case 'sales-menu-per-hour-pos':
                return $this->reportSalesMenuPerHourPos($userAuth->company_id_selected, $request->all());
                break;
            case 'sales-inventory-per-hour-pos':
                return $this->reportSalesInventoryPerHourPos($userAuth->company_id_selected, $request->all());
                break;
            case 'void-pos':
                return $this->reportVoidPos($userAuth->company_id_selected, $request->all());
                break;
            case 'sales-per-hour-pos':
                return $this->reportSalesPerHourPos($userAuth->company_id_selected, $request->all());
                break;
        }
    }

    public function export($menu, Request $request)
    {
        $userAuth = $request->get('userAuth');

        switch ($menu) {
            case 'payment-detail-pos':
                return $this->exportPaymentDetailPos($userAuth->company_id_selected, $request);
                break;
            case 'payment-pos':
                return $this->exportPaymentPos($userAuth->company_id_selected, $request);
                break;
            case 'promotion-type-pos':
                return $this->exportPromotionTypePos($userAuth->company_id_selected, $request);
                break;
            case 'sales-by-menu-pos':
                return $this->exportSalesByMenuPos($userAuth->company_id_selected, $request);
                break;
            case 'sales-by-inventory-pos':
                return $this->exportSalesByInventoryPos($userAuth->company_id_selected, $request);
                break;
            case 'summary-payment-promotion-pos':
                return $this->exporttSummaryPaymentPromotionPos($userAuth->company_id_selected, $request);
                break;
            case 'sales-menu-per-hour-pos':
                return $this->exporttSalesMenuPerHourPos($userAuth->company_id_selected, $request);
                break;
            case 'sales-inventory-per-hour-pos':
                return $this->exporttSalesInventoryPerHourPos($userAuth->company_id_selected, $request);
                break;
            case 'void-pos':
                return $this->exportVoidPos($userAuth->company_id_selected, $request);
                break;
            case 'sales-per-hour-pos':
                return $this->exportSalesPerHourPos($userAuth->company_id_selected, $request);
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
        $dataview = [
            'menu_id' => $request['menuid'],
        ];
        return view('reports.pos.summary-payment-promotion-pos-view', $dataview)->render();
    }

    public function viewSalesMenuPerHourPos($request)
    {
        $dataview = [
            'menu_id' => $request['menuid'],
        ];
        return view('reports.pos.sales-menu-per-hour-pos-view', $dataview)->render();
    }

    public function viewSalesInventoryPerHourPos($request)
    {
        $dataview = [
            'menu_id' => $request['menuid'],
        ];
        return view('reports.pos.sales-inventory-per-hour-pos-view', $dataview)->render();
    }

    public function viewVoidPos($request)
    {
        $dataview = [
            'menu_id' => $request['menuid'],
        ];
        return view('reports.pos.void-pos-view', $dataview)->render();
    }

    public function viewSalesPerHourPos($request)
    {
        $dataview = [
            'menu_id' => $request['menuid'],
        ];
        return view('reports.pos.sales-per-hour-pos-view', $dataview)->render();
    }

    // report
    public function reportPaymentDetailPos($companyId, $request)
    {
        $dataview = AllPos::getDataPaymentDetailReport($companyId, $request['date']);
        return view('reports.pos.payment-detail-pos-report', $dataview)->render();
    }

    public function reportPaymentPos($companyId, $request)
    {
        $dataview = AllPos::getDataPaymentReport($companyId, $request['store'], $request['from-date'], $request['until-date']);
        return view('reports.pos.payment-pos-report', $dataview)->render();
    }

    public function reportPromotionTypePos($companyId, $request)
    {
        $dataview = AllPos::getDataPromotionTypeReport($companyId, $request['from-date'], $request['until-date']);
        return view('reports.pos.promotion-type-pos-report', $dataview)->render();
    }

    public function reportSalesByMenuPos($companyId, $request)
    {
        $dataview = AllPos::getDataSalesByMenuReport($companyId, $request['store'], $request['pos'], $request['from-date'], $request['until-date']);
        return view('reports.pos.sales-by-menu-pos-report', $dataview)->render();
    }

    public function reportSalesByInventoryPos($companyId, $request)
    {
        $dataview = AllPos::getDataSalesByInventoryReport($companyId, $request['store'], $request['pos'], $request['from-date'], $request['until-date']);
        return view('reports.pos.sales-by-inventory-pos-report', $dataview)->render();
    }

    public function reportSummaryPaymentPromotionPos($companyId, $request)
    {
        $dataview = AllPos::getDataSummaryPaymentPromotionReport($companyId, $request['store'], $request['pos'], $request['date']);
        return view('reports.pos.summary-payment-promotion-pos-report', $dataview)->render();
    }

    public function reportSalesMenuPerHourPos($companyId, $request)
    {
        $dataview = AllPos::getDataSalesMenuPerHourReport($companyId, $request['store'], $request['pos'], $request['from-date'], $request['until-date']);
        return view('reports.pos.sales-menu-per-hour-pos-report', $dataview)->render();
    }

    public function reportSalesInventoryPerHourPos($companyId, $request)
    {
        $dataview = AllPos::getDataSalesInventoryPerHourReport($companyId, $request['store'], $request['pos'], $request['from-date'], $request['until-date']);
        return view('reports.pos.sales-inventory-per-hour-pos-report', $dataview)->render();
    }

    public function reportVoidPos($companyId, $request)
    {
        $dataview = AllPos::getDataVoidReport($companyId, $request['store'], $request['pos'], $request['from-date'], $request['until-date']);
        return view('reports.pos.void-pos-report', $dataview)->render();
    }

    public function reportSalesPerHourPos($companyId, $request)
    {
        $dataview = AllPos::getDataSalesPerHourReport($companyId, $request['store'], $request['pos'], $request['from-date'], $request['until-date']);
        return view('reports.pos.sales-per-hour-pos-report', $dataview)->render();
    }

    // export
    public function exportPaymentDetailPos($companyId, $request)
    {
        $request->validate([
            'date' => 'required',
        ]);

        $param = [
            'company_id' => $companyId,
            'date' => $request->date
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
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

    public function exportPaymentPos($companyId, $request)
    {
        $request->validate([
            'store' => 'required',
            'from_date' => 'required',
            'until_date' => 'required',
        ]);

        $param = [
            'company_id' => $companyId,
            'store' => $request->store,
            'from_date' => $request->from_date,
            'until_date' => $request->until_date
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
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

    public function exportPromotionTypePos($companyId, $request)
    {
        $request->validate([
            'from_date' => 'required',
            'until_date' => 'required',
        ]);

        $param = [
            'company_id' => $companyId,
            'from_date' => $request->from_date,
            'until_date' => $request->until_date
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
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

    public function exportSalesByMenuPos($companyId, $request)
    {
        $request->validate([
            'store' => 'required',
            'pos' => 'required',
            'from_date' => 'required',
            'until_date' => 'required',
        ]);

        $param = [
            'company_id' => $companyId,
            'store' => $request->store,
            'pos' => $request->pos,
            'from_date' => $request->from_date,
            'until_date' => $request->until_date
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
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

    public function exportSalesByInventoryPos($companyId, $request)
    {
        $request->validate([
            'store' => 'required',
            'pos' => 'required',
            'from_date' => 'required',
            'until_date' => 'required',
        ]);

        $param = [
            'company_id' => $companyId,
            'store' => $request->store,
            'pos' => $request->pos,
            'from_date' => $request->from_date,
            'until_date' => $request->until_date
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
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

    public function exporttSummaryPaymentPromotionPos($companyId, $request)
    {
        $request->validate([
            'store' => 'required',
            'pos' => 'required',
            'date' => 'required',
        ]);

        $param = [
            'company_id' => $companyId,
            'store' => $request->store,
            'pos' => $request->pos,
            'date' => $request->date
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
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

    public function exporttSalesMenuPerHourPos($companyId, $request)
    {
        $request->validate([
            'store' => 'required',
            'pos' => 'required',
            'from_date' => 'required',
            'until_date' => 'required',
        ]);

        $param = [
            'company_id' => $companyId,
            'store' => $request->store,
            'pos' => $request->pos,
            'from_date' => $request->from_date,
            'until_date' => $request->until_date
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
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

    public function exporttSalesInventoryPerHourPos($companyId, $request)
    {
        $request->validate([
            'store' => 'required',
            'pos' => 'required',
            'from_date' => 'required',
            'until_date' => 'required',
        ]);

        $param = [
            'company_id' => $companyId,
            'store' => $request->store,
            'pos' => $request->pos,
            'from_date' => $request->from_date,
            'until_date' => $request->until_date
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
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

    public function exportVoidPos($companyId, $request)
    {
        $request->validate([
            'store' => 'required',
            'pos' => 'required',
            'from_date' => 'required',
            'until_date' => 'required',
        ]);

        $param = [
            'company_id' => $companyId,
            'store' => $request->store,
            'pos' => $request->pos,
            'from_date' => $request->from_date,
            'until_date' => $request->until_date
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
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

    public function exportSalesPerHourPos($companyId, $request)
    {
        $request->validate([
            'store' => 'required',
            'pos' => 'required',
            'from_date' => 'required',
            'until_date' => 'required',
        ]);

        $param = [
            'company_id' => $companyId,
            'store' => $request->store,
            'pos' => $request->pos,
            'from_date' => $request->from_date,
            'until_date' => $request->until_date
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
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
