<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

use Yajra\DataTables\DataTables;

use App\Library\Helper;

use App\Models\Plant;
use App\Models\Configuration;

use App\Services\GrPlantServiceAppsImpl;
use App\Services\GrPlantServiceSapImpl;

use App\Models\Inventory\GrPlant;
use App\Models\Inventory\GrPlantItem;

class GrPlantController extends Controller
{
    public function index(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $first_plant_id = Plant::getFirstPlantIdSelect($userAuth->company_id_selected, 'all', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);
        $dataview = [
            'menu_id' => $request->query('menuid'),
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'lock' => Configuration::getValueCompByKeyFor($userAuth->company_id_selected, 'inventory', 'lock_gi_gr')
        ];
        return view('inventory.gr-plant', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('gr_plants')
                    ->leftJoin('plants as issuing_plant', 'issuing_plant.id', '=', 'gr_plants.issuing_plant_id')
                    ->leftJoin('plants as receiving_plant', 'receiving_plant.id', '=', 'gr_plants.receiving_plant_id')
                    ->where('gr_plants.company_id', $userAuth->company_id_selected)
                    ->select([
                        'gr_plants.id', 'gr_plants.document_number', 'gr_plants.delivery_number', 'gr_plants.posto_number', 'gr_plants.date',
                        'gr_plants.recepient', 'gr_plants.gr_from', 'issuing_plant.initital as issuing_plant_initital',
                        'issuing_plant.short_name as issuing_plant_name', 'receiving_plant.initital as receiving_plant_initital',
                        'receiving_plant.short_name as receiving_plant_name', 'gr_plants.issuing_plant_id', 'gr_plants.receiving_plant_id'
                    ]);

        if($request->has('plant-id') && $request->query('plant-id') != '0'){
            if($request->query('plant-id') != ''){
                $query = $query->where('gr_plants.receiving_plant_id', $request->query('plant-id'));
            }
        }else {
            $plants_auth = Plant::getPlantsIdByUserId(Auth::id());
            $plants = explode(',', $plants_auth);
            if(!in_array('0', $plants)){
                $query = $query->whereIn('gr_plants.receiving_plant_id', $plants);
            }
        }

        if($request->has('from') && $request->has('until')){
            if($request->query('from') != '' && $request->query('until') != ''){
                $query = $query->whereBetween('gr_plants.date', [$request->query('from'), $request->query('until')]);
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
            ->addColumn('date_desc', function ($data) {
                return date("d-m-Y", strtotime($data->date));
            })
            ->orderColumn('date_desc', function ($query, $order) {
                $query->orderBy('date', $order);
            })
            ->make();
    }

    public function dtbleOutstandingByPlantId($plant_id)
    {
        $grPlantService = new GrPlantServiceSapImpl();
        $response = $grPlantService->getOutstandingPoPlant($plant_id);
        $outstanding = $response['data'];

        return Datatables::of($outstanding)->make();
    }

    public function getOutstandingByPlantId($plant_id)
    {
        $grPlantService = new GrPlantServiceSapImpl();
        $response = $grPlantService->getOutstandingPoPlant($plant_id);
        $outstanding = $response['data'];

        return response()->json($outstanding);
    }

    public function getOutstandingDetailByDocNumber($plant_code, $doc_number)
    {
        $grPlantService = new GrPlantServiceSapImpl();
        $response = $grPlantService->getOutstandingGr($plant_code, $doc_number);
        $detail_outstanding = $response['data'];

        return response()->json($detail_outstanding);
    }

    public function store(Request $request)
    {
        $request->validate([
            'gi_date' => 'required',
            'gi_number' => 'required',
            'plant_from' => 'required',
            'plant_to' => 'required',
            'po_number' => 'required',
            'receive_date' => 'required',
            'recepient' => 'required',
            'material_gr' => 'required',
        ]);

        $stat = 'success';
        $msg = Lang::get("message.save.success", ["data" => Lang::get("gr plant")]);

        $userAuth = $request->get('userAuth');

        $grPlantService = new GrPlantServiceSapImpl();
        $response = $grPlantService->uploadGrPlant($userAuth->company_id_selected, $request);
        if (!$response['status']) {
            $stat = $response['status'];
            $msg = $response['message'];
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function preview($id)
    {
        $data_gi = GrPlant::getDataDetailById($id);
        if( isset($data_gi['header']) ){
            return view('inventory.gr-plant-preview', $data_gi);
        } else {
            echo "<center><b>Data Not Found !</b></center>";
        }
    }
}
