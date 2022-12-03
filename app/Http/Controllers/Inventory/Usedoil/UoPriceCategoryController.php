<?php

namespace App\Http\Controllers\Inventory\Usedoil;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Inventory\Usedoil\UoCategoryPrice;
use App\Models\Inventory\Usedoil\UoCategoryPriceDetail;
use App\Models\User;

class UoPriceCategoryController extends Controller
{
    public function index(Request $request){
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('inventory.usedoil.uo-price-category', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('uo_category_prices')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(['id', 'name']);

        return Datatables::of($query)->addIndexColumn()->make();
    }

    public function dtbleDetail(Request $request, $id)
    {
        if( $id != '0' ){
            $query = DB::table('uo_materials')
                    ->leftJoin('uo_category_price_details', 'uo_category_price_details.uo_material_id', 'uo_materials.id')
                    ->select('uo_materials.id', 'uo_materials.code', 'uo_materials.name', 'uo_materials.uom', 'uo_category_price_details.price')
                    ->where('uo_category_price_details.uo_category_price_id', $id);
        } else {
            $userAuth = $request->get('userAuth');

            $query = DB::table('uo_materials')
                        ->where('company_id', $userAuth->company_id_selected)
                        ->select('id', 'code', 'name', 'uom', DB::raw('0 as price'));
        }

        return Datatables::of($query)
                ->addIndexColumn()
                ->addColumn('price_input', function ($data) {
                    $price = ($data->price != '') ? $data->price : '0' ;
                    return '<input type="number" class="form-control form-control-sm" name="uopricecategory[]" value="' . $price . '" style="min-width: 6rem;">';
                })
                ->rawColumns(['price_input'])
                ->make();
    }

    public function select(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('uo_category_prices')
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
                        'name' => 'required|unique:uo_category_prices,name',
                    ]);

        $userAuth = $request->get('userAuth');

        DB::beginTransaction();

        $uoCategoryPrice = new UoCategoryPrice;
        $uoCategoryPrice->company_id = $userAuth->company_id_selected;
        $uoCategoryPrice->name = $request->name;
        if ($uoCategoryPrice->save()) {

            $insertDetail = [];
            for ($i=0; $i < sizeof($request->material_id); $i++) {
                $insertDetail[] = [
                    'uo_category_price_id' => $uoCategoryPrice->id,
                    'uo_material_id' => $request->material_id[$i],
                    'price' => $request->price[$i]
                ];
            }

            DB::table('uo_category_price_details')->insert($insertDetail);

            DB::commit();
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("category price")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("category price")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                        'name' => 'required',
                    ]);

        DB::beginTransaction();

        $uoCategoryPrice = UoCategoryPrice::find($request->id);
        $uoCategoryPrice->name = $request->name;
        if ($uoCategoryPrice->save()) {

            // clear detail
            DB::table('uo_category_price_details')->where('uo_category_price_id', $uoCategoryPrice->id)->delete();

            $insertDetail = [];
            for ($i=0; $i < sizeof($request->material_id); $i++) {
                $insertDetail[] = [
                    'uo_category_price_id' => $uoCategoryPrice->id,
                    'uo_material_id' => $request->material_id[$i],
                    'price' => $request->price[$i]
                ];
            }

            DB::table('uo_category_price_details')->insert($insertDetail);

            DB::commit();

            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("category price")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("category price")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        $uoCategoryPrice = UoCategoryPrice::find($id);

        DB::beginTransaction();

        // delete mapping
        DB::table('uo_category_price_details')->where('uo_category_price_id', $uoCategoryPrice->id)->delete();

        if ($uoCategoryPrice->delete()) {
            DB::commit();
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("category price detail")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("category price detail")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

}
