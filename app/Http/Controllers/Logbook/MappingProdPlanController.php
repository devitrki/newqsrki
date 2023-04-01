<?php

namespace App\Http\Controllers\Logbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Logbook\LbProductProdPlan;

class MappingProdPlanController extends Controller
{
    public function index(Request $request){
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('logbook.mapping-product-prod-plan', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('lb_product_prod_plans')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select('id', 'product');

        return Datatables::of($query)
                        ->addIndexColumn()
                        ->make();
    }

    public function select(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('lb_product_prod_plans')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(['product as id', 'product as text'])
                    ->orderBy('product');

        if ($request->has('search')) {
            $query->whereRaw("LOWER(product) like '%" . strtolower($request->search) . "%'");
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
                        'product' => 'required'
                    ]);

        $userAuth = $request->get('userAuth');

        $lbProductProdPlan = new LbProductProdPlan;
        $lbProductProdPlan->company_id = $userAuth->company_id_selected;
        $lbProductProdPlan->product = $request->product;
        if ($lbProductProdPlan->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("mapping product production planning")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("mapping product production planning")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                        'product' => 'required',
                    ]);

        $lbProductProdPlan = LbProductProdPlan::find($request->id);
        $lbProductProdPlan->product = $request->product;
        if ($lbProductProdPlan->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("mapping product production planning")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("mapping product production planning")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        $lbProductProdPlan = LbProductProdPlan::find($id);

        if ($lbProductProdPlan->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("mapping product production planning")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("mapping product production planning")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }
}
