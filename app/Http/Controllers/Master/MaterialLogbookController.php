<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\MaterialLogbook;

class MaterialLogbookController extends Controller
{
    public function index(Request $request){
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('master.material-logbook', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('material_logbooks')
                ->where('company_id', $userAuth->company_id_selected)
                ->select(['id', 'name', 'uom', 'source']);

        return Datatables::of($query)->addIndexColumn()->make();
    }

    public function select(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('material_logbooks')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(DB::raw('id, name AS text'));

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

    public function sync(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $material_sap = DB::table('materials')
                            ->where('company_id', $userAuth->company_id_selected)
                            ->select('description', 'uom')->get();

        $result = true;
        DB::beginTransaction();

        foreach ($material_sap as $m) {
            $c_exist = DB::table('material_logbooks')
                        ->where('company_id', $userAuth->company_id_selected)
                        ->where('name', $m->description)
                        ->count();

            if ($c_exist < 1) {
                // not exist and insert
                $materialLogbook = new MaterialLogbook;
                $materialLogbook->company_id = $userAuth->company_id_selected;
                $materialLogbook->name = strtoupper($m->description);
                $materialLogbook->uom = $m->uom;
                $materialLogbook->source = 'SAP';
                if( !$materialLogbook->save() ){
                    $result = false;
                    break;
                }
            }
        }

        if($result){
            DB::commit();
            $stat = 'success';
            $msg = Lang::get("message.sync.success", ["data" => Lang::get("material logbook")]);
        } else {
            DB::rollback();
            $stat = 'failed';
            $msg = Lang::get("message.sync.failed", ["data" => Lang::get("material logbook")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function store(Request $request)
    {
        $request->validate([
                        'name' => 'required|unique:material_logbooks,name',
                        'uom' => 'required'
                    ]);

        $userAuth = $request->get('userAuth');

        $materialLogbook = new MaterialLogbook;
        $materialLogbook->company_id = $userAuth->company_id_selected;
        $materialLogbook->name = strtoupper($request->name);
        $materialLogbook->uom = strtoupper($request->uom);
        $materialLogbook->source = 'WEB';
        if ($materialLogbook->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("material logbook")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("material logbook")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                        'name' => 'required',
                        'uom' => 'required'
                    ]);

        $materialLogbook = MaterialLogbook::find($request->id);
        $materialLogbook->name = strtoupper($request->name);
        $materialLogbook->uom = strtoupper($request->uom);
        if ($materialLogbook->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("material logbook")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("material logbook")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        if( Helper::used( $id, 'material_logbook_id', ['lb_inv_cashiers', 'lb_inv_kitchens', 'lb_inv_warehouses',
            'lb_stock_cards', 'lb_dly_wasteds'] ) ){
            return response()->json( Helper::resJSON( 'failed', Lang::get('validation.used') ) );
        }

        $materialLogbook = MaterialLogbook::find($id);

        if($materialLogbook->source == "SAP"){
            return response()->json( Helper::resJSON( 'failed', Lang::get("Cannot delete material source from SAP") ) );
        }

        if ($materialLogbook->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("material logbook")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("material logbook")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }
}
