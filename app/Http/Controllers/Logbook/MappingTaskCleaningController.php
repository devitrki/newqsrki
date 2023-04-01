<?php

namespace App\Http\Controllers\Logbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Logbook\LbTaskClean;

class MappingTaskCleaningController extends Controller
{
    public function index(Request $request){
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('logbook.mapping-task-cleaning', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('lb_task_cleans')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select('id', 'task', 'section', 'frekuensi', 'status');

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
                        'section' => 'required',
                        'frekuensi' => 'required',
                        'status' => 'required'
                    ]);

        $userAuth = $request->get('userAuth');

        $lbTaskClean = new LbTaskClean;
        $lbTaskClean->company_id = $userAuth->company_id_selected;
        $lbTaskClean->task = $request->task;
        $lbTaskClean->section = $request->section;
        $lbTaskClean->frekuensi = $request->frekuensi;
        $lbTaskClean->status = $request->status;
        if ($lbTaskClean->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("mapping task cleaning & sanitation")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("mapping task cleaning & sanitation")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                        'task' => 'required',
                        'section' => 'required',
                        'frekuensi' => 'required',
                        'status' => 'required'
                    ]);

        $lbTaskClean = LbTaskClean::find($request->id);
        $lbTaskClean->task = $request->task;
        $lbTaskClean->section = $request->section;
        $lbTaskClean->frekuensi = $request->frekuensi;
        $lbTaskClean->status = $request->status;
        if ($lbTaskClean->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("mapping task cleaning & sanitation")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("mapping task cleaning & sanitation")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        if( Helper::used( $id, 'lb_task_clean_id', ['lb_dly_cleans'] ) ){
            return response()->json( Helper::resJSON( 'failed', Lang::get('validation.used') ) );
        }

        $lbTaskClean = LbTaskClean::find($id);

        if ($lbTaskClean->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("mapping task cleaning & sanitation")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("mapping task cleaning & sanitation")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }
}
