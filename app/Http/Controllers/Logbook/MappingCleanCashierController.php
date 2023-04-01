<?php

namespace App\Http\Controllers\Logbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Logbook\LbCleanCashier;

class MappingCleanCashierController extends Controller
{
    public function index(Request $request){
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('logbook.mapping-clean-cashier', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('lb_clean_cashiers')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select('id', 'task', 'frekuensi', 'day', 'status');

        return Datatables::of($query)
                        ->addIndexColumn()
                        ->addColumn('status_desc', function ($data) {
                            if ($data->status != 0) {
                                return Lang::get('Active');
                            } else {
                                return Lang::get('Unactive');
                            }
                        })
                        ->make();
    }

    public function store(Request $request)
    {
        $request->validate([
                        'task' => 'required',
                        'frekuensi' => 'required',
                        'status' => 'required'
                    ]);

        $userAuth = $request->get('userAuth');

        $lbCleanCashier = new LbCleanCashier;
        $lbCleanCashier->company_id = $userAuth->company_id_selected;
        $lbCleanCashier->task = $request->task;
        $lbCleanCashier->frekuensi = $request->frekuensi;
        $lbCleanCashier->day = $request->day;
        $lbCleanCashier->status = $request->status;
        if ($lbCleanCashier->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("mapping task cleaning duties cashier")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("mapping task cleaning duties cashier")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                        'task' => 'required',
                        'frekuensi' => 'required',
                        'status' => 'required'
                    ]);

        $lbCleanCashier = LbCleanCashier::find($request->id);
        $lbCleanCashier->task = $request->task;
        $lbCleanCashier->frekuensi = $request->frekuensi;
        $lbCleanCashier->day = $request->day;
        $lbCleanCashier->status = $request->status;
        if ($lbCleanCashier->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("mapping task cleaning duties cashier")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("mapping task cleaning duties cashier")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        // if( Helper::used( $id, 'lb_task_clean_id', ['lb_dly_cleans'] ) ){
        //     return response()->json( Helper::resJSON( 'failed', Lang::get('validation.used') ) );
        // }

        $lbCleanCashier = LbCleanCashier::find($id);

        if ($lbCleanCashier->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("mapping task cleaning duties cashier")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("mapping task cleaning duties cashier")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }
}
