<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Material;

class MaterialController extends Controller
{

    public function index(Request $request){
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('master.material', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('materials')
                ->where('company_id', $userAuth->company_id_selected)
                ->select(['id', 'code', 'description', 'type', 'group', 'uom', 'alternative_uom', 'consolidation_flag']);

        return Datatables::of($query)->addIndexColumn()->make();
    }

    public function select(Request $request){
        $userAuth = $request->get('userAuth');

        $query = DB::table('materials')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(DB::raw("id, CONCAT(code,' - ',description) as text"));

        $query = $query->where(function($query) use ($request){
            if ($request->has('search')) {
                $query->whereRaw("LOWER(code) like '%" . strtolower($request->search) . "%'");
                $query->orWhereRaw("LOWER(description) like '%" . strtolower($request->search) . "%'");
            }
        });

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

    public function autocomplete(Request $request){
        $search = $request->search;
        $materials = [];
        if ($search != '') {
            $userAuth = $request->get('userAuth');

            $materials = DB::table('materials')
                ->select(DB::raw("id, CONCAT(code,' - ',description) as text"))
                ->where('company_id', $userAuth->company_id_selected)
                ->where(function($query) use ($search){
                    $query->whereRaw("LOWER(code) like '%" . strtolower($search) . "%'");
                    $query->orWhereRaw("LOWER(description) like '%" . strtolower($search) . "%'");
                })
                ->limit(20)
                ->get();
        }
        return response()->json($materials);
    }

    public function selectAlternativeUom($id){
        $material = DB::table('materials')
                        ->select(['alternative_uom'])
                        ->where('id', $id)
                        ->first();
        $uoms = explode(',', $material->alternative_uom);
        $data = [];

        foreach ($uoms as $uom) {
            $data[] = ["id" => $uom, "text" => $uom];
        }
        return response()->json($data);
    }

    public function sync(Request $request)
    {
        $response = Http::get(config('qsrki.api.apps.url') . 'recheese/daily-sales/sap/asset/list?plant=F103');
        if($response->ok()){
            $materials = $response->json();
        }

        $response = Http::get(config('qsrki.api.apps.url') . 'recheese/daily-sales/sap/material');

        if($response->ok()){
            DB::beginTransaction();

            $userAuth = $request->get('userAuth');
            $materials = $response->json();
            if($this->syncMaterial($userAuth, $materials)){
                DB::commit();
                $stat = 'success';
                $msg = Lang::get("message.sync.success", ["data" => Lang::get("material")]);
            } else {
                DB::rollback();
                $stat = 'failed';
                $msg = Lang::get("message.sync.failed", ["data" => Lang::get("material")]);
            }

        } else {
            $stat = 'failed';
            $msg = Lang::get("message.sync.failed", ["data" => Lang::get("material")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    // utility
    public function syncMaterial($userAuth, $materials){

        $result = true;
        // empty table convertion of material
        DB::table('material_convertions')->delete();

        foreach ($materials as $m) {

            $material_id = 0;

            // what it material already exist in database
            $cMaterial = DB::table('materials')->where('code', $m['MATNR'])->count();
            if( $cMaterial > 0 ){
                // replace data except code
                $material = Material::where('code', $m['MATNR'])->first();
                $material->description = $m['MAKTX'];
                $material->type = $m['MTART'];
                $material->group = $m['MATKL'];
                $material->uom = $m['MEINS'];
                $material->alternative_uom = $m['ZMEIN'];
                $material->consolidation_flag = $m['EXTWG'];
                if( $material->save() ){
                    $material_id = $material->id;
                }else{
                    $result = false;
                    break;
                }
            }else{
                // insert material
                $material = new Material;
                $material->company_id = $userAuth->company_id_selected;
                $material->code = $m['MATNR'];
                $material->description = $m['MAKTX'];
                $material->type = $m['MTART'];
                $material->group = $m['MATKL'];
                $material->uom = $m['MEINS'];
                $material->alternative_uom = $m['ZMEIN'];
                $material->consolidation_flag = $m['EXTWG'];
                if( $material->save() ){
                    $material_id = $material->id;
                }else{
                    $result = false;
                    break;
                }
            }

            // insert convertion of material
            if($m['WERKS'] == 'R101' && $material_id != 0){
                $meins = explode(',', $m['ZMEIN']); #uom
                $meuns = explode(';', $m['ZMEUN']); #uom unit
                $c     = count($meins);
                if($c > 0){
                    $base_qty = $meuns[0];
                    for ($i = 0; $i < $c; $i++) {
                        $material_convertion = DB::table('material_convertions')->insert(
                            [
                                'material_id' => $material_id,
                                'base_qty' => $base_qty,
                                'base_uom' => $m['MEINS'],
                                'convertion_qty' => $meuns[$i],
                                'convertion_uom' => $meins[$i]
                            ]
                        );
                        if ($m['MEINS'] == $meins[$i]) {
                            $base_qty = $meuns[$i];
                        }
                    }

                    DB::table("material_convertions")
                            ->where('material_id', $material_id)
                            ->update(['base_qty' => $base_qty]);

                }
            }

        }

        return $result;

    }

    public function getDataMaterial($id)
    {
        $data = [];

        $material = DB::table('materials')
                ->where('id', $id)
                ->select(
                    'id',
                    'code',
                    'description',
                    'uom',
                    'alternative_uom'
                )
                ->first();

        $uoms = explode(',', $material->alternative_uom);
        $uom_alt = [];
        foreach ($uoms as $uom) {
            $uom_alt[] = ["id" => $uom, "text" => $uom];
        }

        $data = [
            'material' => $material,
            'alternative_uom' => $uom_alt
        ];

        return response()->json($data);
    }
}
