<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Profile;
use App\Models\Department;
use Hamcrest\Arrays\IsArray;

class DepartmentController extends Controller
{
    public function index(Request $request){
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('master.department', $dataview)->render();
    }

    public function dtble()
    {
        $query = DB::table('departments')->select(['id', 'name']);
        return Datatables::of($query)->addIndexColumn()->make();
    }

    public function select(Request $request){
        $query = DB::table('departments')->select(['id', 'name as text']);

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
                if( !is_array($data) ){
                    $data->prepend(['id' => 0, 'text' => Lang::get('All')]);
                }
            }
        }

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $request->validate([
                        'name' => 'required|unique:departments,name'
                    ]);

        $department = new Department;
        $department->name = $request->name;
        if ($department->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("department")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("department")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                        'name' => 'required',
                    ]);

        $department = Department::find($request->id);
        $department->name = $request->name;
        if ($department->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("department")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("department")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        if( Helper::used( $id, 'department_id', ['profiles'] ) ){
            return response()->json( Helper::resJSON( 'failed', Lang::get('validation.used') ) );
        }

        $department = Department::find($id);
        if ($department->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("department")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("department")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }
}
