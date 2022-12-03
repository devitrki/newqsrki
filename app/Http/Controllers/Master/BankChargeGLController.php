<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;

use App\Library\Helper;
use App\Models\BankChargeGl;

class BankChargeGLController extends Controller
{
    public function index(Request $request){
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('master.bank-charge-gl', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('bank_charge_gls')
                    ->join('bank_gls', 'bank_gls.id', 'bank_charge_gls.bank_gl_id')
                    ->join('special_gls', 'special_gls.id', 'bank_charge_gls.special_gl_id')
                    ->where('bank_charge_gls.company_id', $userAuth->company_id_selected)
                    ->select(
                        'bank_charge_gls.id',
                        'bank_charge_gls.bank_gl_id',
                        'bank_charge_gls.special_gl_id',
                        'bank_charge_gls.bank_charge_gl',
                        'bank_charge_gls.reference',
                        DB::raw("CONCAT(bank_gls.bank ,' - ', bank_gls.bank_gl) AS bank_gl"),
                        DB::raw("CONCAT(special_gls.payment_type ,' - ', special_gls.special_gl) AS special_gl")
                    );
        return Datatables::of($query)->addIndexColumn()->make();
    }

    public function select(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('bank_charge_gls')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(['id', 'bank_charge_gl as text']);

        if ($request->has('search')) {
            $query->whereRaw("LOWER(bank_charge_gl) like '%" . strtolower($request->search) . "%'");
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
            'bank_gl' => 'required',
            'special_gl' => 'required',
            'reference' => 'required',
            'bank_charge_gl' => 'required|max:15',
        ]);

        $userAuth = $request->get('userAuth');

        $bankChargeGl = new BankChargeGl;
        $bankChargeGl->company_id = $userAuth->company_id_selected;
        $bankChargeGl->bank_gl_id = $request->bank_gl;
        $bankChargeGl->special_gl_id = $request->special_gl;
        $bankChargeGl->bank_charge_gl = $request->bank_charge_gl;
        $bankChargeGl->reference = strtoupper($request->reference);
        if ($bankChargeGl->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("bank charge gl")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("bank charge gl")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'bank_charge_gl' => 'required|max:15',
        ]);

        $bankChargeGl = BankChargeGl::find($id);
        $bankChargeGl->bank_charge_gl = $request->bank_charge_gl;
        $bankChargeGl->reference = strtoupper($request->reference);
        if ($bankChargeGl->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("bank charge gl")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("bank charge gl")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        $bankChargeGl = BankChargeGl::find($id);
        if ($bankChargeGl->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("bank charge gl")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("bank charge gl")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }
}
