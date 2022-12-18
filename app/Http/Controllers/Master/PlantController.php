<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Library\Helper;
use Yajra\DataTables\DataTables;

use App\Services\PlantServiceAppsImpl;
use App\Services\PlantServiceSapImpl;

use App\Models\Plant;

class PlantController extends Controller
{
    public function index(Request $request){
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('master.plant', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('plants')
                ->leftJoin('plants as pdc', 'pdc.id', '=', 'plants.dc_id')
                ->leftJoin('areas', 'areas.id', '=', 'plants.area_id')
                ->leftjoin('pos', 'pos.id', '=', 'plants.pos_id')
                ->join('companies', 'companies.id', '=', 'plants.company_id')
                ->where('plants.company_id', $userAuth->company_id_selected)
                ->select(['plants.id', 'plants.code', 'plants.initital', 'plants.short_name', 'plants.description', 'plants.type',
                        'plants.company_id', 'plants.cost_center', 'plants.cost_center_desc', 'plants.customer_code', 'plants.email',
                        'plants.phone', 'plants.address', 'plants.hours', 'plants.drivethru', 'plants.price_category', 'plants.pos_id',
                        'plants.sloc_id_gr', 'plants.sloc_id_gr_vendor', 'plants.sloc_id_waste', 'plants.sloc_id_asset_mutation',
                        'plants.sloc_id_current_stock', 'plants.sloc_id_opname', 'plants.sloc_id_gi_plant',
                        'pdc.short_name as pdc_name', 'areas.area', 'plants.dc_id', 'plants.area_id', 'companies.name as company_name',
                        'pos.name as pos_name']);

        return Datatables::of($query)
                    ->addIndexColumn()
                    ->addColumn('plant_rm', '{{ \App\Models\Plant::getRMNamePlantById($id) }}')
                    ->addColumn('plant_am', '{{ \App\Models\Plant::getAMNamePlantById($id) }}')
                    ->addColumn('plant_mod', '{{ \App\Models\Plant::getMODNamePlantById($id) }}')
                    ->addColumn('drivethru_desc', function ($data) {
                        if ($data->drivethru == 0) {
                            return "False";
                        } else {
                            return "True";
                        }
                    })
                    ->editColumn('type', function ($data) {
                        if ($data->type == 1) {
                            return "Outlet";
                        } else {
                            return "DC";
                        }
                    })
                    ->filterColumn('pdc_name', function ($query, $keyword) {
                        $sql = "pdc.short_name like ?";
                        $query->whereRaw($sql, ["%{$keyword}%"]);
                    })
                    ->filterColumn('area', function ($query, $keyword) {
                        $sql = "areas.area like ?";
                        $query->whereRaw($sql, ["%{$keyword}%"]);
                    })
                    ->make(true);
    }

    public function select(Request $request){
        $userAuth = $request->get('userAuth');

        $query = DB::table('plants')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(DB::raw("id, CONCAT(initital ,' ', short_name) AS text"));

        if ($request->has('select')) {
            if ($request->query('select') == 'code') {
                $query = DB::table('plants')
                            ->where('company_id', $userAuth->company_id_selected)
                            ->select(DB::raw("code as id, CONCAT(initital ,' ', short_name) AS text"));
            }
        }

        $query = $query->where(function($query) use ($request, $userAuth){

            if ($request->has('type')) {
                if ($request->query('type') != 'dc') {
                    $query = $query->where('type', 1);
                } else {
                    $query = $query->where('type', 2);
                }
            }

            $query = $query->where(function($query) use ($request, $userAuth){
                if ($request->has('auth')) {
                    if ($request->query('auth') == 'true') {
                        $plants_auth = Plant::getPlantsIdByUserId(Auth::id());
                        $plants = explode(',', $plants_auth);
                        if (!in_array('0', $plants)) {
                            $query = $query->whereIn('id', $plants);
                            $query = $query->orWhere(function($query) use ($request){
                                if ($request->has('add')) {
                                    if ($request->query('add') != 'dc') {
                                        $query = $query->where('type', 1);
                                    } else {
                                        $query = $query->where('type', 2);
                                    }
                                }
                            });
                        }
                    }
                }
            });

            if ($request->has('pos')) {
                $query = $query->where('pos_id', $request->query('pos'));
            }

            if ($request->has('have_pos')) {
                if ($request->has('have_pos') == 'true') {
                    $query = $query->whereNotNull('pos_id');
                }
            }

        });

        $query = $query->where(function($query) use ($request){
            if ($request->has('search')) {
                $query->whereRaw("LOWER(initital) like '%" . strtolower($request->search) . "%'");
                $query->orWhereRaw("LOWER(short_name) like '%" . strtolower($request->search) . "%'");
            }
        });

        // must adding for select2

        if ($request->has('limit')) {
            $query->limit($request->limit);
        }

        if ($request->query('init') == 'false' && !$request->has('search')) {
            $data = [];
        } else {
            $data = $query->orderBy('code')->get();
        }

        if ($request->has('ext')) {
            if ($request->query('ext') == 'all') {
                if( !is_array($data) ){
                    $data->prepend(['id' => 0, 'text' => Lang::get('All')]);
                }
            }
        }

        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $plant = Plant::find($request->id);
        $plant->short_name = $request->short_name;
        $plant->description = $request->description;
        $plant->code = $request->code;
        $plant->initital = $request->initial;
        $plant->address = $request->address;
        $plant->email = $request->email;
        $plant->phone = $request->phone;
        $plant->cost_center = $request->cost_center;
        $plant->cost_center_desc = $request->cost_center_desc;
        $plant->customer_code = $request->customer_code;
        $plant->dc_id = $request->dc;
        $plant->area_id = $request->area;
        $plant->hours = $request->hours;
        $plant->drivethru = $request->drivethru;
        $plant->pos_id = $request->pos;
        $plant->price_category = $request->price_category;
        $plant->sloc_id_gi_plant = $request->sloc_id_gi_plant;
        $plant->sloc_id_gr = $request->sloc_id_gr;
        $plant->sloc_id_gr_vendor = $request->sloc_id_gr_vendor;
        $plant->sloc_id_waste = $request->sloc_id_waste;
        $plant->sloc_id_asset_mutation = $request->sloc_id_asset_mutation;
        $plant->sloc_id_current_stock = $request->sloc_id_current_stock;
        $plant->sloc_id_opname = $request->sloc_id_opname;
        if ($plant->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("plant")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("plant")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function sync(Request $request)
    {
        $stat = 'success';
        $msg = Lang::get("message.sync.success", ["data" => Lang::get("plant")]);

        $userAuth = $request->get('userAuth');

        $plantService = new PlantServiceSapImpl();
        $response = $plantService->syncPlant($userAuth->company_id_selected);
        if (!$response['status']) {
            $stat = $response['status'];
            $msg = $response['message'];
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    // utility
    public function getTypePlant($plant){
        return ($plant[0] != 'R') ? 'Outlet' : 'DC';
    }

    public function getInitialPlant($plant){
        return ($plant[0] != 'R') ? 'RF' : 'DC';
    }

    public function cleanInisialPlant($plant){
        return Str::of($plant)->replace('Richeese Factory ', '')->replace('Plant ', '')->replace('DC ', '')->replace('Richeese Factory', '');
    }
}
