<?php

namespace App\Http\Controllers\Logbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Logbook\LbDutKitchen;

class MappingDutiesKitchenController extends Controller
{
    public function index(Request $request){
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('logbook.mapping-duties-kitchen', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('lb_dut_kitchens')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select('id', 'task', 'status');

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
                        'status' => 'required'
                    ]);

        $userAuth = $request->get('userAuth');

        $lbDutKitchen = new LbDutKitchen;
        $lbDutKitchen->company_id = $userAuth->company_id_selected;
        $lbDutKitchen->task = $request->task;
        $lbDutKitchen->status = $request->status;
        if ($lbDutKitchen->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("mapping task daily duties kitchen")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("mapping task daily duties kitchen")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                        'task' => 'required',
                        'status' => 'required'
                    ]);

        $lbDutKitchen = LbDutKitchen::find($request->id);
        $lbDutKitchen->task = $request->task;
        $lbDutKitchen->status = $request->status;
        if ($lbDutKitchen->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("mapping task daily duties kitchen")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("mapping task daily duties kitchen")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        // if( Helper::used( $id, 'lb_task_clean_id', ['lb_dly_cleans'] ) ){
        //     return response()->json( Helper::resJSON( 'failed', Lang::get('validation.used') ) );
        // }

        $lbDutKitchen = LbDutKitchen::find($id);

        if ($lbDutKitchen->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("mapping task daily duties kitchen")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("mapping task daily duties kitchen")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }
}
