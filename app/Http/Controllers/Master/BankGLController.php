<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\BankGl;

class BankGLController extends Controller
{
    public function index(Request $request){
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('master.bank-gl', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('bank_gls')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(
                        'id',
                        'bank',
                        'bank_gl'
                    );
        return Datatables::of($query)->addIndexColumn()->make();
    }

    public function select(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('bank_gls')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(DB::raw("id, CONCAT(bank ,' - ', bank_gl) AS text"));

        if ($request->has('search')) {
            $query = $query->where(function($query) use ($request){
                $query->whereRaw("LOWER(bank) like '%" . strtolower($request->search) . "%'");
                $query->orWhereRaw("LOWER(bank_gl) like '%" . strtolower($request->search) . "%'");
            });
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
            'bank' => 'required|unique:bank_gls,bank',
            'bank_gl' => 'required|max:15',
        ]);

        $userAuth = $request->get('userAuth');

        $bankGl = new BankGl;
        $bankGl->company_id = $userAuth->company_id_selected;
        $bankGl->bank_gl = $request->bank_gl;
        $bankGl->bank = $request->bank;
        if ($bankGl->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("bank gl")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("bank gl")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'bank_gl' => 'required|max:15'
        ]);

        $bankGl = BankGl::find($id);
        $bankGl->bank_gl = $request->bank_gl;
        if ($bankGl->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("bank gl")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("bank gl")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        $bankGl = BankGl::find($id);
        if ($bankGl->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("bank gl")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("bank gl")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }
}
