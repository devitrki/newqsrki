<?php

namespace App\Http\Controllers\Inventory\Usedoil;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Inventory\Usedoil\UoMaterial;
use App\Models\Inventory\Usedoil\UoStock;

class UoMaterialController extends Controller
{
    public function index(Request $request){
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('inventory.usedoil.uo-material', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('uo_materials')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(['id', 'code', 'name', 'uom']);

        return Datatables::of($query)->addIndexColumn()->make();
    }

    public function dtbleQty($inputName, $plantId)
    {
        $data = [];
        if( $plantId != '0' ){
            $plant = DB::table('plants')
                        ->where('id', $plantId)
                        ->first();

            $query = DB::table('uo_materials')
                        ->select('id', 'code', 'name', 'uom')
                        ->get();

            foreach ($query as $q) {
                $stock = UoStock::getStockCurrent($plant->company_id, $plantId, $q->code);
                $data[] = [
                    'id' => $q->id,
                    'code' => $q->code,
                    'name' => $q->name,
                    'uom' => $q->uom,
                    'inputName' => $inputName,
                    'stock' => $stock,
                    'stock_desc' => Helper::convertNumberToInd($stock, '', 2),
                ];
            }
        }

        return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('qty_input', function ($data) {
                    return '<input type="number" class="form-control form-control-sm mul" name="' . $data['inputName'] . '[]" value="0" style="min-width: 6rem;">';
                })
                ->rawColumns(['qty_input'])
                ->make();
    }

    public function select(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('uo_materials')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(['id', 'name as text']);

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
                        'code' => 'required|unique:uo_materials,code',
                        'name' => 'required',
                        'uom' => 'required'
                    ]);

        $userAuth = $request->get('userAuth');

        $uoMaterial = new UoMaterial;
        $uoMaterial->company_id = $userAuth->company_id_selected;
        $uoMaterial->code = $request->code;
        $uoMaterial->name = $request->name;
        $uoMaterial->uom = $request->uom;
        if ($uoMaterial->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("material usedoil")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("material usedoil")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                        'code' => 'required',
                        'name' => 'required',
                        'uom' => 'required'
                    ]);

        $uoMaterial = UoMaterial::find($request->id);
        $uoMaterial->code = $request->code;
        $uoMaterial->name = $request->name;
        $uoMaterial->uom = $request->uom;
        if ($uoMaterial->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("material usedoil")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("material usedoil")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        $uoMaterial = UoMaterial::find($id);
        if ($uoMaterial->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("material usedoil")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("material usedoil")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }
}
