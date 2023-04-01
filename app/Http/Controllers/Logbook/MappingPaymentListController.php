<?php

namespace App\Http\Controllers\Logbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Logbook\LbPaymentList;

class MappingPaymentListController extends Controller
{
    public function index(Request $request){
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('logbook.mapping-payment-list', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('lb_payment_lists')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select('id', 'payment', 'status');

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
                        'payment' => 'required',
                        'status' => 'required'
                    ]);

        $userAuth = $request->get('userAuth');

        $lbPaymentList = new LbPaymentList;
        $lbPaymentList->company_id = $userAuth->company_id_selected;
        $lbPaymentList->payment = $request->payment;
        $lbPaymentList->status = $request->status;
        if ($lbPaymentList->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("mapping payment list")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("mapping payment list")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                        'payment' => 'required',
                        'status' => 'required'
                    ]);

        $lbPaymentList = LbPaymentList::find($request->id);
        $lbPaymentList->payment = $request->payment;
        $lbPaymentList->status = $request->status;
        if ($lbPaymentList->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("mapping payment list")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("mapping payment list")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        // if( Helper::used( $id, 'lb_task_clean_id', ['lb_dly_cleans'] ) ){
        //     return response()->json( Helper::resJSON( 'failed', Lang::get('validation.used') ) );
        // }

        $lbPaymentList = LbPaymentList::find($id);

        if ($lbPaymentList->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("mapping payment list")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("mapping payment list")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }
}
