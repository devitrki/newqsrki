<?php

namespace App\Http\Controllers\Financeacc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

use App\Library\Helper;

use App\Jobs\GenerateReportFinanceacc;

use App\Models\Financeacc\AssetMutation;
use App\Models\Financeacc\AssetRequestMutation;
use App\Models\Financeacc\AssetSo;
use App\Models\Download;

class ReportFinanceaccController extends Controller
{
    public function index($menu, Request $request)
    {
        switch ($menu) {
            case 'outstanding-mutation-asset':
                return $this->viewOutstandingMutationAsset($request->all());
                break;
            case 'log-mutation-asset':
                return $this->viewLogMutationAsset($request->all());
                break;
            case 'asset-so':
                return $this->viewAssetSo($request->all());
                break;
            case 'selisih-asset-so':
                return $this->viewSelisihAssetSo($request->all());
                break;
            case 'outstanding-request-mutation':
                return $this->viewOutstandingRequestMutation($request->all());
                break;
            case 'log-request-mutation':
                return $this->viewLogRequestMutation($request->all());
                break;
        }
    }

    public function report($menu, Request $request)
    {
        $userAuth = $request->get('userAuth');

        switch ($menu) {
            case 'outstanding-mutation-asset':
                return $this->reportOutstandingMutationAsset($userAuth->company_id_selected, $request->all());
                break;
            case 'log-mutation-asset':
                return $this->reportLogMutationAsset($userAuth->company_id_selected, $request->all());
                break;
            case 'asset-so':
                return $this->reportAssetSo($userAuth->company_id_selected, $request->all());
                break;
            case 'selisih-asset-so':
                return $this->reportSelisihAssetSo($userAuth->company_id_selected, $request->all());
                break;
            case 'outstanding-request-mutation':
                return $this->reportOutstandingRequestMutation($request->all());
                break;
            case 'log-request-mutation':
                return $this->reportLogRequestMutation($request->all());
                break;
        }
    }

    public function export($menu, Request $request)
    {
        $userAuth = $request->get('userAuth');

        switch ($menu) {
            case 'outstanding-mutation-asset':
                return $this->exportOutstandingMutationAsset($userAuth->company_id_selected, $request);
                break;
            case 'log-mutation-asset':
                return $this->exportLogMutationAsset($userAuth->company_id_selected, $request);
                break;
            case 'asset-so':
                return $this->exportAssetSo($userAuth->company_id_selected, $request);
                break;
            case 'selisih-asset-so':
                return $this->exportSelisihAssetSo($userAuth->company_id_selected, $request);
                break;
            case 'outstanding-request-mutation':
                return $this->exportOutstandingRequestMutation($userAuth->company_id_selected, $request);
                break;
            case 'log-request-mutation':
                return $this->exportLogRequestMutation($userAuth->company_id_selected, $request);
                break;
        }
    }

    // view
    public function viewOutstandingMutationAsset($request)
    {
        $dataview = [
            'menu_id' => $request['menuid'],
        ];
        return view('reports.financeacc.asset.outstanding-mutation-asset-view', $dataview)->render();
    }

    public function viewLogMutationAsset($request)
    {
        $dataview = [
            'menu_id' => $request['menuid'],
        ];
        return view('reports.financeacc.asset.log-mutation-asset-view', $dataview)->render();
    }

    public function viewAssetSo($request)
    {
        $dataview = [
            'menu_id' => $request['menuid'],
        ];
        return view('reports.financeacc.asset.asset-so-view', $dataview)->render();
    }

    public function viewSelisihAssetSo($request)
    {
        $dataview = [
            'menu_id' => $request['menuid'],
        ];
        return view('reports.financeacc.asset.selisih-asset-so-view', $dataview)->render();
    }

    public function viewOutstandingRequestMutation($request)
    {
        $dataview = [
            'menu_id' => $request['menuid'],
        ];
        return view('reports.financeacc.asset.outstanding-request-mutation-view', $dataview)->render();
    }

    public function viewLogRequestMutation($request)
    {
        $dataview = [
            'menu_id' => $request['menuid'],
        ];
        return view('reports.financeacc.asset.log-request-mutation-view', $dataview)->render();
    }

    // report
    public function reportOutstandingMutationAsset($companyId, $request)
    {
        $dataview = AssetMutation::getDataOutstandingReport($companyId, $request['plant-id'], Auth::id());
        return view('reports.financeacc.asset.outstanding-mutation-asset-report', $dataview)->render();
    }

