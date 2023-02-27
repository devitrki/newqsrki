<?php

namespace App\Http\Controllers\ExternalVendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

use App\Jobs\GenerateReportExternalVendor;

use App\Library\Helper;

use App\Models\ExternalVendor\HistorySendVendor;
use App\Models\Download;

class ReportExternalVendorController extends Controller
{
    public function index($menu, Request $request)
    {
        switch ($menu) {
            case 'history-send-vendor':
                return $this->viewHistorySendVendor($request->all());
                break;
        }
    }

    public function report($menu, Request $request)
    {
        switch ($menu) {
            case 'history-send-vendor':
                return $this->reportHistorySendVendor($request->all());
                break;
        }
    }

    public function export($menu, Request $request)
    {
        $userAuth = $request->get('userAuth');

        switch ($menu) {
            case 'history-send-vendor':
                return $this->exportHistorySendVendor($userAuth->company_id_selected, $request);
                break;
        }
    }

    // view
    public function viewHistorySendVendor($request)
    {
        $dataview = [
            'menu_id' => $request['menuid'],
        ];
        return view('reports.externalVendors.history-send-vendor-view', $dataview)->render();
    }

    // report
    public function reportHistorySendVendor($request)
    {
        $dataview = HistorySendVendor::getDataReport($request['plant-id'], $request['from-date'], $request['until-date'], $request['status']);
        return view('reports.externalVendors.history-send-vendor-report', $dataview)->render();
    }

    // export
    public function exportHistorySendVendor($companyId, $request)
    {
        $request->validate([
            'plant' => 'required',
            'from_date' => 'required',
            'until_date' => 'required',
            'status' => 'required',
        ]);

        $param = [
            'plant' => $request->plant,
            'from_date' => $request->from_date,
            'until_date' => $request->until_date,
            'status' => $request->status
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
        $download->name = 'History Send Vendor';
        $download->module = 'ExternalVendor';
        $download->type = 'history-send-vendor';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportExternalVendor::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("history send vendor")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("history send vendor")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }
}
