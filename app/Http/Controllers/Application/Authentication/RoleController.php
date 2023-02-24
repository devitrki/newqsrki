<?php

namespace App\Http\Controllers\Application\Authentication;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Configuration;

class RoleController extends Controller
{
    public function index(Request $request){
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('application.authentication.role', $dataview)->render();
    }

    public function dtble()
    {
        $query = DB::table('roles')->where('name', '!=', 'superadmin')->select(['id', 'name', 'guard_name']);
        return Datatables::of($query)->addIndexColumn()->make();
    }

    public function select(Request $request)
    {
        $query = DB::table('roles')->select(['id', 'name as text']);

        if ($request->has('superadmin')) {
            if ($request->query('superadmin') != 'true') {
                $query = $query->where('name', '!=', 'superadmin');
            }
        } else {
            $query = $query->where('name', '!=', 'superadmin');
        }

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
                        'name' => 'required|unique:roles,name',
                    ]);

        $role = new Role;
        $role->name = strtolower($request->name);
        $role->guard_name = 'web';
        if ($role->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("role")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("role")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                        'name' => 'required',
                    ]);

        $role = Role::find($request->id);
        $role->name = strtolower($request->name);
        if ($role->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("role")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("role")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        $role = Role::find($id);
        $rolehaspermit = DB::table('role_has_permissions')->where('role_id', $id)->get();
        foreach ($rolehaspermit as $v) {
            $permit = DB::table('permissions')->where('id', $v->permission_id)->first();
            $role->revokePermissionTo($permit->name);
        }
        DB::table('menu_roles')->where('role_id', $id)->delete();
        if ($role->delete()) {
            DB::commit();
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("role")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("role")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    // utility
    public static function getRole($user_id)
    {
        $roles = User::with('roles')->where('id', $user_id)->get();
        $role = '';
        $i = 0;
        foreach ($roles as $r) {
            foreach ($r->roles as $v) {
                if($i == 0){
                    $role .= $v->name;
                }else{
                    $role .= ', ' . $v->name;
                }
            }
        }
        return $role;
    }

    public static function getRoleId($user_id)
    {
        $roles = User::with('roles')->where('id', $user_id)->get();
        $role = '';
        $i = 0;
        foreach ($roles as $r) {
            foreach ($r->roles as $v) {
                if($i == 0){
                    $role .= $v->id;
                }else{
                    $role .= ', ' . $v->id;
                }
            }
        }
        return $role;
    }

    public static function getAuthorizeRole($user_id)
    {
        $roles = User::with('roles')->where('id', $user_id)->get();

        $role_am = Configuration::getValueByKeyFor('general_master', 'role_am');
        $role_rm = Configuration::getValueByKeyFor('general_master', 'role_rm');

        $authorize_role = "";
        $role_id = 0;

        foreach ($roles as $r) {
            foreach ($r->roles as $v) {
                $role_id = $v->id;
            }
        }

        if ($role_id == $role_am) {
            $userAreas = DB::table('user_areas')->where('user_id', $user_id)->first();
            $area = DB::table('area_plants')->where('id', $userAreas->area_plant_id)->first();
            $authorize_role = $area->name;
        } else if ($role_id == $role_rm) {
            $userRegionals = DB::table('user_regionals')->where('user_id', $user_id)->first();
            $regional = DB::table('regional_plants')->where('id', $userRegionals->regional_plant_id)->first();
            $authorize_role = $regional->name;
        } else {
            $userPlants = DB::table('user_plants')->where('user_id', $user_id)->first();
            $authorize_role = Lang::get('All Plant');
            if ($userPlants) {
                if( $userPlants->plant_id != '0' ){
                    $plant = DB::table('plants')->where('id', $userPlants->plant_id)->first();
                    if ($plant) {
                        $authorize_role = $plant->initital . ' ' .  $plant->short_name;
                    } else {
                        $authorize_role = '-';
                    }

                }
            }
        }

        return $authorize_role;
    }
}
