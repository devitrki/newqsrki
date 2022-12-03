<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;

use App\Library\Helper;
use App\Models\Area;

class AreaController extends Controller
{
    public function index(Request $request){
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('master.area', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('areas')
                    ->select(['id', 'area'])
                    ->where('company_id', $userAuth->company_id_selected);

        return Datatables::of($query)->addIndexColumn()->make();
    }

    public function select(Request $request)
    {
        $userAuth = $request->get('userAuth');
        $query = DB::table('areas')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(['id', 'area as text']);

        if ($request->has('search')) {
            $query->whereRaw("LOWER(area) like '%" . strtolower($request->search) . "%'");
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
                        'area' => 'required|unique:areas,area'
                    ]);

        $userAuth = $request->get('userAuth');

        $area = new Area;
        $area->area = $request->area;
        $area->company_id = $userAuth->company_id_selected;
        if ($area->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("area")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("area")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                        'area' => 'required',
                    ]);

        $area = Area::find($request->id);
        $area->area = $request->area;
        if ($area->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("area")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("area")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        if( Helper::used( $id, 'area_id', ['plants'] ) ){
            return response()->json( Helper::resJSON( 'failed', Lang::get('validation.used') ) );
        }

        $area = area::find($id);
        if ($area->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("area")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("area")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }
}
