<?php

namespace App\Http\Controllers\Financeacc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use App\Library\Helper;
use Yajra\DataTables\DataTables;

use App\Mail\Financeacc\Assets\NotificationMutation;
use App\Mail\Financeacc\Assets\NotificationRequestMutation;

use App\Models\User;
use App\Models\Plant;
use App\Models\Company;
use App\Models\Configuration;
use App\Models\Financeacc\Asset;
use App\Models\Financeacc\AssetMutation;
use App\Models\Financeacc\AssetRequestMutation;
use App\Models\Financeacc\AssetAdminDepart;

use App\Services\AssetServiceAppsImpl;
use App\Services\AssetServiceSapImpl;

use App\Rules\CheckAmPlant;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $userAuth = $request->get('userAuth');

        // get first plant user
        $user = User::find(Auth::id());
        $plantUser = 'all';

        if($user->hasRole('store manager')){
            $first_plant_id = Plant::getFirstPlantIdSelect($userAuth->company_id_selected, 'all', true);
            $first_plant_name = Plant::getShortNameById($first_plant_id);

            // check dc / outlet
            $plantAuth = Plant::getPlantsIdByUserId(Auth::id());
            $plantAuths = explode(',' , $plantAuth);
            if( sizeof($plantAuths) == 1 ){
                $plantUser = Plant::getTypeByPlantId($plantAuths[0]);
            }

        } else {
            $first_plant_id = Plant::getFirstPlantIdSelect($userAuth->company_id_selected, 'all', true);
            $first_plant_name = Plant::getShortNameById($first_plant_id);
        }

        $mutation = false;
        $statusAssetSoOutlet = Configuration::getValueCompByKeyFor($userAuth->company_id_selected, 'financeacc', 'status_outlet_asset_so');
        $statusAssetSoDC = Configuration::getValueCompByKeyFor($userAuth->company_id_selected, 'financeacc', 'status_dc_asset_so');

        if ($statusAssetSoOutlet != 'Running' && $statusAssetSoDC != 'Running') {
            $mutation = true;
        }

        $dataview = [
            'menu_id' => $request->query('menuid'),
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'first_cost_center_code' => Asset::getCostCenterCodeByPlantBy($first_plant_id),
            'mutation' => $mutation,
            'plant_user' => $plantUser,
        ];

        return view('financeacc.asset-list', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $query = DB::table('assets')
                    ->select('assets.*', 'plants.initital', 'plants.short_name', 'plants.code')
                    ->leftJoin('plants', 'plants.id', '=', 'assets.plant_id');

        if ($request->has('plant_id')) {
            $query = $query->where('plant_id', $request->query('plant_id'));
        }
        if ($request->has('cost_center_code')) {
            $query = $query->where('cost_center_code', $request->query('cost_center_code'));
        }

        return Datatables::of($query)
                ->addIndexColumn()
                ->addColumn('plant', function ($data) {
                    return $data->initital . ' ' . $data->short_name;
                })
                ->addColumn('plant_code', function ($data) {
                    return $data->code;
                })
                ->addColumn('cost_center_desc', function ($data) {
                    return $data->cost_center_code . ' - ' . $data->cost_center;
                })
                ->addColumn('status_mutation', function ($data) {
                    return AssetMutation::getStatusMutationAssetByAssetNumber($data->number, $data->number_sub);
                })
                ->addColumn('status_mutation_desc', function ($data) {
                    $statusMutation = AssetMutation::getStatusMutationAssetByAssetNumber($data->number, $data->number_sub);

                    if( in_array($statusMutation, [1, 3, 5, 7, 9, 11])){
                        if($statusMutation >= 11){
                            $status = "<div class = 'badge badge-success'>Waiting Approval</div>";
                        } else {
                            $status = "<div class = 'badge badge-warning'>Waiting Approval</div>";
                        }
                    } else {
                        $status = "<div class = 'badge badge-info'>Available</div>";
                    }
                    return $status;
                })
                ->rawColumns(['status_mutation_desc'])
                ->make();
    }

    public function selectCostCenter(Request $request, $plant_id)
    {
        $query = DB::table('assets')
                        ->select(['cost_center_code as id', 'cost_center as text'])
                        ->where('plant_id', $plant_id);

        $typePlant = Plant::getTypeByPlantId($plant_id);

        if ($request->has('cc_code') && $typePlant != 'Outlet') {
            $query = $query->orWhere('cost_center_code', $request->query('cc_code'));
        }

        $costCenters = $query->distinct()->get();

        return response()->json($costCenters);
    }

    public function sync(Request $request)
    {
        $request->validate([
            'plant' => 'required|exists:plants,id',
        ]);

        $stat = 'success';
        $msg = Lang::get("message.sync.success", ["data" => Lang::get("asset")]);

        $assetService = new AssetServiceSapImpl();
        $response = $assetService->syncAsset($request->plant);

        if (!$response['status']) {
            $stat = $response['status'];
            $msg = $response['message'];
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function checkAssetMutasi(Request $request)
    {
        $count = DB::table('asset_mutations')
                    ->select('id')
                    ->where('from_plant_id', $request->plant_id)
                    ->where('number', $request->number)
                    ->where('number_sub', $request->number_sub)
                    ->count();
        return response()->json($count);
    }

    public function store(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $request->validate([
            'plant_receiver' => ['required', new CheckAmPlant($userAuth->company_id_selected)],
            'plant_sender' => ['required', new CheckAmPlant($userAuth->company_id_selected)],
            'cost_center_receiver' => 'required',
            'qty_mutation' => 'required',
            'est_send_date' => 'required',
            'validator' => 'required',
        ]);

        $plantCodeHO = Company::getConfigByKey($userAuth->company_id_selected, 'PLANT_CODE_HO');
        if (!$plantCodeHO || $plantCodeHO == '') {
            return false;
        }
        $plantCodeHO = explode(',', $plantCodeHO);

        // check level approve boss (first)
        $user = User::find(Auth::id());
        $typePlantFrom = Plant::getTypeByPlantId($request->plant_sender);
        $codePlantFrom = Plant::getCodeById($request->plant_sender);
        $typePlantTo = Plant::getTypeByPlantId($request->plant_receiver);
        $codePlantTo = Plant::getCodeById($request->plant_receiver);
        $amSenderId = Plant::getAMIdPlantById($request->plant_sender);
        $amReceiverId = Plant::getAMIdPlantById($request->plant_receiver);

        $requestor = '';
        $levelRequestFirst = '';
        $levelRequestFirstId = '';
        $levelRequestSecond = '';
        $levelRequestSecondId = '';
        $levelRequestThird = '';
        $levelRequestThirdId = '0';

        $senderCostCenter = '';
        $senderCostCenterId = '0';
        $receiverCostCenter = '';
        $receiverCostCenterId = '0';

        if($user->hasRole('area manager')){
            $rmID = plant::getRMIdByAm($user->id);

            if( $rmID == '0' ){
                $stat = 'failed';
                $msg = Lang::get("Your's RM Not Yet Mapping.");
                return response()->json(Helper::resJSON($stat, $msg));
            }

            if( $amSenderId == $amReceiverId ){
                $requestor = 'AM Receiver';
                $levelRequestFirst = "RM Receiver";
            } else {
                if($user->id == $amSenderId){
                    $requestor = 'AM Sender';
                    $levelRequestFirst = "RM Sender";
                } else {
                    $requestor = 'AM Receiver';
                    $levelRequestFirst = "RM Receiver";
                }
            }

            $levelRequestFirstId = $rmID;
        } else {
            // if depart check HOD
            $departmentId = User::getDepartmentIdById($user->id);
            $hodId = User::getHodIdByDepartmentId($departmentId);
            if( $hodId == '0' ){
                $stat = 'failed';
                $msg = Lang::get("Your's HOD Not Yet Mapping.");
                return response()->json(Helper::resJSON($stat, $msg));
            }
            if( $typePlantFrom != 'DC' ){
                $stat = 'failed';
                $msg = Lang::get("Admin department only request transfer asset from DC.");
                return response()->json(Helper::resJSON($stat, $msg));
            }

            $requestor = 'Admin Department'; // back office
            $levelRequestFirst = 'HOD';
            $levelRequestFirstId = $hodId; // approval id back office (HOD)
        }

        // additional validation
        // 1. Outlet -> DC
        if( $typePlantFrom == 'Outlet' && $typePlantTo == 'DC' ){

            // flag receive is HO
            if( in_array($codePlantTo, $plantCodeHO) ){
                // check bahwa requestor harus AM Pengirim / AM Outlet
                if($requestor != 'AM Sender'){
                    $stat = 'failed';
                    $msg = Lang::get("Asset transfer from outlet to HO, request must be AM outlet.");
                    return response()->json(Helper::resJSON($stat, $msg));
                }

                $adminDepartReceiver = AssetAdminDepart::getAdminDepart($request->plant_receiver, $request->cost_center_code_receiver);
                $hodDepartReceiver = AssetAdminDepart::getHODDepart($request->plant_receiver, $request->cost_center_code_receiver);

                if( $adminDepartReceiver == '0' ){
                    $stat = 'failed';
                    $msg = Lang::get("Admin department receiver not yet mapping.");
                    return response()->json(Helper::resJSON($stat, $msg));
                }

                if( $hodDepartReceiver == '0' ){
                    $stat = 'failed';
                    $msg = Lang::get("HOD department receiver not yet mapping.");
                    return response()->json(Helper::resJSON($stat, $msg));
                }

                $levelRequestSecond = 'HOD Receiver';
                $levelRequestSecondId = $hodDepartReceiver;

                $receiverCostCenter = 'Admin Department Receiver';
                $receiverCostCenterId = $adminDepartReceiver;

            } else {
                // check bahwa requestor harus AM Pengirim / AM Outlet
                if($requestor != 'AM Sender'){
                    $stat = 'failed';
                    $msg = Lang::get("Asset transfer from outlet to DC, request must be AM outlet.");
                    return response()->json(Helper::resJSON($stat, $msg));
                }

                $levelRequestSecond = 'SPV DC Receiver';
                $levelRequestSecondId = $amReceiverId;
            }

        }

        // 2. Outlet -> Outlet
        if( $typePlantFrom == 'Outlet' && $typePlantTo == 'Outlet' ){

            // check mutasi harus beda plant
            if($request->plant_sender == $request->plant_receiver){
                $stat = 'failed';
                $msg = Lang::get("Asset transfer from outlet to outlet, request must different outlet");
                return response()->json(Helper::resJSON($stat, $msg));
            }

            // check bahwa requestor harus AM Penerima
            if($requestor != 'AM Receiver'){
                $stat = 'failed';
                $msg = Lang::get("Asset transfer from outlet to outlet, request must be AM receiver.");
                return response()->json(Helper::resJSON($stat, $msg));
            }

            $levelRequestSecond = 'AM Sender';
            $levelRequestSecondId = $amSenderId;
        }

        if( $typePlantFrom == 'DC' && $typePlantTo == 'DC'){
            if( !in_array($codePlantFrom, $plantCodeHO) && !in_array($codePlantTo, $plantCodeHO) ){
                // DC -> DC

                // check bahwa requestor harus AM Penerima
                if($requestor != 'AM Sender'){
                    $stat = 'failed';
                    $msg = Lang::get("Asset transfer from DC to DC, request must be SPV DC sender.");
                    return response()->json(Helper::resJSON($stat, $msg));
                }
                $requestor = 'SPV DC Sender';
                $levelRequestSecond = 'SPV DC Receiver';
                $levelRequestSecondId = $amReceiverId;

            } else {

                $adminDepartSender = AssetAdminDepart::getAdminDepart($request->plant_sender, $request->cost_center_code);
                $hodDepartSender = AssetAdminDepart::getHODDepart($request->plant_sender, $request->cost_center_code);
                $adminDepartReceiver = AssetAdminDepart::getAdminDepart($request->plant_receiver, $request->cost_center_code_receiver);
                $hodDepartReceiver = AssetAdminDepart::getHODDepart($request->plant_receiver, $request->cost_center_code_receiver);

                if( in_array($codePlantFrom, $plantCodeHO) && !in_array($codePlantTo, $plantCodeHO)){
                    // HO -> DC

                    if( $adminDepartSender == '0' ){
                        $stat = 'failed';
                        $msg = Lang::get("Admin department sender not yet mapping.");
                        return response()->json(Helper::resJSON($stat, $msg));
                    }

                    if( $hodDepartSender == '0' ){
                        $stat = 'failed';
                        $msg = Lang::get("HOD department sender not yet mapping.");
                        return response()->json(Helper::resJSON($stat, $msg));
                    }

                    // requestor must admin depart sender
                    if( $adminDepartSender != $user->id ){
                        $stat = 'failed';
                        $msg = Lang::get("Request asset transfer from HO to DC must admin department sender.");
                        return response()->json(Helper::resJSON($stat, $msg));
                    }

                    $requestor = 'Admin Department Sender';
                    $levelRequestFirst = 'HOD Sender';
                    $levelRequestFirstId = $hodDepartSender;
                    $levelRequestSecond = 'SPV DC Receiver';
                    $levelRequestSecondId = $amReceiverId;

                    $senderCostCenter = 'Admin Department Sender';
                    $senderCostCenterId = $adminDepartSender;

                } else if( !in_array($codePlantFrom, $plantCodeHO) && in_array($codePlantTo, $plantCodeHO)){
                    // DC -> HO
                    if( $adminDepartReceiver == '0' ){
                        $stat = 'failed';
                        $msg = Lang::get("Admin department receiver not yet mapping.");
                        return response()->json(Helper::resJSON($stat, $msg));
                    }

                    if( $hodDepartReceiver == '0' ){
                        $stat = 'failed';
                        $msg = Lang::get("HOD department receiver not yet mapping.");
                        return response()->json(Helper::resJSON($stat, $msg));
                    }

                    // requestor must admin depart receiver
                    if( $adminDepartReceiver != $user->id ){
                        $stat = 'failed';
                        $msg = Lang::get("Request asset transfer from DC to HO must admin department receiver.");
                        return response()->json(Helper::resJSON($stat, $msg));
                    }

                    $requestor = 'Admin Department Receiver';
                    $levelRequestFirst = 'HOD Receiver';
                    $levelRequestFirstId = $hodDepartReceiver;
                    $levelRequestSecond = 'SPV DC Sender';
                    $levelRequestSecondId = $amSenderId;

                    $receiverCostCenter = 'Admin Department Receiver';
                    $receiverCostCenterId = $adminDepartReceiver;

                } else {
                    // HO -> HO

                    // check cost center not same
                    if($request->cost_center_receiver == $request->cost_center){
                        $stat = 'failed';
                        $msg = Lang::get("Asset transfer from cost center to cost center, must different.");
                        return response()->json(Helper::resJSON($stat, $msg));
                    }

                    if( $adminDepartSender == '0' ){
                        $stat = 'failed';
                        $msg = Lang::get("Admin department sender not yet mapping.");
                        return response()->json(Helper::resJSON($stat, $msg));
                    }

                    if( $hodDepartSender == '0' ){
                        $stat = 'failed';
                        $msg = Lang::get("HOD department sender not yet mapping.");
                        return response()->json(Helper::resJSON($stat, $msg));
                    }

                    if( $adminDepartReceiver == '0' ){
                        $stat = 'failed';
                        $msg = Lang::get("Admin department receiver not yet mapping.");
                        return response()->json(Helper::resJSON($stat, $msg));
                    }

                    if( $hodDepartReceiver == '0' ){
                        $stat = 'failed';
                        $msg = Lang::get("HOD department receiver not yet mapping.");
                        return response()->json(Helper::resJSON($stat, $msg));
                    }

                    // requestor must admin depart receiver
                    if( $adminDepartReceiver != $user->id ){
                        $stat = 'failed';
                        $msg = Lang::get("Request asset transfer from cost center to cost center must admin department receiver.");
                        return response()->json(Helper::resJSON($stat, $msg));
                    }

                    $requestor = 'Admin Department Receiver';
                    $levelRequestFirst = 'HOD Receiver';
                    $levelRequestFirstId = $hodDepartReceiver;
                    $levelRequestSecond = 'HOD Sender';
                    $levelRequestSecondId = $hodDepartSender;

                    $senderCostCenter = 'Admin Department Sender';
                    $senderCostCenterId = $adminDepartSender;
                    $receiverCostCenter = 'Admin Department Receiver';
                    $receiverCostCenterId = $adminDepartReceiver;
                }
            }

        }

        // 5. DC -> Outlet
        if( $typePlantFrom == 'DC' && $typePlantTo == 'Outlet'){

            // flag if send from HO
            if( in_array($codePlantFrom, $plantCodeHO)){

                if( !in_array($requestor, ['Admin Department', 'AM Receiver']) ){
                    $stat = 'failed';
                    $msg = Lang::get("Request asset transfer from HO must admin department or AM Receiver.");
                    return response()->json(Helper::resJSON($stat, $msg));
                }

                // requestor must be related cost center admin
                $adminDepartSender = AssetAdminDepart::getAdminDepart($request->plant_sender, $request->cost_center_code);
                $hodDepartSender = AssetAdminDepart::getHODDepart($request->plant_sender, $request->cost_center_code);

                if( $adminDepartSender == '0' ){
                    $stat = 'failed';
                    $msg = Lang::get("Admin department sender not yet mapping.");
                    return response()->json(Helper::resJSON($stat, $msg));
                }

                if( $hodDepartSender == '0' ){
                    $stat = 'failed';
                    $msg = Lang::get("HOD department sender not yet mapping.");
                    return response()->json(Helper::resJSON($stat, $msg));
                }

                if( $requestor == 'Admin Department' ){
                    if( $adminDepartSender != $user->id ){
                        $stat = 'failed';
                        $msg = Lang::get("Request asset transfer from HO to outlet must admin department sender.");
                        return response()->json(Helper::resJSON($stat, $msg));
                    }

                    $requestor = 'Admin Department Sender';
                    $levelRequestFirst = 'HOD Sender';
                    $levelRequestFirstId = $hodDepartSender;
                    $levelRequestSecond = 'AM Receiver';
                    $levelRequestSecondId = $amReceiverId;

                    $senderCostCenter = 'Admin Department Sender';
                    $senderCostCenterId = $adminDepartSender;

                } else {

                    $levelRequestSecond = 'HOD Sender';
                    $levelRequestSecondId = $hodDepartSender;
                    $senderCostCenter = 'Admin Department Sender';
                    $senderCostCenterId = $adminDepartSender;

                }

            } else {
                // am receiver = am penerima, am sender = spv dc
                if($requestor == 'Admin Department'){
                    $levelRequestSecond = 'AM Receiver';
                    $levelRequestSecondId = $amReceiverId;
                    $levelRequestThird = 'SPV DC Sender';
                    $levelRequestThirdId = $amSenderId;
                } else if($requestor == 'AM Receiver'){
                    $levelRequestSecond = 'SPV DC Sender';
                    $levelRequestSecondId = $amSenderId;
                } else {
                    $requestor = 'SPV DC Sender';
                    $levelRequestSecond = 'AM Receiver';
                    $levelRequestSecondId = $amReceiverId;
                }
            }

        }

        $asset = DB::table('assets')
                    ->where('plant_id', $request->plant_id)
                    ->where('cost_center_code', $request->cost_center_code)
                    ->where('number', $request->number)
                    ->where('number_sub', $request->number_sub)
                    ->first();

        $assetMutation = new AssetMutation;
        $assetMutation->company_id = $asset->company_id;
        $assetMutation->number = $asset->number;
        $assetMutation->number_sub = $asset->number_sub;
        $assetMutation->description = $asset->description;
        $assetMutation->spec_user = $asset->spec_user;
        $assetMutation->qty_web = $asset->qty_web;
        $assetMutation->qty_mutation = $request->qty_mutation;
        $assetMutation->uom = $asset->uom;

        $assetMutation->req_number = $asset->number;
        $assetMutation->req_number_sub = $asset->number_sub;
        $assetMutation->req_description = $asset->description;
        $assetMutation->req_spec_user = $asset->spec_user;
        $assetMutation->req_qty_web = $asset->qty_web;
        $assetMutation->req_qty_mutation = $request->qty_mutation;
        $assetMutation->req_uom = $asset->uom;
        $assetMutation->req_remark = $asset->remark;

        $assetMutation->from_plant_id = $asset->plant_id;
        $assetMutation->from_cost_center = $asset->cost_center;
        $assetMutation->from_cost_center_code = $asset->cost_center_code;
        $assetMutation->to_plant_id = $request->plant_receiver;
        $assetMutation->to_cost_center = $request->cost_center_receiver;
        $assetMutation->to_cost_center_code = $request->cost_center_code_receiver;
        $assetMutation->date_send_est = $request->est_send_date;
        $assetMutation->date_request = \Carbon\Carbon::now();

        $assetMutation->user_id = Auth::id();
        $assetMutation->asset_validator_id = $request->validator;
        $assetMutation->note_request = $request->note_request;

        $assetMutation->requestor = $requestor;
        $assetMutation->level_request_first = $levelRequestFirst;
        $assetMutation->level_request_first_id = $levelRequestFirstId;
        $assetMutation->level_request_second = $levelRequestSecond;
        $assetMutation->level_request_second_id = $levelRequestSecondId;
        $assetMutation->level_request_third = $levelRequestThird;
        $assetMutation->level_request_third_id = $levelRequestThirdId;

        $assetMutation->sender_cost_center = $senderCostCenter;
        $assetMutation->sender_cost_center_id = $senderCostCenterId;
        $assetMutation->receiver_cost_center = $receiverCostCenter;
        $assetMutation->receiver_cost_center_id = $receiverCostCenterId;

        $assetMutation->status_mutation = 1;
        $assetMutation->status_mutation_desc = 'Request By User';

        if ($assetMutation->save()) {

            // send email
            Mail::queue(new NotificationMutation($assetMutation->id));

            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("asset transfer")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("asset transfer")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function storeRequest(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $request->validate([
            'plant_sender' => ['required', new CheckAmPlant($userAuth->company_id_selected)],
            'plant_receiver' => ['required', new CheckAmPlant($userAuth->company_id_selected)],
            'cost_center_receiver' => 'required',
            'validator' => 'required',
            'qty_mutation' => 'required',
        ]);

        // if am check RM
        $user = User::find(Auth::id());


        if($user->hasRole('area manager')){
            $rmID = plant::getRMIdByAm($user->id);
            if( $rmID == '0' ){
                $stat = 'failed';
                $msg = Lang::get("Your's RM Not Yet Mapping.");
                return response()->json(Helper::resJSON($stat, $msg));
            }

            $levelRequest = 'am'; //area manager
            $levelRequestId = $rmID; // approval id am (RM)
        } else {
            // if depart check SOD
            $departmentId = User::getDepartmentIdById($user->id);
            $hodId = User::getHodIdByDepartmentId($departmentId);
            if( $hodId == '0' ){
                $stat = 'failed';
                $msg = Lang::get("Your's HOD Not Yet Mapping.");
                return response()->json(Helper::resJSON($stat, $msg));
            }
            $levelRequest = 'bo'; // back office
            $levelRequestId = $hodId; // approval id back office (HOD)
        }

        $asset = DB::table('assets')
                    ->where('plant_id', $request->plant_id)
                    ->where('cost_center_code', $request->cost_center_code)
                    ->where('number', $request->number)
                    ->where('number_sub', $request->number_sub)
                    ->first();

        $assetRequestMutation = new AssetRequestMutation;
        $assetRequestMutation->company_id = $asset->company_id;
        $assetRequestMutation->number = $asset->number;
        $assetRequestMutation->number_sub = $asset->number_sub;
        $assetRequestMutation->description = $asset->description;
        $assetRequestMutation->spec_user = $asset->spec_user;
        $assetRequestMutation->qty_web = $asset->qty_web;
        $assetRequestMutation->qty_mutation = $request->qty_mutation;
        $assetRequestMutation->uom = $asset->uom;
        $assetRequestMutation->remark = $asset->remark;

        $assetRequestMutation->req_number = $asset->number;
        $assetRequestMutation->req_number_sub = $asset->number_sub;
        $assetRequestMutation->req_description = $asset->description;
        $assetRequestMutation->req_spec_user = $asset->spec_user;
        $assetRequestMutation->req_qty_web = $asset->qty_web;
        $assetRequestMutation->req_qty_mutation = $request->qty_mutation;
        $assetRequestMutation->req_uom = $asset->uom;
        $assetRequestMutation->req_remark = $asset->remark;

        $assetRequestMutation->from_plant_id = $asset->plant_id;
        $assetRequestMutation->from_cost_center = $asset->cost_center;
        $assetRequestMutation->from_cost_center_code = $asset->cost_center_code;
        $assetRequestMutation->to_plant_id = $request->plant_receiver;
        $assetRequestMutation->to_cost_center = $request->cost_center_receiver;
        $assetRequestMutation->to_cost_center_code = $request->cost_center_code_receiver;
        $assetRequestMutation->date_submit = \Carbon\Carbon::now();
        $assetRequestMutation->step_request_desc = 'Request by User';

        $assetRequestMutation->user_id = Auth::id();
        $assetRequestMutation->asset_validator_id = $request->validator;
        $assetRequestMutation->note_request = $request->note_request;
        $assetRequestMutation->level_request = $levelRequest;
        $assetRequestMutation->level_request_id = $levelRequestId;

        if ($assetRequestMutation->save()) {

            // send email notification
            Mail::queue(new NotificationRequestMutation($assetRequestMutation->id));

            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("request asset transfer")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("request asset transfer")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function checkAssetMutation(Request $request)
    {
        $assetMutation = DB::table('asset_mutations')
                            ->where('number', $request->number)
                            ->where('number_sub', $request->number_sub)
                            ->where('from_plant_id', $request->plant_id)
                            ->where('status', 0); // flag 0 = progress mutation
        $check = false;
        if($assetMutation->count() > 0){
            $check = true;
        }

        $data = [
            'check' => $check
        ];

        return response()->json(Helper::resJSON('success', 'success', $data));
    }

    public function checkRequestAssetMutation(Request $request)
    {
        $assetMutation = DB::table('asset_request_mutations')
                            ->where('number', $request->number)
                            ->where('number_sub', $request->number_sub)
                            ->where('from_plant_id', $request->plant_id)
                            ->where('status', 0); // flag 0 = progress mutation
        $check = false;
        if($assetMutation->count() > 0){
            $check = true;
        }

        $data = [
            'check' => $check
        ];

        return response()->json(Helper::resJSON('success', 'success', $data));
    }

    public function cancelAssetMutation(Request $request)
    {
        $assetMutation = AssetMutation::where('number', $request->number)
                            ->where('number_sub', $request->number_sub)
                            ->where('from_plant_id', $request->plant_id)
                            ->where('status', 0)
                            ->first();

        // flag to finish
        $assetMutation->status = 1;
        $assetMutation->status_mutation = 2;
        $assetMutation->status_mutation_desc = 'Cancel By User';
        $assetMutation->date_cancel_request = \Carbon\Carbon::now();

        if($assetMutation->save()){
            // send email
            Mail::queue(new NotificationMutation($assetMutation->id));

            $stat = 'success';
            $msg = Lang::get("message.cancel.success", ["data" => Lang::get("asset transfer")]);
        }else {
            $stat = 'failed';
            $msg = Lang::get("message.cancel.failed", ["data" => Lang::get("asset transfer")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function cancelRequestAssetMutation(Request $request)
    {
        $assetRequestMutation = AssetRequestMutation::where('number', $request->number)
                                                        ->where('number_sub', $request->number_sub)
                                                        ->where('from_plant_id', $request->plant_id)
                                                        ->where('status', 0)
                                                        ->first();

        // flag to finish
        $assetRequestMutation->status = 1;
        $assetRequestMutation->step_request = 2;
        $assetRequestMutation->step_request_desc = 'Cancel By User';
        $assetRequestMutation->date_cancel = \Carbon\Carbon::now();

        if($assetRequestMutation->save()){
            $stat = 'success';
            $msg = Lang::get("message.cancel.success", ["data" => Lang::get("request asset transfer")]);
        }else {
            $stat = 'failed';
            $msg = Lang::get("message.cancel.failed", ["data" => Lang::get("request asset transfer")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

}
