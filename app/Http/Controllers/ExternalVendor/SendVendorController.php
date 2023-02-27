<?php

namespace App\Http\Controllers\ExternalVendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Jobs\ExternalVendor\SendTransactionVendor;

use App\Models\ExternalVendor\SendVendor;
use App\Models\ExternalVendor\TargetVendor;

class SendVendorController extends Controller
{
    public function index(Request $request)
    {
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('externalVendors.send-vendor', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('send_vendors')
                    ->leftJoin('plants', 'plants.id', '=', 'send_vendors.plant_id')
                    ->leftJoin('template_sales', 'template_sales.id', '=', 'send_vendors.template_sale_id')
                    ->leftJoin('target_vendors', 'target_vendors.id', '=', 'send_vendors.target_vendor_id')
                    ->where('send_vendors.company_id', $userAuth->company_id_selected)
                    ->select([
                        'send_vendors.id',
                        'send_vendors.plant_id',
                        'send_vendors.template_sale_id',
                        'send_vendors.target_vendor_id',
                        'send_vendors.prefix_name_store',
                        'send_vendors.status',
                        'template_sales.name as template_sales',
                        'target_vendors.name as target_vendor',
                        'plants.initital',
                        'plants.short_name',
                        'plants.description',

                    ]);

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
            'template_sales' => 'required',
            'target_vendor' => 'required',
            'plant' => 'required',
            'prefix_name_store' => 'required',
            'status' => 'required'
        ]);

        $userAuth = $request->get('userAuth');

        $sendVendor = new SendVendor;
        $sendVendor->company_id = $userAuth->company_id_selected;
        $sendVendor->template_sale_id = $request->template_sales;
        $sendVendor->target_vendor_id = $request->target_vendor;
        $sendVendor->plant_id = $request->plant;
        $sendVendor->prefix_name_store = $request->prefix_name_store;
        $sendVendor->status = $request->status;
        if ($sendVendor->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("send vendor")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("send vendor")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'template_sales' => 'required',
            'target_vendor' => 'required',
            'plant' => 'required',
            'prefix_name_store' => 'required',
            'status' => 'required'
        ]);

        $sendVendor = SendVendor::find($request->id);
        $sendVendor->template_sale_id = $request->template_sales;
        $sendVendor->target_vendor_id = $request->target_vendor;
        $sendVendor->plant_id = $request->plant;
        $sendVendor->prefix_name_store = $request->prefix_name_store;
        $sendVendor->status = $request->status;
        if ($sendVendor->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("send vendor")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("send vendor")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function destroy($id)
    {
        $sendVendor = SendVendor::find($id);
        if ($sendVendor->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("send vendor")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("send vendor")]);
        }
        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function download(Request $request)
    {
        $id = $request->query('id');
        $date = $request->query('date');
        $fileType = $request->query('file-type');

        $result = SendVendor::downloadFileSales($id, $date, $fileType);

        if ($result['status']) {

            return response()->download($result['data']['file'], $result['data']['fileName']);

        } else{
            echo $result['message'];
        }
    }

    public function send(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'date' => 'required',
        ]);

        if (SendTransactionVendor::dispatch($request->date, $request->id)) {
            $stat = 'success';
            $msg = Lang::get("Send manual successfully entry in the queue, please check the report history in a few minutes");
        } else {
            $stat = 'failed';
            $msg = Lang::get("Send manual failed");
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function receive($targetVendorId, $dateFrom, $dateUntil)
    {
        $host = TargetVendor::getConfigByKey($targetVendorId, 'HOST');
        $authenticationKey = TargetVendor::getConfigByKey($targetVendorId, 'AUTHENTICATION_KEY');

        if (!$host) {
            !dd('Setting config HOST first.');
        }

        if (!$authenticationKey) {
            !dd('Setting config AUTHENTICATION_KEY first.');
        }

        $headers = [
            'Content-type' => 'text/json',
            'Authorization' => 'Basic ' . $authenticationKey
        ];

        $url = $host . '/POS/POSService.svc/GetReceipts/' . $dateFrom . '/' . $dateUntil;

        $res = Http::withHeaders($headers)
                ->get($url);

        !dd([
            $res->status(),
            $res->json()
        ]);
    }

    public function clearTest($targetVendorId)
    {
        $host = TargetVendor::getConfigByKey($targetVendorId, 'HOST');
        $authenticationKey = TargetVendor::getConfigByKey($targetVendorId, 'AUTHENTICATION_KEY');

        if (!$host) {
            !dd('Setting config HOST first.');
        }

        if (!$authenticationKey) {
            !dd('Setting config AUTHENTICATION_KEY first.');
        }

        $headers = [
            'Content-type' => 'text/json',
            'Authorization' => 'Basic ' . $authenticationKey
        ];

        $url = $host . '/POS/POSService.svc/ClearTestData';

        $res = Http::withHeaders($headers)
                ->get($url);

        !dd([
            $res->status(),
            $res->json()
        ]);
    }
}
