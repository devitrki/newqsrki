<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\UomConvert;

class UomConvertController extends Controller
{
    public function index(Request $request)
    {
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('master.uom-convert', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('uom_converts')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(['id', 'base_uom', 'send_sap_uom']);

        return Datatables::of($query)
                            ->addIndexColumn()
                            ->make();
    }

    public function store(Request $request)
    {
        $request->validate([
            'base_uom' => 'required|unique:uom_converts,base_uom',
            'send_sap_uom' => 'required',
        ]);

        $userAuth = $request->get('userAuth');

        $uomConvert = new UomConvert;
        $uomConvert->company_id = $userAuth->company_id_selected;
        $uomConvert->base_uom = strtoupper($request->base_uom);
        $uomConvert->send_sap_uom = strtoupper($request->send_sap_uom);
        if ($uomConvert->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("uom convert")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("uom convert")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'base_uom' => 'required',
            'send_sap_uom' => 'required',
        ]);

        $uomConvert = UomConvert::find($request->id);
        $uomConvert->base_uom = strtoupper($request->base_uom);
        $uomConvert->send_sap_uom = strtoupper($request->send_sap_uom);
        if ($uomConvert->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("uom convert")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("uom convert")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function destroy($id)
    {
        $uomConvert = UomConvert::find($id);
        if ($uomConvert->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("uom convert")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("uom convert")]);
        }
        return response()->json(Helper::resJSON($stat, $msg));
    }
}
