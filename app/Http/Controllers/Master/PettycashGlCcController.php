<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\PettycashCcGl;

class PettycashGlCcController extends Controller
{
    public function index(Request $request){
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('master.pettycash-glcc', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('pettycash_cc_gls')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(['id', 'gl', 'cc', 'privilege']);

        return Datatables::of($query)
                    ->addIndexColumn()
                    ->addColumn('privilege_desc', function ($data) {

                        if ($data->privilege == '0') {
                            return 'All';
                        }

                        if ($data->privilege == '1') {
                            return 'Outlet';
                        }

                        if ($data->privilege == '2') {
                            return 'DC';
                        }

                        return '';
                    })
                    ->make();
    }

    public function store(Request $request)
    {
        $request->validate([
                        'gl' => 'required',
                        'cc' => 'required',
                        'privilege' => 'required',
                    ]);

        $userAuth = $request->get('userAuth');

        $pettycashCcGl = new PettycashCcGl;
        $pettycashCcGl->company_id = $userAuth->company_id_selected;
        $pettycashCcGl->gl = $request->gl;
        $pettycashCcGl->cc = $request->cc;
        $pettycashCcGl->privilege = $request->privilege;
        if ($pettycashCcGl->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("petty cash gl cc")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("petty cash gl cc")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                        'gl' => 'required',
                        'cc' => 'required',
                        'privilege' => 'required',
                    ]);

        $pettycashCcGl = PettycashCcGl::find($request->id);
        $pettycashCcGl->gl = $request->gl;
        $pettycashCcGl->cc = $request->cc;
        $pettycashCcGl->privilege = $request->privilege;
        if ($pettycashCcGl->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("petty cash gl cc")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("petty cash gl cc")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        $pettycashCcGl = PettycashCcGl::find($id);
        if ($pettycashCcGl->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("petty cash gl cc")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("petty cash gl cc")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }
}
