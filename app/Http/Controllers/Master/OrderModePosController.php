<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\OrderModePos;

class OrderModePosController extends Controller
{
    public function index(Request $request)
    {
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('master.order-mode-pos', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('order_mode_pos')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(['id', 'order_mode_id', 'order_mode_name', 'sap_name']);

        return Datatables::of($query)
                            ->addIndexColumn()
                            ->make();
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_mode_id' => 'required|unique:order_mode_pos,order_mode_id',
            'order_mode_name' => 'required',
            'sap_name' => 'required',
        ]);

        $userAuth = $request->get('userAuth');

        $orderModePos = new OrderModePos;
        $orderModePos->company_id = $userAuth->company_id_selected;
        $orderModePos->order_mode_id = $request->order_mode_id;
        $orderModePos->order_mode_name = strtoupper($request->order_mode_name);
        $orderModePos->sap_name = strtoupper($request->sap_name);
        if ($orderModePos->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("order mode pos")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("order mode pos")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'order_mode_id' => 'required',
            'order_mode_name' => 'required',
            'sap_name' => 'required',
        ]);

        $orderModePos = OrderModePos::find($request->id);
        $orderModePos->order_mode_id = $request->order_mode_id;
        $orderModePos->order_mode_name = strtoupper($request->order_mode_name);
        $orderModePos->sap_name = strtoupper($request->sap_name);
        if ($orderModePos->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("order mode pos")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("order mode pos")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function destroy($id)
    {
        $orderModePos = OrderModePos::find($id);
        if ($orderModePos->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("order mode pos")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("order mode pos")]);
        }
        return response()->json(Helper::resJSON($stat, $msg));
    }
}