    public function reportLogMutationAsset($companyId, $request)
    {
        $dataview = AssetMutation::getDataLogReport($companyId, $request['plant-id'], Auth::id(), $request['from-date'], $request['until-date']);
        return view('reports.financeacc.asset.log-mutation-asset-report', $dataview)->render();
    }

    public function reportAssetSo($companyId, $request)
    {
        $dataview = AssetSo::getDataAssetSoReport($companyId, $request['plant-id'], $request['costcenter'], $request['periode']);
        return view('reports.financeacc.asset.asset-so-report', $dataview)->render();
    }

    public function reportSelisihAssetSo($companyId, $request)
    {
        $dataview = AssetSo::getDataSelisihAssetSoReport($companyId, $request['plant-id'], $request['periode'], Auth::id());
        return view('reports.financeacc.asset.selisih-asset-so-report', $dataview)->render();
    }

    public function reportOutstandingRequestMutation($request)
    {
        $dataview = AssetRequestMutation::getDataOutstandingReport($request['plant-id'], Auth::id());
        return view('reports.financeacc.asset.outstanding-request-mutation-report', $dataview)->render();
    }

    public function reportLogRequestMutation($request)
    {
        $dataview = AssetRequestMutation::getDataLogReport($request['plant-id'], Auth::id(), $request['from-date'], $request['until-date']);
        return view('reports.financeacc.asset.log-request-mutation-report', $dataview)->render();
    }

    // export
    public function exportOutstandingMutationAsset($companyId, $request)
    {
        $request->validate([
            'plant' => 'required',
        ]);

        $param = [
            'company_id' => $companyId,
            'plant' => $request->plant,
            'user_id' => Auth::id()
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
        $download->name = 'Outstanding Asset Transfer';
        $download->module = 'Finance Accounting';
        $download->type = 'outstanding-mutation-asset';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportFinanceacc::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("Outstanding Asset Transfer")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("Outstanding Asset Transfer")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportLogMutationAsset($companyId, $request)
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
        $download->name = 'Log Asset Transfer';
        $download->module = 'Finance Accounting';
        $download->type = 'log-mutation-asset';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportFinanceacc::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("Log Asset Transfer")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("Log Asset Transfer")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportAssetSo($companyId, $request)
    {
        $request->validate([
            'plant' => 'required',
            'cost_center' => 'required',
            'periode' => 'required',
        ]);

        $param = [
            'company_id' => $companyId,
            'plant' => $request->plant,
            'costcenter' => $request->cost_center,
            'periode' => $request->periode
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
        $download->name = 'Asset SO';
        $download->module = 'Finance Accounting';
        $download->type = 'asset-so';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportFinanceacc::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("Asset SO")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("Asset SO")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportSelisihAssetSo($companyId, $request)
    {
        $request->validate([
            'plant' => 'required',
            'periode' => 'required',
        ]);

        $param = [
            'company_id' => $companyId,
            'plant' => $request->plant,
            'periode' => $request->periode,
            'user_id' => Auth::id()
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
        $download->name = 'Selisih Asset SO';
        $download->module = 'Finance Accounting';
        $download->type = 'selisih-asset-so';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportFinanceacc::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("Selisih Asset SO")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("Selisih Asset SO")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportOutstandingRequestMutation($companyId, $request)
    {
        $request->validate([
            'plant' => 'required',
        ]);

        $param = [
            'plant' => $request->plant,
            'user_id' => Auth::id()
        ];

        // insert to downloads
        $download = new Download;
        $download->company_id = $companyId;
        $download->name = 'Outstanding Request Mutation';
        $download->module = 'Finance Accounting';
        $download->type = 'outstanding-request-mutation';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportFinanceacc::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("Outstanding Request Mutation")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("Outstanding Request Mutation")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function exportLogRequestMutation($companyId, $request)
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
        $download->company_id = $companyId;
        $download->name = 'Log Request Mutation';
        $download->module = 'Finance Accounting';
        $download->type = 'log-request-mutation';
        $download->param = json_encode($param);
        $download->filetype = strtolower($request->type);
        $download->user_id = Auth::id();

        $success = false;

        if ($download->save()) {
            if (GenerateReportFinanceacc::dispatch($download->id)->onQueue('high')) {
                $success = true;
            } else {
                $success = false;
            }
        }

        if ($success) {
            $stat = 'success';
            $msg = Lang::get("message.export.success", ["data" => Lang::get("Log Request Mutation")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.export.failed", ["data" => Lang::get("Log Request Mutation")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }
}
