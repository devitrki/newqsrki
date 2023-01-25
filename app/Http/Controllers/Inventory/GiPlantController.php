<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Plant;
use App\Models\Configuration;
use App\Models\Inventory\GiPlant;
use App\Models\Inventory\GiPlantItem;

use App\Services\GiPlantServiceAppsImpl;
use App\Services\GiPlantServiceSapImpl;

class GiPlantController extends Controller
{
    public function index(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $dataview = [
            'menu_id' => $request->query('menuid'),
            'lock' => Configuration::getValueCompByKeyFor($userAuth->company_id_selected, 'inventory', 'lock_gi_gr')
        ];
        return view('inventory.gi-plant', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('gi_plants')
                    ->leftJoin('plants as issuing_plant', 'issuing_plant.id', '=', 'gi_plants.issuing_plant_id')
                    ->leftJoin('plants as receiving_plant', 'receiving_plant.id', '=', 'gi_plants.receiving_plant_id')
                    ->where('gi_plants.company_id', $userAuth->company_id_selected)
                    ->select(['gi_plants.id', 'gi_plants.document_number', 'gi_plants.document_posto', 'gi_plants.date',
                              'gi_plants.requester', 'gi_plants.issuer', 'issuing_plant.initital as issuing_plant_initital',
                              'issuing_plant.short_name as issuing_plant_name', 'receiving_plant.initital as receiving_plant_initital',
                              'receiving_plant.short_name as receiving_plant_name', 'gi_plants.issuing_plant_id', 'gi_plants.receiving_plant_id']);

        if($request->has('plant-id') && $request->query('plant-id') != '0'){
            if($request->query('plant-id') != ''){
                $query = $query->where('gi_plants.issuing_plant_id', $request->query('plant-id'));
            }
        }else {
            $plants_auth = Plant::getPlantsIdByUserId(Auth::id());
            $plants = explode(',', $plants_auth);
            if(!in_array('0', $plants)){
                $query = $query->whereIn('gi_plants.issuing_plant_id', $plants);
            }
        }

        if($request->has('from') && $request->has('until')){
            if($request->query('from') != '' && $request->query('until') != ''){
                $query = $query->whereBetween('gi_plants.date', [$request->query('from'), $request->query('until')]);
            }
        }

        return Datatables::of($query)
                ->addIndexColumn()
                ->addColumn('issuing_plant', function ($data) {
                    return $data->issuing_plant_initital . ' ' . $data->issuing_plant_name;
                })
                ->addColumn('receiving_plant', function ($data) {
                    return $data->receiving_plant_initital . ' ' . $data->receiving_plant_name;
                })
                ->make();
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required',
            'issuer' => 'required',
            'requester' => 'required',
            'issuing_plant' => 'required',
            'receiving_plant' => 'required',
        ]);

        $userAuth = $request->get('userAuth');

        DB::BeginTransaction();

        $success = false;

        $giPlant = new GiPlant;
        $giPlant->company_id = $userAuth->company_id_selected;
        $giPlant->date = $request->date;
        $giPlant->issuer = $request->issuer;
        $giPlant->requester = $request->requester;
        $giPlant->issuing_plant_id = $request->issuing_plant;
        $giPlant->receiving_plant_id = $request->receiving_plant;
        $giPlant->movement_type = '351';
        if ($giPlant->save()) {

            $materialId = json_decode($request->material_id, true);
            $qty = json_decode($request->qty, true);
            $uom = json_decode($request->uom, true);
            $note = json_decode($request->note, true);

            $insertItems = [];
            for ($i=0; $i < sizeof($materialId); $i++) {
                $material = DB::table('materials')->where('id', $materialId[$i])->first();
                $insertItems[] = [
                    'gi_plant_id' => $giPlant->id,
                    'material_id' => $material->id,
                    'uom' => $uom[$i],
                    'qty' => round( Helper::replaceDelimiterNumber($qty[$i]), 3),
                    'note' => $note[$i],
                ];
            }

            DB::table('gi_plant_items')->insert($insertItems);
            $success = true;

        }

        if ($success) {
            DB::commit();
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("gi plant")]);
        }else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("gi plant")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'required',
            'issuer' => 'required',
            'requester' => 'required',
            'issuing_plant' => 'required',
            'receiving_plant' => 'required',
        ]);

        $success = false;

        $giPlant = GiPlant::find($id);
        $giPlant->date = $request->date;
        $giPlant->issuer = $request->issuer;
        $giPlant->requester = $request->requester;
        $giPlant->issuing_plant_id = $request->issuing_plant;
        $giPlant->receiving_plant_id = $request->receiving_plant;
        if ($giPlant->save()) {

            DB::table('gi_plant_items')->where('gi_plant_id', $request->id)->delete();

            $materialId = json_decode($request->material_id, true);
            $qty = json_decode($request->qty, true);
            $uom = json_decode($request->uom, true);
            $note = json_decode($request->note, true);

            $insertItems = [];
            for ($i=0; $i < sizeof($materialId); $i++) {
                $material = DB::table('materials')->where('id', $materialId[$i])->first();
                $insertItems[] = [
                    'gi_plant_id' => $giPlant->id,
                    'material_id' => $material->id,
                    'uom' => $uom[$i],
                    'qty' => round( Helper::replaceDelimiterNumber($qty[$i]), 3),
                    'note' => $note[$i],
                ];
            }

            DB::table('gi_plant_items')->insert($insertItems);
            $success = true;

        }

        if ($success) {
            DB::commit();
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("gi plant")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("gi plant")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function getItemsById($id)
    {
        $items = DB::table('gi_plant_items')
                    ->leftJoin('materials', 'materials.id', '=', 'gi_plant_items.material_id')
                    ->where('gi_plant_id', $id)
                    ->select('gi_plant_items.*', 'materials.code', 'materials.description', 'materials.alternative_uom')
                    ->get();

        foreach ($items as $item) {

            $uoms = explode(',', $item->alternative_uom);
            $uom_alt = [];
            foreach ($uoms as $uom) {
                $uom_alt[] = ["id" => $uom, "text" => $uom];
            }

            $item->alternative_uom = $uom_alt;
            $item->qty = $item->qty + 0;
        }

        return response()->json($items->toArray());
    }

    public function destroy($id)
    {
        $deleted = false;
        $giPlant = GiPlant::find($id);
        if ($giPlant->delete()) {
            $giPlantItem = GiPlantItem::where('gi_plant_id', $id);
            if ($giPlantItem->delete()) {
                $deleted = true;
            }
        }

        if($deleted){
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("gi plant")]);
        }else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("gi plant")]);
        }
        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function uploadSap($id)
    {
        $stat = 'success';
        $msg = Lang::get("message.upload.success", ["data" => Lang::get("gi plant")]);

        $giPlantService = new GiPlantServiceSapImpl();
        $response = $giPlantService->uploadGiPlant($id);
        if (!$response['status']) {
            $stat = $response['status'];
            $msg = $response['message'];
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function preview($id)
    {
        $data_gi = GiPlant::getDataDetailById($id);
        return view('inventory.gi-plant-preview', $data_gi);
    }
}
