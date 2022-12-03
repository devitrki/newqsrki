<?php

namespace App\Http\Controllers\Application\Authentication;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Auth\Menu;

class PermissionmenuController extends Controller
{
    public function index(Request $request){
        $role = DB::table('roles')->select('id', 'name')->first();
        $permissions = DB::table('permission_lists')->select('id','name')->get();
        $dataview = [
            'menu_id' => $request->query('menuid'),
            'role' => $role,
            'permissions' => $permissions
        ];
        return view('application.authentication.permissionmenu', $dataview)->render();
    }

    public function dtble($role_id)
    {
        $permissions = DB::table('permission_lists')->select('id', 'name', 'short_name')->orderBy('id')->get();
        $role = DB::table('roles')->select('id', 'name')->where('id', $role_id)->first();
        $menus = DB::table('menus')->select('id', 'name', 'type', 'path')->where('type', 1)->orderBy('path')->get();
        $permission_menus = [];
        foreach ($menus as $menu) {
            $data = [];
            $data['role'] = $role->name;
            $data['menu'] = $menu->name;
            $data['parent'] = Menu::getDescPathParentByMenuId($menu->id);
            foreach ($permissions as $permission) {
                $checklist = $this->getChecklistPermissionMenu( $menu->type,
                                                                $permission->short_name,
                                                                $role_id,
                                                                $menu->id );
                if($checklist != 2){
                    $data['p-'.$permission->id] = '<input type="checkbox" class="checkbox-input" name="p'.$permission->id.'" value="'.$menu->id.'">';
                } else {
                    $data['p-'.$permission->id] = '<input type="checkbox" class="checkbox-input" name="p'.$permission->id.'" value="'.$menu->id.'" checked>';
                }

            }
            $permission_menus[] = $data;
        }
        return Datatables::of(collect($permission_menus))->escapeColumns(['*'])->make();
    }

    public function getChecklistPermissionMenu($menu_type, $permission_short_name, $role_id, $menu_id){
        // 0 = folder / module
        // 1 = cannot has permission
        // 2 = has permission

        $checklist = 0;
        if ($menu_type == 1) {
            $permission = DB::table('permissions')->where('name', $permission_short_name.$menu_id)->select('id')->first();
            $checklist = 1;
            if (isset($permission->id)) {
                $count_check = DB::table('role_has_permissions')
                                    ->where('permission_id', $permission->id)
                                    ->where('role_id', $role_id)
                                    ->select('id')
                                    ->count();
                if($count_check > 0){
                    $checklist = 2;
                }
            }
        }

        return $checklist;
    }

    public function store(Request $request)
    {
        if( $request->roleid != '1' ){
            DB::beginTransaction();

            // delete menu_roles and role_has_permissions
            $role = Role::find($request->roleid);
            $rolehaspermit = DB::table('role_has_permissions')->where('role_id', $request->roleid)->get();
            foreach ($rolehaspermit as $v) {
                $permit = DB::table('permissions')->where('id', $v->permission_id)->first();
                $role->revokePermissionTo($permit->name);
            }
            DB::table('menu_roles')->where('role_id', $request->roleid)->delete();

            // get permission list for sync with id 'p'*
            $permissions = DB::table('permission_lists')->select('id', 'name', 'short_name')->orderBy('id')->get();
            foreach ($permissions as $permission) {
                // list menu post
                $menus = $request["p" . $permission->id];

                if ($menus != null) {
                    foreach ($menus as $menu) {
                        // check permisson create / no
                        $check_permission = DB::table('permissions')->where('name', $permission->short_name . $menu)->count();
                        if ($check_permission < 1) {
                            // create permission
                            $new_permission = Permission::create(['name' => $permission->short_name . $menu]);
                        }

                        // give permission to role
                        $role->givePermissionTo($permission->short_name . $menu);

                        $check_menu_roles = DB::table('menu_roles')->where('menu_id', $menu)->where('role_id', $request->roleid)->count();
                        if ($check_menu_roles < 1) {
                            // insert menu role
                            DB::table('menu_roles')->insertOrIgnore(
                                ['menu_id' => $menu, "role_id" => $request->roleid]
                            );
                        }
                    }
                }
            }

            Menu::clearCacheMenus();

            DB::commit();
        }
        $stat = 'success';
        $msg = Lang::get("message.update.success", ["data" => Lang::get("permission menu")]);

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function copy(Request $request)
    {
        $request->validate([
                        'fromrole' => 'required',
                        'torole' => 'required'
                    ]);

        DB::beginTransaction();

        // delete menu_roles and role_has_permissions role to
        $role = Role::find($request->torole);
        $rolehaspermit = DB::table('role_has_permissions')->where('role_id', $request->torole)->get();
        foreach ($rolehaspermit as $v) {
            $permit = DB::table('permissions')->where('id', $v->permission_id)->first();
            $role->revokePermissionTo($permit->name);
        }
        DB::table('menu_roles')->where('role_id', $request->torole)->delete();

        // copy permission fromrole to torole
        $permission_fromrole = DB::table('role_has_permissions')->where('role_id', $request->fromrole)->get();
        foreach ($permission_fromrole as $pf) {
            $permission = DB::table('permissions')->where('id', $pf->permission_id)->first();
            // give permission to role
            $role->givePermissionTo($permission->name);
        }

        // copy menu roles fromrole to torole
        $menu_roles = DB::table('menu_roles')->where('role_id', $request->fromrole)->get();
        foreach ($menu_roles as $menu_role) {
            DB::table('menu_roles')->insertOrIgnore(
                ['menu_id' => $menu_role->menu_id, "role_id" => $request->torole]
            );
        }

        Menu::clearCacheMenus();

        DB::commit();
        $stat = 'success';
        $msg = Lang::get("message.update.success", ["data" => Lang::get("permission menu")]);

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
        DB::table('role_has_permissions')->where('role_id', $id)->delete();
        DB::table('menu_roles')->where('role_id', $id)->delete();
        $role = Role::find($id);
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
}
