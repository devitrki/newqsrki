<?php

namespace App\Http\Controllers\Interfaces;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Jobs\GenerateReportInterfaces;
use App\Library\Helper;

use App\Models\Pos\AlohaTransactionLog;
use App\Models\Pos\AlohaHistorySendSap;

use App\Models\Download;

class ReportInterfacesController extends Controller
{
    public function index($menu, Request $request)
    {
        switch ($menu) {
            // aloha
            case 'transaction-log-sap-aloha':
                return $this->viewTransactionLogSapAloha($request->all());
                break;
            case 'history-send-sap-aloha':
                return $this->viewHistorySendSapAloha($request->all());
                break;
        }
    }

    public function report($menu, Request $request)
    {
        $userAuth = $request->get('userAuth');

        switch ($menu) {
            // aloha
            case 'transaction-log-sap-aloha':
                return $this->reportTransactionLogSapAloha($userAuth->company_id_selected, $request->all());
                break;
            case 'history-send-sap-aloha':
                return $this->reportHistorySendSapAloha($userAuth->company_id_selected, $request->all());
                break;
        }
    }

    public function export($menu, Request $request)
    {
    }

    // view


    // aloha
    public function viewTransactionLogSapAloha($request)
    {
        $dataview = [
            'menu_id' => $request['menuid'],
        ];
        return view('reports.interfaces.aloha.transaction-log-sap-aloha-view', $dataview)->render();
    }

    public function viewHistorySendSapAloha($request)
    {
        $dataview = [
            'menu_id' => $request['menuid'],
        ];
        return view('reports.interfaces.aloha.history-send-sap-aloha-view', $dataview)->render();
    }

    // report

    // aloha

    public function reportTransactionLogSapAloha($companyId, $request)
    {
        $dataview = AlohaTransactionLog::getDataReport($companyId, $request['store'], $request['from-date'], $request['until-date'], $request['status']);
        return view('reports.interfaces.aloha.transaction-log-sap-aloha-report', $dataview)->render();
    }

    public function reportHistorySendSapAloha($companyId, $request)
    {
        $dataview = AlohaHistorySendSap::getDataReport($companyId, $request['store'], $request['from-date'], $request['until-date'], $request['status']);
        return view('reports.interfaces.aloha.history-send-sap-aloha-report', $dataview)->render();
    }

    // export

}
