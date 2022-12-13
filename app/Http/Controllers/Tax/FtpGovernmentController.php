<?php

namespace App\Http\Controllers\Tax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

use App\Library\Helper;

use App\Models\Tax\FtpGovernment;

class FtpGovernmentController extends Controller
{
    public function index(Request $request)
    {
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('tax.ftp-government', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('ftp_governments')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(['id', 'name', 'transfer_type', 'host', 'username', 'password', 'port']);

        return Datatables::of($query)
                ->addIndexColumn()
                ->make();
    }

    public function select(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $data = DB::table('ftp_governments')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(['id', 'name as text'])->get();

        if ($request->has('ext')) {
            if ($request->query('ext') == 'all') {
                $data->prepend(['id' => 0, 'text' => Lang::get('All')]);
            }
        }

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:ftp_governments,name',
            'transfer_type' => 'required',
            'host' => 'required',
            'username' => 'required',
            'password' => 'required',
            'port' => 'required'
        ]);

        $userAuth = $request->get('userAuth');

        $ftpGovernment = new FtpGovernment;
        $ftpGovernment->company_id = $userAuth->company_id_selected;
        $ftpGovernment->name = $request->name;
        $ftpGovernment->transfer_type = $request->transfer_type;
        $ftpGovernment->host = $request->host;
        $ftpGovernment->username = $request->username;
        $ftpGovernment->password = $request->password;
        $ftpGovernment->port = $request->port;
        if ($ftpGovernment->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("ftp government")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("ftp government")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'transfer_type' => 'required',
            'host' => 'required',
            'username' => 'required',
            'password' => 'required',
            'port' => 'required'
        ]);

        $ftpGovernment = FtpGovernment::find($request->id);
        $ftpGovernment->name = $request->name;
        $ftpGovernment->transfer_type = $request->transfer_type;
        $ftpGovernment->host = $request->host;
        $ftpGovernment->username = $request->username;
        $ftpGovernment->password = $request->password;
        $ftpGovernment->port = $request->port;
        if ($ftpGovernment->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("ftp government")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("ftp government")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function destroy($id)
    {
        if (Helper::used($id, 'ftp_government_id', ['send_taxes'])) {
            return response()->json(Helper::resJSON('failed', Lang::get('validation.used')));
        }

        $ftpGovernment = FtpGovernment::find($id);
        if ($ftpGovernment->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("ftp government")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("ftp government")]);
        }
        return response()->json(Helper::resJSON($stat, $msg));
    }
}
