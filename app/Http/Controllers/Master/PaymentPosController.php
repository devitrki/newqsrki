<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\PaymentPos;
use App\Models\Plant;

class PaymentPosController extends Controller
{
    public function index(Request $request)
    {
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('master.payment-pos', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('payment_pos')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(['id', 'method_payment_name', 'title', 'range_tender', 'sort_order']);

        return Datatables::of($query)
                            ->addIndexColumn()
                            ->make();
    }

    public function store(Request $request)
    {
        $request->validate([
            'method_payment_name' => 'required|unique:payment_pos,method_payment_name',
            'title' => 'required',
            'sort_order' => 'required',
        ]);

        $userAuth = $request->get('userAuth');

        $paymentPos = new PaymentPos();
        $paymentPos->company_id = $userAuth->company_id_selected;
        $paymentPos->method_payment_name = $request->method_payment_name;
        $paymentPos->title = $request->title;
        $paymentPos->range_tender = $request->range_tender;
        $paymentPos->sort_order = $request->sort_order;
        if ($paymentPos->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("payment pos")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("payment pos")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'method_payment_name' => 'required',
            'sort_order' => 'required',
            'title' => 'required',
        ]);

        $paymentPos = PaymentPos::find($request->id);
        $paymentPos->method_payment_name = $request->method_payment_name;
        $paymentPos->title = $request->title;
        $paymentPos->range_tender = $request->range_tender;
        $paymentPos->sort_order = $request->sort_order;
        if ($paymentPos->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("payment pos")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("payment pos")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function destroy($id)
    {
        $paymentPos = PaymentPos::find($id);
        if ($paymentPos->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("payment pos")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("payment pos")]);
        }
        return response()->json(Helper::resJSON($stat, $msg));
    }

}
