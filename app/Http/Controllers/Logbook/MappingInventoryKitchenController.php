<?php

namespace App\Http\Controllers\Logbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Logbook\LbInvKitchen;

class MappingInventoryKitchenController extends Controller
{
    public function index(Request $request){
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('logbook.mapping-inventory-kitchen', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('lb_inv_kitchens')
                    ->join('material_logbooks', 'material_logbooks.id', '=', 'lb_inv_kitchens.material_logbook_id')
                    ->where('material_logbooks.company_id', $userAuth->company_id_selected)
                    ->select(['lb_inv_kitchens.id', 'material_logbooks.name', 'material_logbooks.uom', 'lb_inv_kitchens.frekuensi', 'lb_inv_kitchens.status', 'lb_inv_kitchens.material_logbook_id']);

        return Datatables::of($query)
                        ->addIndexColumn()
                        ->addColumn('status_desc', function ($data) {
                            if ($data->status != 0) {
                                return Lang::get('Active');
                            } else {
                                return Lang::get('Unactive');
                            }
                        })
                        ->filterColumn('name', function($query, $keyword) {
                            $query->whereRaw("material_logbooks.name like ?", ["%{$keyword}%"]);
                        })
                        ->filterColumn('uom', function($query, $keyword) {
                            $query->whereRaw("material_logbooks.uom like ?", ["%{$keyword}%"]);
                        })
                        ->make();
    }

    public function store(Request $request)
    {
        $request->validate([
                        'material' => 'required|unique:lb_inv_kitchens,material_logbook_id',
                        'frekuensi' => 'required',
                        'status' => 'required'
                    ]);

        $lbInvKitchen = new LbInvKitchen;
        $lbInvKitchen->material_logbook_id = $request->material;
        $lbInvKitchen->frekuensi = $request->frekuensi;
        $lbInvKitchen->status = $request->status;
        if ($lbInvKitchen->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("mapping inventory kitchen")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("mapping inventory kitchen")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                        'material' => 'required',
                        'frekuensi' => 'required',
                        'status' => 'required'
                    ]);

        $lbInvKitchen = LbInvKitchen::find($request->id);
        $lbInvKitchen->material_logbook_id = $request->material;
        $lbInvKitchen->frekuensi = $request->frekuensi;
        $lbInvKitchen->status = $request->status;
        if ($lbInvKitchen->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("mapping inventory kitchen")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("mapping inventory kitchen")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        if( Helper::used( $id, 'lb_inv_kitchen_id', [] ) ){
            return response()->json( Helper::resJSON( 'failed', Lang::get('validation.used') ) );
        }

        $lbInvKitchen = LbInvKitchen::find($id);

        if ($lbInvKitchen->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("mapping inventory kitchen")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("mapping inventory kitchen")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }
}
