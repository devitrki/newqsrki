<?php

namespace App\Http\Controllers\Logbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Logbook\LbProdOrganoleptik;

class MappingProdOrganoController extends Controller
{
    public function index(Request $request){
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('logbook.mapping-product-organoleptik', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('lb_prod_organoleptiks')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select('id', 'product', 'desc_taste', 'desc_aroma', 'desc_texture', 'desc_color');

        return Datatables::of($query)
                        ->addIndexColumn()
                        ->make();
    }

    public function select(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('lb_prod_organoleptiks')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(['product as id', 'product as text']);

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

        $lbProdOrganoleptik = new LbProdOrganoleptik;
        $lbProdOrganoleptik->company_id = $userAuth->company_id_selected;
        $lbProdOrganoleptik->product = $request->product;
        $lbProdOrganoleptik->desc_taste = $request->desc_taste;
        $lbProdOrganoleptik->desc_aroma = $request->desc_aroma;
        $lbProdOrganoleptik->desc_texture = $request->desc_texture;
        $lbProdOrganoleptik->desc_color = $request->desc_color;
        if ($lbProdOrganoleptik->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("mapping product organoleptik")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("mapping product organoleptik")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                        'product' => 'required',
                    ]);

        $lbProdOrganoleptik = LbProdOrganoleptik::find($request->id);
        $lbProdOrganoleptik->product = $request->product;
        $lbProdOrganoleptik->desc_taste = $request->desc_taste;
        $lbProdOrganoleptik->desc_aroma = $request->desc_aroma;
        $lbProdOrganoleptik->desc_texture = $request->desc_texture;
        $lbProdOrganoleptik->desc_color = $request->desc_color;
        if ($lbProdOrganoleptik->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("mapping product organoleptik")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("mapping product organoleptik")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        $lbProdOrganoleptik = LbProdOrganoleptik::find($id);

        if ($lbProdOrganoleptik->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("mapping product organoleptik")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("mapping product organoleptik")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function detail(Request $request){
        $data = LbProdOrganoleptik::where('product', trim($request->product))->first();
        return response()->json( Helper::resJSON( 'success', '', $data ) );
    }
}
