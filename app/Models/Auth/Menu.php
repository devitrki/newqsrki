<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Cache;

use Spatie\Permission\Models\Role;
use App\Models\User;

class Menu extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public function menuRoles()
    {
        return $this->hasMany(MenuRole::class);
    }

    public static function getMappingMenuByUserId($user_id){
        $mapping_menu_by_user = Cache::tags('menus')->get('mapping_menu_by_user_id_' . $user_id);
        if (!$mapping_menu_by_user) {
            $list_menus = Menu::getListMenuByRoleUserId($user_id);
            $mapping_menu_by_user = Menu::getMappingMenu($list_menus);
            Cache::tags('menus')->put('mapping_menu_by_user_id_' . $user_id, $mapping_menu_by_user);
        }

        return $mapping_menu_by_user;
    }

    public static function getListMenuByRoleUserId($user_id){
        // get user by user login
        $user = User::find($user_id);

        // get roles user
        $roles = $user->getRoleNames();
        $role_name = ( sizeof($roles) > 0 ) ? $roles[0] : '0';
        $roleid = 0;
        if($role_name != '0'){
            $role = Role::where('name', $role_name)->first();
            $roleid = $role->id;
        }
        // get list menu id by role
        $list_menus = DB::table('menu_roles')
                        ->where('role_id', $roleid)
                        ->select('menu_id')
                        ->get();

        return $list_menus;
    }

    public static function getMappingMenu($list_menus){
        $mapping_parent = [];
        $mapping_root = [];
        foreach ($list_menus as $v) {
            $path = DB::table('menus')
                        ->where('id', $v->menu_id)
                        ->select('path', 'type')
                        ->first();
            $paths = explode(',', $path->path);
            for ($i=0; $i < sizeof($paths)-1; $i++) {

                if( isset($mapping_parent[ $paths[$i] ]) ){
                    if( !in_array( $paths[$i+1], $mapping_parent[ $paths[$i] ] ) ){
                        $mapping_parent[ $paths[$i] ][] = $paths[$i+1];
                    }
                } else {
                    $mapping_parent[ $paths[$i] ][] = $paths[$i+1];
                }

                if ($i == 0 && !in_array( $paths[$i], $mapping_root ) ){
                    $mapping_root[] =  $paths[$i];
                }

            }
        }
        return [
                "parent" => $mapping_parent,
                "root" => $mapping_root
               ];
    }

    public static function getMappingMenuAll(){
        $mapping_parent = [];
        $mapping_root = [];
        $list_menus = DB::table('menus')
                        ->where('flag_end', 1)
                        ->select('id')
                        ->get();

        foreach ($list_menus as $v) {
            $path = DB::table('menus')
                        ->where('id', $v->id)
                        ->select('path', 'type', 'flag_end')
                        ->first();

            $paths = explode(',', $path->path);
            if(sizeof($paths) > 1){
                for ($i=0; $i < sizeof($paths)-1; $i++) {

                    if( isset($mapping_parent[ $paths[$i] ]) ){
                        if( !in_array( $paths[$i+1], $mapping_parent[ $paths[$i] ] ) ){
                            $mapping_parent[ $paths[$i] ][] = $paths[$i+1];
                        }
                    } else {
                        $mapping_parent[ $paths[$i] ][] = $paths[$i+1];
                    }

                    if ($i == 0 && !in_array( $paths[$i], $mapping_root ) ){
                        $mapping_root[] =  $paths[$i];
                    }

                }
            } else {
                for ($i=0; $i < sizeof($paths); $i++) {
                    if ($i == 0 && !in_array( $paths[$i], $mapping_root ) ){
                        $mapping_root[] =  $paths[$i];
                    }
                }
            }

        }
        return [
                "parent" => $mapping_parent,
                "root" => $mapping_root
               ];
    }

    public static function getStructureMenuArray($list_menu, $mapping_parent){
        $structure = [];

        $list_menu = DB::table('menus')
                        ->whereIn('id', $list_menu)
                        ->select('id', 'url', 'type', 'name', 'description', 'sort_order')
                        ->orderBy('sort_order')
                        ->get();

        foreach ($list_menu as $menu) {
            if( isset($mapping_parent[$menu->id]) ){
                $menu->child = Menu::getStructureMenu($mapping_parent[$menu->id], $mapping_parent);
            }
            $structure[] = $menu;
        }

        return $structure;
    }

    public static function getStructureMenuHTML($list_menu, $mapping_parent){
        $structure = [];

        $list_menu = DB::table('menus')
                        ->whereIn('id', $list_menu)
                        ->select('id', 'url', 'type', 'name', 'description', 'sort_order')
                        ->orderBy('sort_order')
                        ->get();

        foreach ($list_menu as $menu) {
            if( $menu->type == 3 ){
                // for module
                echo '<li class="navigation-header">';
                    echo '<span>' . Lang::get($menu->name) . '</span>';
                echo '</li>';

            } else {
                // for menu folder / file
                echo '<li class=" nav-item">';
                    if( $menu->type == 2 ){
                    echo '<a href="javascript:void(0)">';
                        echo '<i class="bx bx-folder"></i>';
                    }else{
                    echo '<a href="javascript:void(0)" onclick="main_menu.openMenuTabs(' . $menu->id . ',\'' . $menu->name . '\',\'' . $menu->url .'\')">';
                        echo '<i class="bx bx-file"></i>';
                    }
                        echo '<span>' . Lang::get($menu->name) . '</span>';
                    echo '</a>';
            }
            if( isset($mapping_parent[$menu->id]) ){
                if( $menu->type != 3 ){
                    echo '<ul class="menu-content">';
                }
                $menu->child = Menu::getStructureMenuHTML($mapping_parent[$menu->id], $mapping_parent);
                if( $menu->type != 3 ){
                    echo '</ul>';
                }
            }
            if( $menu->type != 3 ){
                echo '</li>';
            }
        }
    }

    public static function getTreeviewMenuJSON(){
        $mapping = Menu::getMappingMenuAll();
        $menus = Menu::getTreeviewMenu($mapping['root'], $mapping['parent']);
        return $menus;

    }

    public static function getTreeviewMenu($list_menu, $mapping_parent){
        $treeview = [];

        $list_menu = DB::table('menus')
                        ->whereIn('id', $list_menu)
                        ->select('id', 'url', 'type', 'name', 'description', 'sort_order')
                        ->orderBy('sort_order')
                        ->get();

        foreach ($list_menu as $menu) {
            $tree = [];
            $tree['text'] = $menu->name;
            $tree['data'] = $menu;
            if( isset($mapping_parent[$menu->id]) ){
                $tree['nodes'] = Menu::getTreeviewMenu($mapping_parent[$menu->id], $mapping_parent);
            } else {
                if ($menu->type == 1) {
                    $tree['icon'] = 'bx bx-file';
                }
            }
            $treeview[] = $tree;
        }

        return $treeview;

    }

    public static function getListMenusByUserIdCache($user_id){
        $list_menu_by_user = Cache::tags('menus')->get('search_menu_by_user_id_' . $user_id);
        if (!$list_menu_by_user) {
            $list_menu_by_user = Menu::getListMenusByUserId($user_id);
            Cache::tags('menus')->put('search_menu_by_user_id_' . $user_id, $list_menu_by_user);
        }

        return $list_menu_by_user;
    }

    public static function getListMenusByUserId($user_id){
        $list_menus = Menu::getListMenuByRoleUserId($user_id);
        $menus = [];
        foreach ($list_menus as $m) {
            $menu = DB::table('menus')
                    ->where('id', $m->menu_id)
                    ->first();

            $paths = explode(',', $menu->path);
            $names = "";
            for ($i=0; $i < sizeof($paths); $i++) {
                $path_menu = DB::table('menus')
                            ->where('id', $paths[$i])
                            ->select('name')
                            ->first();
                if ($i != (sizeof($paths) - 1) ) {
                    $names .= Lang::get($path_menu->name) . ' ^ ';
                } else {
                    $names .= Lang::get($path_menu->name);
                }
            }

            $list = [
                        "name" => $names,
                        "menu" => $menu->name,
                        "url" => $menu->url,
                        "icon" => "bx bx-file",
                        "id" => $menu->id,
                    ];

            $menus[] = $list;
        }
        return $menus;
    }

    public static function updatePathMenu($menu_id){
        $menu = DB::table('menus')
                    ->where('id', $menu_id)
                    ->select('id','parent_id')
                    ->first();
        $path = $menu->id;
        $parent = true;
        $parent_id = $menu->parent_id;
        while ($parent) {
            $menu = DB::table('menus')
                        ->where('id', $parent_id)
                        ->select('id','parent_id')
                        ->first();
            if(isset($menu->id)){
                $path = $menu->id . "," . $path;
                $parent_id = $menu->parent_id;
            }else{
                $parent = false;
            }
        }

        $update = DB::table('menus')
                    ->where('id', $menu_id)
                    ->update(['path' => $path]);

        return ($update) ? true : false ;
    }

    public static function updateSortOrderMenu($menu_id){
        $menu = DB::table('menus')
                    ->where('id', $menu_id)
                    ->select('id','parent_id')
                    ->first();

        $sort_order = DB::table('menus')
                    ->where('parent_id', $menu->parent_id)
                    ->select('id')
                    ->count();

        $update = DB::table('menus')
                    ->where('id', $menu_id)
                    ->update(['sort_order' => $sort_order]);

        return ($update) ? true : false ;
    }

    public static function getDescPathParentByMenuId($menu_id){
        $menu = DB::table('menus')
                    ->where('id', $menu_id)
                    ->first();
        $paths = explode(',', $menu->path);
        $desc = '';

        if(sizeof($paths) > 1){
            for ($i=0; $i < sizeof($paths)-1; $i++) {
                $path_menu = DB::table('menus')
                            ->where('id', $paths[$i])
                            ->select('name')
                            ->first();
                if ($i != (sizeof($paths) - 2) ) {
                    $desc .= Lang::get($path_menu->name) . ' ^ ';
                } else {
                    $desc .= Lang::get($path_menu->name);
                }
            }
        }
        return $desc;
    }

    public static function clearCacheMenus(){
        Cache::tags('menus')->flush();
    }
}
