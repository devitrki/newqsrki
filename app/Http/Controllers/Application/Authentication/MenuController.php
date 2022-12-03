<?php

namespace App\Http\Controllers\Application\Authentication;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use App\Library\Helper;
use Yajra\DataTables\DataTables;

use App\Models\Auth\Menu;
use App\Rules\CheckMenu;

class MenuController extends Controller
{
    public function getJSONSearchMenu(){
        $menus = Menu::getListMenusByUserId(Auth::id());
        return response()->json(['listItems' => $menus]);
    }

    public function getViewMenu(){
        return view('welcome')->render();
    }

    public function getMenuTreeviewJson(){
        $menus = [
            "text" => "Roots",
            "data" => [
                "id" => 0,
                "url" => null,
                "type" => 3,
                "name" => "Roots",
                "description" => "Roots",
                "sort_order" => 1
            ],
            "nodes" => Menu::getTreeviewMenuJSON()
        ];

        return response()->json([$menus]);
    }

    public function getMenuDtble(Request $request){
        $query = DB::table('menus')->select(['id', 'type', 'path', 'name', 'description', 'url']);
        if( $request->has('parentid') ) {
            $query->where('parent_id', $request->query('parentid'));

            if ($query->count() < 1) {
                $query->orWhere('id', $request->query('parentid'));
            }
        }
        $query->orderBy('sort_order');
        return Datatables::of($query)->addIndexColumn()
                                    ->editColumn('type', '{{($type==1) ? "File" : "Folder"}}')
                                    ->make();
    }

    public function getSortOrderByMenu($id){
        $query = DB::table('menus')->where('id', $id)->select(['sort_order'])->first();
        return $query->sort_order;
    }

    public function changeSort(Request $request){
        $map_sort = [];
        foreach ($request->arsort as $v) {
            $ids = explode(',', $v);
            $map_sort[] = [
                $ids[0],
                $this->getSortOrderByMenu($ids[1])
            ];
        }
        $ok = 1;
        DB::beginTransaction();
        foreach ($map_sort as $sorted) {
            $menu = Menu::find($sorted[0]);
            $menu->sort_order = $sorted[1];
            if (!$menu->save()) {
                $ok = 0;
                exit;
            }
        }
        if ($ok != 1) {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => "index menu"]);
        }else {
            DB::commit();
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => "index menu"]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function createMenu(Request $request){
        $request->validate([
                        'menu' => 'required|unique:menus,name',
                        'description' => 'required',
                        'url' => 'required',
                        'parentid' => ['required', new CheckMenu],
                    ]);

        DB::beginTransaction();
        $menu = new Menu;
        $menu->parent_id = $request->parentid;
        $menu->type = 1;
        $menu->path = '';
        $menu->name = $request->menu;
        $menu->description = $request->description;
        $menu->url = $request->url;
        $menu->flag_end = 1;
        $menu->sort_order = 0;
        if ($menu->save()) {
            Menu::updatePathMenu($menu->id);
            Menu::updateSortOrderMenu($menu->id);
            // give menu role superadmin
            DB::table('menu_roles')->insert(
                ['menu_id' => $menu->id, 'role_id' => 1]
            );
            DB::commit();
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("menu")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("menu")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function createFolder(Request $request){
        $request->validate([
                        'menu' => 'required',
                        'description' => 'required',
                        'parentid' => ['required', new CheckMenu],
                    ]);

        DB::beginTransaction();
        $menu = new Menu;
        $menu->parent_id = $request->parentid;
        $menu->type = ($request->parentid != 0) ? 2 : 3 ;
        $menu->path = '';
        $menu->name = $request->menu;
        $menu->description = $request->description;
        $menu->flag_end = 1;
        $menu->sort_order = 0;
        if ($menu->save()) {
            Menu::updatePathMenu($menu->id);
            Menu::updateSortOrderMenu($menu->id);
            DB::commit();
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("folder")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("folder")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request){
        $menu = Menu::find($request->id);
        if($menu->type != 1){
            $request->validate([
                        'menu' => 'required',
                        'description' => 'required'
                    ]);
        } else {
            $request->validate([
                        'menu' => 'required',
                        'description' => 'required',
                        'url' => 'required',
                    ]);
        }

        $menu->name = $request->menu;
        $menu->description = $request->description;
        $menu->url = $request->url;
        if ($menu->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("folder / menu")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("folder / menu")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id){
        // check child
        $parent = Menu::where("parent_id", $id)->select('id')->count();
        if( $parent > 0 ){
            return response()->json( Helper::resJSON( 'failed', Lang::get("Please delete menu child first") ));
        }
        DB::beginTransaction();
        DB::table('menu_roles')->where('menu_id', $id)->delete();
        DB::table('permissions')->whereRaw('RIGHT(name, 1) = ?', $id)->delete();
        $menu = Menu::find($id);
        if ($menu->delete()) {
            DB::commit();
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("menu")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("menu")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );

    }

    public function index(Request $request){
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('application.authentication.menu', $dataview)->render();
    }

    public function check(){
        return response()->json( Helper::resJSON( 'success', 'authentication is connect' ));
    }
}
