<?php

namespace App\Http\Controllers\Tax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Jobs\Tax\SendTaxFtp;

use App\Models\Tax\SendTax;

class SendTaxController extends Controller
{
    public function index(Request $request)
    {
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('tax.send-tax', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('send_taxes')
                    ->leftJoin('ftp_governments', 'ftp_governments.id', '=', 'send_taxes.ftp_government_id')
                    ->leftJoin('plants', 'plants.id', '=', 'send_taxes.plant_id')
                    ->where('send_taxes.company_id', $userAuth->company_id_selected)
                    ->select(['send_taxes.id','send_taxes.ftp_government_id', 'send_taxes.plant_id', 'ftp_governments.name', 'plants.initital', 'plants.short_name', 'plants.description', 'send_taxes.prefix_name_store', 'send_taxes.status']);

        return Datatables::of($query)
                ->addIndexColumn()
                ->addColumn('status_desc', function ($data) {
                    if ($data->status == 0) {
                        return "Not Active";
                    } else {
                        return "Active";
                    }
                })
                ->make();
    }

    public function store(Request $request)
    {
        $request->validate([
            'ftp_government' => 'required',
            'plant' => 'required',
            'prefix_name_store' => 'required',
            'status' => 'required'
        ]);

        $userAuth = $request->get('userAuth');

        $sendTax = new SendTax;
        $sendTax->company_id = $userAuth->company_id_selected;
        $sendTax->ftp_government_id = $request->ftp_government;
        $sendTax->plant_id = $request->plant;
        $sendTax->prefix_name_store = $request->prefix_name_store;
        $sendTax->status = $request->status;
        if ($sendTax->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("send tax")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("send tax")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function send(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'date' => 'required',
        ]);

        if (SendTaxFtp::dispatch($request->date, $request->id)) {
            $stat = 'success';
            $msg = Lang::get("Send manual successfully entry in the queue, please check the report history in a few minutes");
        } else {
            $stat = 'failed';
            $msg = Lang::get("Send manual failed");
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function download(Request $request)
    {
        $id = $request->query('id');
        $date = $request->query('date');
        $fileType = $request->query('file-type');

        $result = SendTax::downloadFileSales($id, $date, $fileType);

        if ($result['status']) {

            return response()->download($result['data']['file'], $result['data']['fileName']);

        } else{
            echo $result['message'];
        }

    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'ftp_government' => 'required',
            'plant' => 'required',
            'prefix_name_store' => 'required',
            'status' => 'required'
        ]);

        $sendTax = SendTax::find($request->id);
        $sendTax->ftp_government_id = $request->ftp_government;
        $sendTax->plant_id = $request->plant;
        $sendTax->prefix_name_store = $request->prefix_name_store;
        $sendTax->status = $request->status;
        if ($sendTax->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("send tax")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("send tax")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function destroy($id)
    {
        $sendTax = SendTax::find($id);
        if ($sendTax->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("send tax")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("send tax")]);
        }
        return response()->json(Helper::resJSON($stat, $msg));
    }
}
