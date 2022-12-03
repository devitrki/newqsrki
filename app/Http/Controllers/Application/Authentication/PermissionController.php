<?php

namespace App\Http\Controllers\Application\Authentication;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Auth\PermissionList;

class PermissionController extends Controller
{
    public function index(Request $request){
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('application.authentication.permission', $dataview)->render();
    }

    public function dtble()
    {
        $query = DB::table('permission_lists')->select(['id', 'name', 'short_name']);
        return Datatables::of($query)->addIndexColumn()->make();
    }

    public function store(Request $request)
    {
        $request->validate([
                        'name' => 'required|unique:permission_lists,name',
                        'short_name' => 'required'
                    ]);

        $permissionList = new PermissionList;
        $permissionList->name = $request->name;
        $permissionList->short_name = strtolower($request->short_name);
        if ($permissionList->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("permission")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("permission")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                        'name' => 'required',
                        'short_name' => 'required'
                    ]);

        $permissionList = PermissionList::find($request->id);
        $permissionList->name = $request->name;
        $permissionList->short_name = strtolower($request->short_name);
        if ($permissionList->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("permission")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("permission")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        $permissionList = PermissionList::find($id);
        if ($permissionList->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("permission")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("permission")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }
}
