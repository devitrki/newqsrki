<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Inventory\OpnameMaterialFormula;
use App\Models\Inventory\OpnameMaterialFormulaItem;

class OpnameMaterialFormulaController extends Controller
{
    public function index(Request $request){
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('master.opname-material-formula', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('opname_material_formulas')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(
                        'id',
                        'material_code',
                        'material_name'
                    );
        return Datatables::of($query)->addIndexColumn()->make();
    }

    public function store(Request $request)
    {
        $request->validate([
            'material' => 'required|unique:opname_material_formulas,material_code',
        ]);

        $userAuth = $request->get('userAuth');

        $materialOutlet = DB::table('material_outlets')
                            ->where('code', $request->material)
                            ->first();

        $opnameMaterialFormula = new OpnameMaterialFormula;
        $opnameMaterialFormula->company_id = $userAuth->company_id_selected;
        $opnameMaterialFormula->material_code = $materialOutlet->code;
        $opnameMaterialFormula->material_name = $materialOutlet->description;
        if ($opnameMaterialFormula->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("opname material formula")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("opname material formula")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'material' => 'required',
        ]);

        $materialOutlet = DB::table('material_outlets')
                            ->where('code', $request->material)
                            ->first();

        $opnameMaterialFormula = OpnameMaterialFormula::find($id);
        $opnameMaterialFormula->material_code = $materialOutlet->code;
        $opnameMaterialFormula->material_name = $materialOutlet->description;
        if ($opnameMaterialFormula->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("opname material formula")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("opname material formula")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        $opnameMaterialFormula = OpnameMaterialFormula::find($id);

        OpnameMaterialFormulaItem::where('opname_material_formula_id', $opnameMaterialFormula->id)->delete();

        if ($opnameMaterialFormula->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("opname material formula")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("opname material formula")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    // items

    public function dtbleItem($opname_material_formula_id)
    {
        $query = DB::table('opname_material_formula_items')
                    ->where('opname_material_formula_id', $opname_material_formula_id)
                    ->select(
                        'id',
                        'material_code',
                        'material_name',
                        'multiplication'
                    );

        return Datatables::of($query)
                ->editColumn('multiplication', function ($data) {
                    $multiplication = $data->multiplication * 1;
                    return $multiplication;
                })
                ->addIndexColumn()
                ->make();
    }

    public function storeItem(Request $request, $opname_material_formula_id)
    {
        $request->validate([
            'material' => 'required',
            'multiplication' => 'required'
        ]);

        $materialOutlet = DB::table('material_outlets')
                            ->where('code', $request->material)
                            ->first();

        $opnameMaterialFormulaItem = new OpnameMaterialFormulaItem;
        $opnameMaterialFormulaItem->opname_material_formula_id = $opname_material_formula_id;
        $opnameMaterialFormulaItem->material_code = $materialOutlet->code;
        $opnameMaterialFormulaItem->material_name = $materialOutlet->description;
        $opnameMaterialFormulaItem->multiplication = $request->multiplication;
        if ($opnameMaterialFormulaItem->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("opname material formula item")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("opname material formula item")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function updateItem(Request $request, $opname_material_formula_id)
    {
        $request->validate([
            'material' => 'required',
            'multiplication' => 'required'
        ]);

        $materialOutlet = DB::table('material_outlets')
                            ->where('code', $request->material)
                            ->first();

        $opnameMaterialFormulaItem = OpnameMaterialFormulaItem::find($request->id);
        $opnameMaterialFormulaItem->material_code = $materialOutlet->code;
        $opnameMaterialFormulaItem->material_name = $materialOutlet->description;
        $opnameMaterialFormulaItem->multiplication = $request->multiplication;
        if ($opnameMaterialFormulaItem->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("opname material formula item")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("opname material formula item")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }


    public function destroyItem(Request $request, $opname_material_formula_id, $id)
    {
        $opnameMaterialFormulaItem = OpnameMaterialFormulaItem::find($id);
        if ($opnameMaterialFormulaItem->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("opname material formula item")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("opname material formula item")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }
}
