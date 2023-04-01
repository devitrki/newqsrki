<?php

namespace App\Http\Controllers\Logbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Logbook\LbStorageTemp;

class MappingStorageTempController extends Controller
{
    public function index(Request $request){
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('logbook.mapping-storage-temp', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('lb_storage_temps')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select('id', 'name', 'top_value', 'bottom_value', 'top_value_center', 'bottom_value_center', 'interval', 'uom', 'status');

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

    public function select(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('lb_storage_temps')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(['name as id', 'name as text']);

        if ($request->has('search')) {
            $query->whereRaw("LOWER(name) like '%" . strtolower($request->search) . "%'");
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
                        'name' => 'required',
                        'top_value' => 'required',
                        'bottom_value' => 'required',
                        'top_value_center' => 'required',
                        'bottom_value_center' => 'required',
                        'interval' => 'required',
                        'status' => 'required'
                    ]);

        $userAuth = $request->get('userAuth');

        $lbStorageTemp = new LbStorageTemp;
        $lbStorageTemp->company_id = $userAuth->company_id_selected;
        $lbStorageTemp->name = $request->name;
        $lbStorageTemp->top_value = $request->top_value;
        $lbStorageTemp->bottom_value = $request->bottom_value;
        $lbStorageTemp->top_value_center = $request->top_value_center;
        $lbStorageTemp->bottom_value_center = $request->bottom_value_center;
        $lbStorageTemp->interval = $request->interval;
        $lbStorageTemp->uom = $request->uom;
        $lbStorageTemp->status = $request->status;
        if ($lbStorageTemp->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("mapping storage temperature")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("mapping storage temperature")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                        'name' => 'required',
                        'top_value' => 'required',
                        'bottom_value' => 'required',
                        'top_value_center' => 'required',
                        'bottom_value_center' => 'required',
                        'interval' => 'required',
                        'status' => 'required'
                    ]);

        $lbStorageTemp = LbStorageTemp::find($request->id);
        $lbStorageTemp->name = $request->name;
        $lbStorageTemp->top_value = $request->top_value;
        $lbStorageTemp->bottom_value = $request->bottom_value;
        $lbStorageTemp->top_value_center = $request->top_value_center;
        $lbStorageTemp->bottom_value_center = $request->bottom_value_center;
        $lbStorageTemp->interval = $request->interval;
        $lbStorageTemp->uom = $request->uom;
        $lbStorageTemp->status = $request->status;
        if ($lbStorageTemp->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("mapping storage temperature")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("mapping storage temperature")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        // if( Helper::used( $id, 'lb_task_clean_id', ['lb_dly_cleans'] ) ){
        //     return response()->json( Helper::resJSON( 'failed', Lang::get('validation.used') ) );
        // }

        $lbStorageTemp = LbStorageTemp::find($id);

        if ($lbStorageTemp->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("mapping storage temperature")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("mapping storage temperature")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }
}
