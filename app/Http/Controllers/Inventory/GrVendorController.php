<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

use App\Library\Helper;

use App\Services\GrVendorServiceAppsImpl;
use App\Services\GrVendorServiceSapImpl;

use App\Models\Plant;
use App\Models\Configuration;
use App\Models\Inventory\GrVendor;

class GrVendorController extends Controller
{
    public function index(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $dataview = [
            'menu_id' => $request->query('menuid'),
            'mat_code_batch' => explode(',', str_replace(' ', '', Configuration::getValueCompByKeyFor($userAuth->company_id_selected, 'inventory', 'mat_code_batch')) ),
            'lock' => Configuration::getValueCompByKeyFor($userAuth->company_id_selected, 'inventory', 'lock_gi_gr')
        ];
        return view('inventory.gr-vendor', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('gr_vendors')
                ->where('gr_vendors.company_id', $userAuth->company_id_selected)
                ->leftJoin('plants', 'plants.id', '=', 'gr_vendors.plant_id')
                ->select([
                    'gr_vendors.id', 'gr_vendors.gr_number', 'gr_vendors.po_number', 'gr_vendors.ref_number', 'gr_vendors.po_date',
                    'gr_vendors.posting_date', 'gr_vendors.vendor_id', 'gr_vendors.vendor_name', 'gr_vendors.vendor_name',
                    'gr_vendors.material_code', 'gr_vendors.material_desc', 'gr_vendors.qty_po', 'gr_vendors.qty_remaining_po',
                    'gr_vendors.qty_gr', 'gr_vendors.qty_remaining', 'gr_vendors.batch', 'gr_vendors.uom', 'gr_vendors.recepient',
                    'plants.short_name', 'plants.initital'
                ]);

        if($request->has('plant-id') && $request->query('plant-id') != '0'){
            if($request->query('plant-id') != ''){
                $query = $query->where('gr_vendors.plant_id', $request->query('plant-id'));
            }
        }else {
            $plants_auth = Plant::getPlantsIdByUserId(Auth::id());
            $plants = explode(',', $plants_auth);
            if(!in_array('0', $plants)){
                $query = $query->whereIn('gr_vendors.plant_id', $plants);
            }
        }

        if($request->has('from') && $request->has('until')){
            if($request->query('from') != '' && $request->query('until') != ''){
                $query = $query->whereBetween('gr_vendors.posting_date', [$request->query('from'), $request->query('until')]);
            }
        }

        return Datatables::of($query)
                ->addIndexColumn()
                ->addColumn('plant_desc', function ($data) {
                    return $data->initital . ' ' . $data->short_name;
                })
                ->filterColumn('plant_desc', function ($query, $keyword) {
                    $sql = "plants.short_name like ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->make();
    }

    public function getOutstandingByPlantId($plant_id)
    {
        $grVendorService = new GrVendorServiceSapImpl();
        $response = $grVendorService->getOutstandingPoVendor($plant_id);
        $outstanding = $response['data'];

        return response()->json($outstanding);
    }

    public function store(Request $request)
    {
        $request->validate([
            'posting_date' => 'required',
            'ref_number' => 'required',
            'recepient' => 'required',
            'qty_gr' => 'required',
        ]);

        $stat = 'success';
        $msg = Lang::get("message.save.success", ["data" => Lang::get("gr po vendor")]);

        $userAuth = $request->get('userAuth');

        $grVendorService = new GrVendorServiceSapImpl();
        $response = $grVendorService->uploadGrVendor($userAuth->company_id_selected, $request);
        if (!$response['status']) {
            $stat = $response['status'];
            $msg = $response['message'];
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function preview($id){

        $qGrVendor = DB::table('gr_vendors')
                    ->where('id', $id);

        if( $qGrVendor->count() > 0 ){
            $grvendor = $qGrVendor->first();
            $dataview = [
                'grvendor' => $grvendor,
                'plant' => Plant::getShortNameById($grvendor->plant_id),
                'plant_address' => Plant::getAddressById($grvendor->plant_id)
            ];
            return view('inventory.gr-vendor-preview', $dataview)->render();
        } else {
            echo "<center><b>Data Not Found !</b></center>";
        }
    }

    public function fixMaterialCode(){

        $grvendors = DB::table('gr_vendors')
                    ->select('id', 'material_code')
                    ->get();

        foreach ($grvendors as $gr) {

            if( is_numeric($gr->material_code) ){
                $materialCode = $gr->material_code + 0;
            } else{
                $materialCode = $gr->material_code;
            }

            DB::table('gr_vendors')
                ->where('id', $gr->id)
                ->update([
                    'material_code' => $materialCode . ''
                ]);

        }

        !dd('done');
    }
}
