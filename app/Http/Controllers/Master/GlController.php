<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Gl;
use App\Models\Plant;

class GlController extends Controller
{
    public function index(Request $request){
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('master.gl', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('gls')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(['id', 'name', 'description', 'code', 'privilege']);

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

    public function select(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('gls')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(DB::raw("code as id, CONCAT(code ,' - ', name) as text"));

        if ($request->has('search')) {
            $query = $query->where(function($query) use ($request){
                $query->whereRaw("LOWER(code) like '%" . strtolower($request->search) . "%'");
                $query->orWhereRaw("LOWER(name) like '%" . strtolower($request->search) . "%'");
            });
        }

        if ($request->has('map')) {

            $typePlant = Plant::getTypeIdByPlantId($request->query('plant'));

            $query = $query->whereIn('privilege', [0, $typePlant]);
        }

        if ($request->has('limit')) {
            $query = $query->limit($request->limit);
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
                        'code' => 'required',
                        'name' => 'required',
                        'description' => 'required',
                        'privilege' => 'required',
                    ]);

        $userAuth = $request->get('userAuth');

        $gl = new Gl;
        $gl->company_id = $userAuth->company_id_selected;
        $gl->code = $request->code;
        $gl->name = $request->name;
        $gl->description = $request->description;
        $gl->privilege = $request->privilege;
        if ($gl->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("gl")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("gl")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                        'code' => 'required',
                        'name' => 'required',
                        'description' => 'required',
                        'privilege' => 'required',
                    ]);

        $gl = Gl::find($request->id);
        $gl->code = $request->code;
        $gl->name = $request->name;
        $gl->description = $request->description;
        $gl->privilege = $request->privilege;
        if ($gl->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("gl")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("gl")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        $gl = Gl::find($id);
        if ($gl->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("gl")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("gl")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }
}
