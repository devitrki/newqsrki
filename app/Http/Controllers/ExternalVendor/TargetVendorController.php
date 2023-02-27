<?php

namespace App\Http\Controllers\ExternalVendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\DataTables;

use App\Library\Helper;
use App\Models\ExternalVendor\TargetVendor;

class TargetVendorController extends Controller
{
    public function index(Request $request){
        $transferTypes = TargetVendor::getTransferTypes();
        $transferTypeOptions = [];
        foreach ($transferTypes as $k => $v) {
            $transferTypeOptions[] = [
                'id' => $k,
                'text' => $v
            ];
        }

        $dataview = [
            'menu_id' => $request->query('menuid'),
            'transfer_type_options' => $transferTypeOptions
        ];
        return view('externalVendors.target-vendor', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('target_vendors')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(['id', 'name', 'transfer_type']);

        return Datatables::of($query)
                ->addIndexColumn()
                ->addColumn('transfer_type_desc', function ($data) {
                    $transferTypes = TargetVendor::getTransferTypes();
                    return $transferTypes[$data->transfer_type];
                })
                ->make();
    }

    public function select(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('target_vendors')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(['id', 'name as text']);

        if ($request->has('search')) {
            $query->whereRaw("LOWER(name) like '%" . strtolower($request->search) . "%'");
        }

        if ($request->has('limit')) {
            $query->limit($request->limit);
        }

        if ($request->query('init') == 'false' && !$request->has('search')) {
            $data = [];
        } else {
            $data = $query->get();
        }

        if ($request->has('ext')) {
            if ($request->query('ext') == 'all') {
                if (!is_array($data)) {
                    $data->prepend(['id' => 0, 'text' => Lang::get('All')]);
                }
            }
        }

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $request->validate([
                        'name' => 'required|unique:target_vendors,name',
                        'transfer_type' => 'required',
                    ]);
        $userAuth = $request->get('userAuth');

        $targetVendor = new TargetVendor;
        $targetVendor->company_id = $userAuth->company_id_selected;
        $targetVendor->name = $request->name;
        $targetVendor->transfer_type = $request->transfer_type;
        if ($targetVendor->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("target vendor")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("target vendor")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                        'name' => 'required',
                        'transfer_type' => 'required',
                    ]);

        $targetVendor = TargetVendor::find($request->id);
        $targetVendor->name = $request->name;
        $targetVendor->transfer_type = $request->transfer_type;
        if ($targetVendor->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("target vendor")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("target vendor")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        // if( Helper::used( $id, 'target_vendor_id', [''] ) ){
        //     return response()->json( Helper::resJSON( 'failed', Lang::get('validation.used') ) );
        // }

        $targetVendor = TargetVendor::find($id);
        if ($targetVendor->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("target vendor")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("target vendor")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    // configuration
    public function dtbleConf($id)
    {
        $targetVendors = DB::table('target_vendors')
                            ->where('id', $id)
                            ->select(
                                'configurations'
                            )
                            ->first();

        $configurations = [];

        if ($targetVendors->configurations) {
            $configurationJsons = json_decode($targetVendors->configurations, true);
            foreach ($configurationJsons as $key => $value) {
                $configuration = [
                    'key' => $key,
                    'value' => $value
                ];
                array_push($configurations, $configuration);
            }
        }

        $configurations = collect($configurations);

        return Datatables::of($configurations)->addIndexColumn()->toJson();
    }

    public function storeConf(Request $request, $id)
    {
        $request->validate([
            'key' => 'required',
            'value' => 'required'
        ]);

        $targetVendor = TargetVendor::find($id);

        $configurations = [];
        if ($targetVendor->configurations) {
            $configurations = json_decode($targetVendor->configurations, true);
        }
        $configurations[$request->key] = $request->value;
        $configurationJson = json_encode($configurations);

        $targetVendor->configurations = $configurationJson;
        if ($targetVendor->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("target vendor configuration")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("target vendor configuration")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroyConf(Request $request, $id)
    {
        $targetVendor = TargetVendor::find($id);

        $configurations = json_decode($targetVendor->configurations, true);
        unset($configurations[$request->key]);

        $configurationJson = json_encode($configurations);
        $targetVendor->configurations = $configurationJson;
        if ($targetVendor->save()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("target vendor configuration")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("target vendor configuration")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }
}
