<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;

use App\Library\Helper;
use App\Models\AreaPlant;
use App\Models\MappingAreaPlant;

class AreaPlantController extends Controller
{
    public function index(Request $request)
    {
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('master.area-plant', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('area_plants')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(['id', 'name']);

        return Datatables::of($query)->addIndexColumn()->make();
    }

    public function dtbleList($id)
    {
        $query = DB::table('mapping_area_plants')
                    ->leftJoin('area_plants', 'area_plants.id', 'mapping_area_plants.area_plant_id')
                    ->leftJoin('plants', 'plants.id', 'mapping_area_plants.plant_id')
                    ->select(['mapping_area_plants.id', 'area_plants.name', 'plants.code', 'plants.description'])
                    ->where('area_plant_id', $id);

        return Datatables::of($query)->addIndexColumn()->make();
    }

    public function select(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('area_plants')
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
            'name' => 'required|unique:area_plants,name',
        ]);

        $userAuth = $request->get('userAuth');

        $areaPlant = new AreaPlant;
        $areaPlant->company_id = $userAuth->company_id_selected;
        $areaPlant->name = $request->name;
        if ($areaPlant->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("area plant")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("area plant")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function storeList(Request $request, $id)
    {
        $request->validate([
            'plant' => 'required',
        ]);

        // delete area plant id if exist
        $mappingAreaPlantOld = MappingAreaPlant::where('plant_id', $request->plant);
        if($mappingAreaPlantOld->count() > 0){
            $mappingAreaPlantOld->delete();
        }

        $mappingAreaPlant = new MappingAreaPlant;
        $mappingAreaPlant->plant_id = $request->plant;
        $mappingAreaPlant->area_plant_id = $request->area_plant_id;
        if ($mappingAreaPlant->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("list area plant")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("list area plant")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $areaPlant = AreaPlant::find($request->id);
        $areaPlant->name = $request->name;
        if ($areaPlant->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("area plant")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("area plant")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function destroy($id)
    {
        if (Helper::used($id, 'area_plant_id', ['mapping_area_plants'])) {
            return response()->json(Helper::resJSON('failed', Lang::get('validation.used')));
        }

        $areaPlant = AreaPlant::find($id);
        if ($areaPlant->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("area plant")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("area plant")]);
        }
        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function destroyList($id)
    {
        $mappingAreaPlant = MappingAreaPlant::find($id);
        if ($mappingAreaPlant->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("list area plant")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("list area plant")]);
        }
        return response()->json(Helper::resJSON($stat, $msg));
    }
}
