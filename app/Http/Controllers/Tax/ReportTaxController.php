<?php

namespace App\Http\Controllers\Tax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

use App\Jobs\GenerateReportTax;

use App\Library\Helper;

use App\Models\Tax\HistorySendTax;
use App\Models\Download;

class ReportTaxController extends Controller
{
    public function index($menu, Request $request)
    {
        switch ($menu) {
            case 'history-send-ftp':
                return $this->viewHistorySendFTP($request->all());
                break;
        }
    }

    public function report($menu, Request $request)
    {
        switch ($menu) {
            case 'history-send-ftp':
                return $this->reportHistorySendFTP($request->all());
                break;
        }
    }

    public function export($menu, Request $request)
    {
        $userAuth = $request->get('userAuth');

        switch ($menu) {
            case 'history-send-ftp':
                return $this->exportHistorySendFTP($userAuth->company_id_selected, $request);
                break;
        }
    }

    // view
    public function viewHistorySendFTP($request)
    {
        $dataview = [
            'menu_id' => $request['menuid'],
        ];
        return view('reports.tax.history-send-ftp-view', $dataview)->render();
    }

    // report
    public function reportHistorySendFTP($request)
    {
        $dataview = HistorySendTax::getDataReport($request['plant-id'], $request['from-date'], $request['until-date'], $request['status']);
        return view('reports.tax.history-send-ftp-report', $dataview)->render();
    }

    // export
    public function exportHistorySendFTP($companyId, $request)
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
        $download->name = 'History Send FTP Tax';
        $download->module = 'Tax';
        $download->type = 'history-send-ftp';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportTax::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("history send ftp tax")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("history send ftp tax")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

}
