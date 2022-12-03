<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\SpecialGl;

class SpecialGlController extends Controller
{
    public function index(Request $request){
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('master.special-gl', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('special_gls')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(
                        'id',
                        'special_gl',
                        'payment_type',
                        'reference',
                        'sap_code'
                    );
        return Datatables::of($query)->addIndexColumn()->make();
    }

    public function select(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('special_gls')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(DB::raw("id, CONCAT(payment_type ,' - ', special_gl) AS text"));

        if ($request->has('search')) {
            $query = $query->where(function($query) use ($request){
                $query->whereRaw("LOWER(payment_type) like '%" . strtolower($request->search) . "%'");
                $query->orWhereRaw("LOWER(special_gl) like '%" . strtolower($request->search) . "%'");
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
            'special_gl' => 'required|unique:special_gls,special_gl|max:1',
            'payment_type' => 'required',
            'reference' => 'required',
            'sap_code' => 'required|max:2'
        ]);

        $userAuth = $request->get('userAuth');

        $specialGl = new SpecialGl;
        $specialGl->company_id = $userAuth->company_id_selected;
        $specialGl->special_gl = strtoupper($request->special_gl);
        $specialGl->payment_type = $request->payment_type;
        $specialGl->reference = $request->reference;
        $specialGl->sap_code = $request->sap_code;
        if ($specialGl->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("special gl")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("special gl")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'special_gl' => 'required',
            'payment_type' => 'required',
            'reference' => 'required',
            'sap_code' => 'required|max:2'
        ]);

        $specialGl = SpecialGl::find($id);
        $specialGl->special_gl = $request->special_gl;
        $specialGl->payment_type = $request->payment_type;
        $specialGl->reference = $request->reference;
        $specialGl->sap_code = $request->sap_code;
        if ($specialGl->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("special gl")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("special gl")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        $specialGl = SpecialGl::find($id);
        if ($specialGl->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("special gl")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("special gl")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }
}
