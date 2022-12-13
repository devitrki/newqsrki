<?php

namespace App\Http\Controllers\Financeacc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Lang;
use App\Library\Helper;
use Yajra\DataTables\DataTables;

use App\Mail\Financeacc\Assets\NotificationMutation;
use App\Jobs\Financeacc\CheckChangeAssetSap;

use App\Services\AssetServiceAppsImpl;
use App\Services\AssetServiceSapImpl;

use App\Models\Financeacc\AssetMutation;
use App\Models\Financeacc\AssetValidator;
use App\Models\Plant;
use App\Models\User;
use App\Models\Configuration;

class AssetMutationController extends Controller
{
    public function index(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $first_plant_id = Plant::getFirstPlantIdSelect($userAuth->company_id_selected, 'all', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $user = User::find(Auth::id());
        $position = User::getPositionById($user->id);
        $userValidators = AssetValidator::getValidatorByUserId($user->id);

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
            'mutation' => $mutation,
            'position' => $position,
            'user_validators' => $userValidators,
        ];

        return view('financeacc.asset-mutation', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('asset_mutations')
                    ->join('plants as plant_from', 'plant_from.id', '=', 'asset_mutations.from_plant_id')
                    ->join('plants as plant_to', 'plant_to.id', '=', 'asset_mutations.to_plant_id')
                    ->join('users', 'users.id', '=', 'asset_mutations.user_id')
                    ->join('profiles', 'profiles.id', '=', 'users.profile_id')
                    ->join('departments', 'departments.id', '=', 'profiles.department_id')
                    ->where('asset_mutations.company_id', $userAuth->company_id_selected)
                    ->select('asset_mutations.*', 'plant_from.initital as from_plant_initital',
                            'plant_from.short_name as from_plant_name', 'plant_from.code as from_plant_code',
                            'plant_to.initital as to_plant_initital',
                            'plant_to.short_name as to_plant_name', 'plant_to.code as to_plant_code',
                            'profiles.name as request_name', 'departments.name as depart')
                    ->where('asset_mutations.status', 0);

        $user = User::find(Auth::id());
        if ($request->has('type')) {
            // area manager
            if($user->hasRole('area manager')){

                $query = $query->where(function($query) use ($request, $user){

                    $query = $query->where(function($query) use ($user){
                        $query = $query->where('status_mutation', 5)
                                        ->where('level_request_second_id', $user->id);
                    });

                    $query = $query->orWhere(function($query) use ($user){
                        $query = $query->where('status_mutation', 7)
                                        ->where('level_request_third_id', $user->id);
                    });
                });

                if($request->query('plant_id') != 0){
                    // filter plant
                    $query = $query->where(function($query) use ($request){
                        $query = $query->where('from_plant_id', $request->query('plant_id'))
                                        ->orWhere('to_plant_id', $request->query('plant_id'));
                    });
                }

                switch ($request->query('type')) {
                    case 'approval_sender':
                        $query = $query->where(function($query) use ($request){
                            $query = $query
                                        ->whereIn('level_request_second', ['SPV DC Sender', 'AM Sender'])
                                        ->orWhereIn('level_request_third', ['SPV DC Sender', 'AM Sender']);
                        });
                        break;
                    case 'approval_receiver':
                        $query = $query->where(function($query) use ($request){
                            $query = $query
                                        ->whereIn('level_request_second', ['SPV DC Receiver', 'AM Receiver'])
                                        ->orWhereIn('level_request_third', ['SPV DC Receiver', 'AM Receiver']);
                        });
                        break;
                }
            } else {
                // store manager
                if($request->query('type') != 'all'){
                    switch ($request->query('type')) {
                        case 'confirmation_sender':
                            $query = $query->where('from_plant_id', $request->query('plant_id'));
                            $query = $query->where(function($query){
                                $query = $query->whereRaw('status_mutation = 7 and level_request_third_id = 0')
                                                ->orWhere('status_mutation', 9);
                            });
                            break;
                        case 'accepted_receiver':
                            $query = $query->where('to_plant_id', $request->query('plant_id'))
                                            ->where('status_mutation', 11);
                            break;
                    }

                } else {
                    $query = $query->where(function($query) use ($request){
                        $query = $query->where('from_plant_id', $request->query('plant_id'));
                        $query = $query->where(function($query){
                            $query = $query->whereRaw('status_mutation = 7 and level_request_third_id = 0')
                                            ->orWhere('status_mutation', 9);
                        });
                        $query = $query->orWhere(function($query) use ($request){
                            $query = $query->where('to_plant_id', $request->query('plant_id'))
                                                ->where('status_mutation', 11);
                        });
                    });
                }
            }
        } else {
            // HOD and RM
            $position = User::getPositionById($user->id);

            // approval request
            if( $position == 'Head of Department'){
                $query = $query->where(function($query) use ($user){
                    $query = $query->where(function($query) use ($user){
                        $query = $query->where('asset_mutations.level_request_first_id', $user->id)
                                    ->where('asset_mutations.status_mutation', 1);
                    });
                    $query = $query->orWhere(function($query) use ($user){
                        $query = $query->where('asset_mutations.level_request_second_id', $user->id)
                                            ->where('asset_mutations.status_mutation', 5);
                    });
                });
            } else if ($position == 'Regional Manager' ){
                $query = $query->where('asset_mutations.level_request_first_id', $user->id)
                            ->where('asset_mutations.status_mutation', 1);
            } else{
                $userValidators = AssetValidator::getValidatorByUserId($user->id);

                if(  sizeof($userValidators) <= 0 ){

                    // admin department
                    $query = $query->where(function($query) use ($user){
                        $query = $query->where('sender_cost_center_id', $user->id);

                        $query = $query->where(function($query){
                            $query = $query->whereRaw('status_mutation = 7 and level_request_third_id = 0')
                                            ->orWhere('status_mutation', 9);
                        });
                    });
                    $query = $query->orWhere(function($query) use ($user){
                        $query = $query->where('receiver_cost_center_id', $user->id)
                                        ->where('status_mutation', 11);
                    });

                } else {
                    // validator
                    $query = $query->whereIn('asset_mutations.asset_validator_id', $userValidators)
                                    ->where('asset_mutations.status_mutation', 3);
                }
            }
        }

        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('plant_from', function ($data) {
                return $data->from_plant_initital . ' ' . $data->from_plant_name;
            })
            ->addColumn('plant_to', function ($data) {
                return $data->to_plant_initital . ' ' . $data->to_plant_name;
            })
            ->addColumn('from_cost_center_desc', function ($data) {
                return $data->from_cost_center . ' - ' . $data->from_cost_center_code;
            })
            ->addColumn('to_cost_center_desc', function ($data) {
                return $data->to_cost_center . ' - ' . $data->to_cost_center_code;
            })
            ->addColumn('date_send_est_desc', function ($data) {
                return ($data->date_send_est != '' && $data->date_send_est != null ) ?
                            Helper::DateConvertFormat($data->date_send_est, 'Y-m-d H:i:s', 'd-m-Y') :
                            '-';
            })
            ->addColumn('date_request_desc', function ($data) {
                return ($data->date_request != '' && $data->date_request != null ) ? Helper::DateConvertFormat($data->date_request, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-';
            })
            ->addColumn('date_approve_first_desc', function ($data) {
                return ($data->date_approve_first != '' && $data->date_approve_first != null ) ? Helper::DateConvertFormat($data->date_approve_first, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-';
            })
            ->addColumn('date_confirmation_validator_desc', function ($data) {
                return ($data->date_confirmation_validator != '' && $data->date_confirmation_validator != null ) ? Helper::DateConvertFormat($data->date_confirmation_validator, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-';
            })
            ->addColumn('date_approve_second_desc', function ($data) {
                return ($data->date_approve_second != '' && $data->date_approve_second != null ) ? Helper::DateConvertFormat($data->date_approve_second, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-';
            })
            ->addColumn('date_approve_third_desc', function ($data) {
                return ($data->date_approve_third != '' && $data->date_approve_third != null ) ? Helper::DateConvertFormat($data->date_approve_third, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-';
            })
            ->addColumn('date_confirmation_sender_desc', function ($data) {
                return ($data->date_confirmation_sender != '' && $data->date_confirmation_sender != null ) ? Helper::DateConvertFormat($data->date_confirmation_sender, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-';
            })
            ->addColumn('date_accept_receiver_desc', function ($data) {
                return ($data->date_accept_receiver != '' && $data->date_accept_receiver != null ) ? Helper::DateConvertFormat($data->date_accept_receiver, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-';
            })
            ->addColumn('type', function ($data) {
                $type = '';
                switch ($data->status_mutation) {
                    case '1':
                        $type = 'Approval Request';
                        break;
                    case '3':
                        $type = 'Confirmation Validator';
                        break;
                    case '5':
                        if($data->sender_cost_center_id != 0){
                            $type = 'Approval Receiver';
                        }else{
                            $type = ($data->status_mutation == 5 && in_array($data->level_request_second, ['SPV DC Receiver', 'AM Receiver', 'HOD Receiver'])) ? 'Approval Receiver' : 'Approval Sender';
                        }
                        break;
                    case '7':
                        if( $data->level_request_third_id != 0 ){
                            // have approver 3
                            $type = ($data->status_mutation == 7 && in_array($data->level_request_second, ['SPV DC Receiver', 'AM Receiver', 'HOD Receiver'])) ? 'Approval Receiver' : 'Approval Sender';
                        } else {
                            // not have approver 3
                            $type = "Confirmation Sender";
                        }
                        break;
                    case '9':
                        $type = "Confirmation Sender";
                        break;
                    case '11':
                        $type = 'Accepted Receiver';
                        break;
                }
                return $type;
            })
            ->addColumn('requestor_desc', function ($data) {
                if($data->requestor != 'Admin Department'){
                    return $data->request_name . ' ( ' . $data->requestor . ' )';
                } else {
                    return $data->request_name . ' ( ' . $data->depart . ' )';
                }
            })
            ->addColumn('validator', function ($data) {
                return AssetValidator::getNameById($data->asset_validator_id);
            })
            ->addColumn('approver1', function ($data) {
                $approve1Name = User::getNameById($data->level_request_first_id);
                return $approve1Name . ' (' . $data->level_request_first . ')';
            })
            ->addColumn('approver2', function ($data) {
                $approve2Name = User::getNameById($data->level_request_second_id);
                return $approve2Name . ' (' . $data->level_request_second . ')';
            })
            ->addColumn('approver3', function ($data) {
                $approve3Name = User::getNameById($data->level_request_third_id);
                if($approve3Name != ''){
                    return $approve3Name . ' (' . $data->level_request_third . ')';
                }
                return '-';
            })
            ->make();
    }

    /*
        status mutation
        1 = request
        2 = cancel request
        3 = approve approver 1
        4 = unapprove approver 1
        5 = confirmation validator
        6 = reject by validator
        7 = approve approver 2
        8 = unapprove approver 2
        9 = approve approver 3
        10 = unapprove approver 3
        11 = confirmation send sender
        12 = reject sender
        13 = accept receiver
        14 = reject receiver
    */

    public function approveAssetRequest(Request $request)
    {
        $assetMutation = AssetMutation::find($request->id);

        if( $assetMutation->status_mutation != 1 ){
            // approver 2
            $assetMutation->date_approve_second = \Carbon\Carbon::now();
            $assetMutation->status_mutation = 7;
            $assetMutation->status_mutation_desc = 'Approved Approver 2';
        } else{
            // approver 1
            $assetMutation->date_approve_first = \Carbon\Carbon::now();
            $assetMutation->status_mutation = 3;
            $assetMutation->status_mutation_desc = 'Approved Approver 1';
        }

        if($assetMutation->save()){
            // send email notification
            Mail::queue(new NotificationMutation($assetMutation->id));

            $stat = 'success';
            $msg = Lang::get("message.approve.success", ["data" => Lang::get("asset transfer request")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.approve.failed", ["data" => Lang::get("asset transfer request")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function unapproveAssetRequest(Request $request)
    {
        $request->validate([
            'reason_rejected' => 'required'
        ]);

        $assetMutation = AssetMutation::find($request->id);
        $assetMutation->date_unapprove_first = \Carbon\Carbon::now();
        $assetMutation->reason_rejected = $request->reason_rejected;
        $assetMutation->status = 1;
        $assetMutation->status_mutation = 4;
        $assetMutation->status_mutation_desc = 'UnApprove By Approver 1';
        if($assetMutation->save()){
            // send email notification
            Mail::queue(new NotificationMutation($assetMutation->id));

            $stat = 'success';
            $msg = Lang::get("message.unapprove.success", ["data" => Lang::get("asset transfer request")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.unapprove.failed", ["data" => Lang::get("asset transfer request")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function assignValidator(Request $request)
    {
        $request->validate([
            'assign_validator' => 'required'
        ]);

        $assetMutation = AssetMutation::find($request->id);
        $assetMutation->assign_asset_validator_id = $assetMutation->asset_validator_id;
        $assetMutation->asset_validator_id = $request->assign_validator;
        if($assetMutation->save()){
            // send email notification
            Mail::queue(new NotificationMutation($assetMutation->id));

            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("assign validator request")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("assign validator request")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function confirmationValidator(Request $request)
    {
        $assetMutation = AssetMutation::find($request->id);
        $assetMutation->date_confirmation_validator = \Carbon\Carbon::now();
        $assetMutation->number = $request->number;
        $assetMutation->number_sub = $request->number_sub;
        $assetMutation->description = $request->description;
        $assetMutation->spec_user = ($request->spec_user) ? $request->spec_user : "";
        $assetMutation->qty_web = $request->qty_web;
        $assetMutation->uom = $request->uom;
        $assetMutation->status_mutation = 5;
        $assetMutation->status_mutation_desc = 'Confirmed By Validator';
        if($assetMutation->save()){
            // send email notification
            Mail::queue(new NotificationMutation($assetMutation->id));

            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("confirmation validator request")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("confirmation validator request")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function rejectValidator(Request $request)
    {
        $request->validate([
            'reason_rejected' => 'required'
        ]);

        $assetMutation = AssetMutation::find($request->id);
        $assetMutation->date_reject_validator = \Carbon\Carbon::now();
        $assetMutation->reason_rejected = $request->reason_rejected;
        $assetMutation->status = 1;
        $assetMutation->status_mutation = 6;
        $assetMutation->status_mutation_desc = 'Rejected By Validator';
        if($assetMutation->save()){
            // send email notification
            Mail::queue(new NotificationMutation($assetMutation->id));

            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("reject validator request")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("reject validator request")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function approveAMAssetMutation(Request $request)
    {
        $assetMutation = AssetMutation::find($request->id);

        if ( $assetMutation->status_mutation == 5 ) {
            // approve sender
            $assetMutation->date_approve_second = \Carbon\Carbon::now();
            $assetMutation->status_mutation = 7;
            $assetMutation->status_mutation_desc = 'Approved Approver 2';
        } else if ( $assetMutation->status_mutation == 7 ) {
            $assetMutation->date_approve_third = \Carbon\Carbon::now();
            $assetMutation->status_mutation = 9;
            $assetMutation->status_mutation_desc = 'Approved Approver 3';
        }

        if($assetMutation->save()){
            // send email notification
            Mail::queue(new NotificationMutation($assetMutation->id));

            $stat = 'success';
            $msg = Lang::get("message.approve.success", ["data" => Lang::get("asset transfer request")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.approve.failed", ["data" => Lang::get("asset transfer request")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function unapproveAMAssetMutation(Request $request)
    {
        $request->validate([
            'reason_rejected' => 'required'
        ]);

        $assetMutation = AssetMutation::find($request->id);
        $assetMutation->status = 1;
        $assetMutation->reason_rejected = $request->reason_rejected;

        if ( $assetMutation->status_mutation == 5 ) {
            // unapprove by approver 2
            $assetMutation->date_unapprove_second = \Carbon\Carbon::now();
            $assetMutation->status_mutation = 8;
            $assetMutation->status_mutation_desc = 'UnApprove By Approver 2';
        } else if ( $assetMutation->status_mutation == 7 ) {
            // unapprove by approver 3
            $assetMutation->date_unapprove_third = \Carbon\Carbon::now();
            $assetMutation->status_mutation = 10;
            $assetMutation->status_mutation_desc = 'UnApprove By Approver 3';
        }


        if($assetMutation->save()){
            // send email notification
            Mail::queue(new NotificationMutation($assetMutation->id));

            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("reject asset mutation")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("reject asset mutation")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function approveAssetMutation($id, Request $request)
    {
        $stat = 'success';
        $msg = '';

        $assetMutation = AssetMutation::find($id);
        if ( $assetMutation->status_mutation == 1 ) {
            // approve sender
            $assetMutation->date_approve_sender = \Carbon\Carbon::now();
            $assetMutation->status_mutation = 3;
            $assetMutation->status_mutation_desc = 'Approve By AM Sender';
        } else if ( $assetMutation->status_mutation == 3 ) {
            // approve receiver
            $assetMutation->date_approve_receiver = \Carbon\Carbon::now();
            $assetMutation->status_mutation = 5;
            $assetMutation->status_mutation_desc = 'Approve By AM Receiver';
        } else if ( $assetMutation->status_mutation == 5 ) {
            // accept receiver
            $assetMutation->date_receiver = \Carbon\Carbon::now();
            $assetMutation->status_mutation = 7;
            $assetMutation->status = 1;
            $assetMutation->pic_receiver = $request->pic_receiver;
            $assetMutation->status_mutation_desc = 'Accepted By Plant Receiver';

            if( $assetMutation->qty_mutation <= 1 || $assetMutation->qty_web == $assetMutation->qty_mutation ){
                // upload to sap
                $assetService = new AssetServiceSapImpl();
                $response = $assetService->mutationAsset($assetMutation);
                $msg = $response['message'];
                if (!$response['status']) {
                    $stat = 'success';
                } else {
                    $stat = 'failed';
                }
            }

        }

        if($stat != 'failed'){
            $assetMutation->save();
            Mail::queue(new NotificationMutation($assetMutation->id));
            $msg = Lang::get("message.approve.success", ["data" => Lang::get("asset transfer")]);
            if ($assetMutation->status_mutation == 7) {
                CheckChangeAssetSap::dispatch(
                    $assetMutation->id,
                    $assetMutation->number,
                    $assetMutation->number_sub,
                    $assetMutation->from_plant_id,
                    $assetMutation->to_plant_id
                )->onConnection('sync');
            }
        } else {
            if($msg == ''){
                $msg = Lang::get("message.approve.failed", ["data" => Lang::get("asset transfer")]);
            }
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function unapproveAssetMutation($id, Request $request)
    {
        $stat = 'success';
        $msg = '';

        $assetMutation = AssetMutation::find($id);
        if ( $assetMutation->status_mutation == 1 ) {
            // UnApprove sender
            $assetMutation->date_approve_sender = \Carbon\Carbon::now();
            $assetMutation->status_mutation = 4;
            $assetMutation->status = 1;
            $assetMutation->status_mutation_desc = 'UnApprove By AM Sender';
        }

        if ( $assetMutation->status_mutation == 3 ) {
            // approve receiver
            $assetMutation->date_approve_receiver = \Carbon\Carbon::now();
            $assetMutation->status_mutation = 6;
            $assetMutation->status = 1;
            $assetMutation->status_mutation_desc = 'UnApprove By AM Receiver';
        }

        if ( $assetMutation->status_mutation == 5 ) {
            // accept receiver
            $assetMutation->date_receiver = \Carbon\Carbon::now();
            $assetMutation->status_mutation = 8;
            $assetMutation->status_mutation_desc = 'Rejected By Plant Receiver';
            $assetMutation->status = 1;
            $assetMutation->reason_rejected = $request->reason;
        }

        if ($stat != 'failed') {
            $assetMutation->save();
            Mail::queue(new NotificationMutation($assetMutation->id));
            $msg = Lang::get("message.unapprove.success", ["data" => Lang::get("asset transfer")]);
        } else {
            if ($msg == '') {
                $msg = Lang::get("message.unapprove.failed", ["data" => Lang::get("asset transfer")]);
            }
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function send(Request $request)
    {
        $request->validate([
            'pic_sender' => 'required',
            'condition_asset_send' => 'required',
            'remark' => 'required',
            'est_send_date' => 'required',
        ]);

        $assetMutation = AssetMutation::find($request->id);
        $assetMutation->date_confirmation_sender = \Carbon\Carbon::now();
        $assetMutation->remark = $request->remark;
        $assetMutation->pic_sender = $request->pic_sender;
        $assetMutation->condition_send = $request->condition_asset_send;
        $assetMutation->date_send_est = $request->est_send_date;
        $assetMutation->status_mutation = 11;
        $assetMutation->status_mutation_desc = 'Confirmed Send';
        if($assetMutation->save()){
            // send email notification
            Mail::queue(new NotificationMutation($assetMutation->id));

            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("confirmation send")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("confirmation send")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function rejectSend(Request $request)
    {
        $request->validate([
            'reason_rejected' => 'required'
        ]);

        $assetMutation = AssetMutation::find($request->id);
        $assetMutation->date_reject_sender = \Carbon\Carbon::now();
        $assetMutation->reason_rejected = $request->reason_rejected;
        $assetMutation->status = 1;
        $assetMutation->status_mutation = 12;
        $assetMutation->status_mutation_desc = 'Rejected By Sender';
        if($assetMutation->save()){
            // send email notification
            Mail::queue(new NotificationMutation($assetMutation->id));

            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("reject send")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("reject send")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function accept(Request $request)
    {
        $request->validate([
            'condition_asset_receive' => 'required',
            'pic_receiver' => 'required',
        ]);

        $stat = 'success';
        $msg = '';

        $assetMutation = AssetMutation::find($request->id);
        $assetMutation->date_accept_receiver = \Carbon\Carbon::now();
        $assetMutation->condition_receive = $request->condition_asset_receive;
        $assetMutation->pic_receiver = $request->pic_receiver;
        $assetMutation->status_mutation = 13;
        $assetMutation->status = 1;
        $assetMutation->status_mutation_desc = 'Accepted Receiver';

        if( $assetMutation->qty_mutation <= 1 || $assetMutation->qty_web == $assetMutation->qty_mutation ){
            // upload to sap
            $assetService = new AssetServiceSapImpl();
            $response = $assetService->mutationAsset($assetMutation);
            $msg = $response['message'];
            if (!$response['status']) {
                $stat = 'success';
            } else {
                $stat = 'failed';
            }
        }

        if($stat != 'failed'){
            $assetMutation->save();
            Mail::queue(new NotificationMutation($assetMutation->id));
            $msg = Lang::get("message.save.success", ["data" => Lang::get("accept receiver")]);
            if ($assetMutation->status_mutation == 13) {
                CheckChangeAssetSap::dispatch(
                    $assetMutation->id,
                    $assetMutation->number,
                    $assetMutation->number_sub,
                    $assetMutation->from_plant_id,
                    $assetMutation->from_cost_center_code,
                    $assetMutation->to_plant_id,
                    $assetMutation->to_cost_center_code,
                )->onConnection('sync');
            }
        } else {
            if($msg == ''){
                $msg = Lang::get("message.save.failed", ["data" => Lang::get("accept receiver")]);
            }
        }
        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function rejectAccept(Request $request)
    {
        $request->validate([
            'reason_rejected' => 'required'
        ]);

        $assetMutation = AssetMutation::find($request->id);
        $assetMutation->date_reject_receiver = \Carbon\Carbon::now();
        $assetMutation->reason_rejected = $request->reason_rejected;
        $assetMutation->status = 1;
        $assetMutation->status_mutation = 14;
        $assetMutation->status_mutation_desc = 'Rejected By Receiver';
        if($assetMutation->save()){
            // send email notification
            Mail::queue(new NotificationMutation($assetMutation->id));

            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("reject receive")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("reject receive")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function preview(Request $request)
    {
        $qAssetMutation = DB::table('asset_mutations')
                            ->where('number', $request->number)
                            ->where('number_sub', $request->sub)
                            ->where('from_plant_id', $request->plant_id)
                            ->where('status', 0)
                            ->whereNotIn('status_mutation', [2, 4, 6, 8, 10, 12, 13, 14]);

        if( $qAssetMutation->count() > 0 ){
            $assetMutation = $qAssetMutation->first();
            if( $assetMutation->status_mutation >= 11){

                $assetMutation->plant_sender = Plant::getShortNameById($assetMutation->from_plant_id);
                $assetMutation->plant_sender_code = Plant::getCodeById($assetMutation->from_plant_id);
                $assetMutation->plant_sender_address = Plant::getAddressById($assetMutation->from_plant_id);
                $assetMutation->plant_receiver = Plant::getShortNameById($assetMutation->to_plant_id);
                $assetMutation->plant_receiver_code = Plant::getCodeById($assetMutation->to_plant_id);
                $assetMutation->plant_receiver_address = Plant::getAddressById($assetMutation->to_plant_id);


                $dataview = [
                    'assetMutation' => $assetMutation
                ];

                return view('financeacc.asset-mutation-preview', $dataview);

            }else{
                echo Lang::get("This Mutation Cannot Preview !");
            }
        } else {
            echo Lang::get("This Mutation Cannot Preview !");
        }


    }

}
