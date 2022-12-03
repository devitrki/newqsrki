<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\RegionalPlant;
use App\Models\MappingRegionalArea;

class RegionalPlantController extends Controller
{
    public function index(Request $request)
    {
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('master.regional-plant', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('regional_plants')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(['id', 'name']);
        return Datatables::of($query)->addIndexColumn()->make();
    }

    public function dtbleList($id)
    {
        $query = DB::table('mapping_regional_areas')
                    ->leftJoin('regional_plants', 'regional_plants.id', 'mapping_regional_areas.regional_plant_id')
                    ->leftJoin('area_plants', 'area_plants.id', 'mapping_regional_areas.area_plant_id')
                    ->select(['mapping_regional_areas.id', 'regional_plants.name', 'area_plants.name as area'])
                    ->where('regional_plant_id', $id);
        return Datatables::of($query)->addIndexColumn()->make();
    }

    public function select(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('regional_plants')
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
            'name' => 'required|unique:regional_plants,name',
        ]);

        $userAuth = $request->get('userAuth');

        $regionalPlant = new RegionalPlant;
        $regionalPlant->company_id = $userAuth->company_id_selected;
        $regionalPlant->name = $request->name;
        if ($regionalPlant->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("regional plant")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("regional plant")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function storeList(Request $request, $id)
    {
        $request->validate([
            'area' => 'required',
        ]);

        // delete area id if exist
        $mappingRegionalAreaOld = MappingRegionalArea::where('area_plant_id', $request->area);
        if( $mappingRegionalAreaOld->count() > 0 ){
            $mappingRegionalAreaOld->delete();
        }

        $mappingRegionalArea = new MappingRegionalArea;
        $mappingRegionalArea->area_plant_id = $request->area;
        $mappingRegionalArea->regional_plant_id = $request->regional_plant_id;
        if ($mappingRegionalArea->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("list regional area")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("list regional area")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $regionalPlant = RegionalPlant::find($request->id);
        $regionalPlant->name = $request->name;
        if ($regionalPlant->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("regional plant")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("regional plant")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function destroy($id)
    {
        if (Helper::used($id, 'regional_plant_id', ['mapping_regional_areas'])) {
            return response()->json(Helper::resJSON('failed', Lang::get('validation.used')));
        }

        $regionalPlant = RegionalPlant::find($id);
        if ($regionalPlant->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("regional plant")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("regional plant")]);
        }
        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function destroyList($id)
    {
        $mappingRegionalArea = MappingRegionalArea::find($id);
        if ($mappingRegionalArea->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("list regional area")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("list regional area")]);
        }
        return response()->json(Helper::resJSON($stat, $msg));
    }
}
