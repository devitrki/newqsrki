<?php

namespace App\Http\Controllers\Financeacc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use DataTables;
use App\Library\Helper;

use App\Mail\Financeacc\Assets\NotificationRequestMutation;
use App\Mail\Financeacc\Assets\NotificationMutation;

use App\Models\Plant;
use App\Models\Financeacc\AssetValidator;
use App\Models\Financeacc\AssetRequestMutation;
use App\Models\Financeacc\AssetMutation;
use App\Models\User;

class AssetRequestMutationController extends Controller
{
    public function index(Request $request){
        $user = User::find(Auth::id());
        $position = User::getPositionById($user->id);

        $dataview = [
            'menu_id' => $request->query('menuid'),
            'position' => $position
        ];
        return view('financeacc.asset-request-mutation', $dataview)->render();
    }

    public function dtble()
    {
        $query = DB::table('asset_request_mutations')
                    ->join('users', 'users.id', '=', 'asset_request_mutations.user_id')
                    ->join('profiles', 'profiles.id', '=', 'users.profile_id')
                    ->join('departments', 'departments.id', '=', 'profiles.department_id')
                    ->join('plants as plant_from', 'plant_from.id', '=', 'asset_request_mutations.from_plant_id')
                    ->join('plants as plant_to', 'plant_to.id', '=', 'asset_request_mutations.to_plant_id')
                    ->join('asset_validators', 'asset_validators.id', '=', 'asset_request_mutations.asset_validator_id')
                    ->select('asset_request_mutations.id', 'asset_request_mutations.description', 'asset_request_mutations.number',
                            'asset_request_mutations.number_sub', 'asset_request_mutations.qty_mutation', 'asset_request_mutations.uom',
                            'asset_request_mutations.from_cost_center', 'asset_request_mutations.to_cost_center',
                            'asset_request_mutations.date_submit', 'asset_request_mutations.date_confirmation_validator',
                            'asset_request_mutations.date_send', 'asset_request_mutations.note_request',
                            'plant_from.initital as from_plant_initital', 'plant_from.short_name as from_plant_name',
                            'plant_to.initital as to_plant_initital', 'plant_to.short_name as to_plant_name',
                            'profiles.name as request_name', 'departments.name as depart', 'asset_request_mutations.status',
                            'asset_request_mutations.spec_user', 'asset_validators.name as validator',
                            'asset_request_mutations.asset_validator_id', 'asset_request_mutations.from_plant_id',
                            'asset_request_mutations.from_cost_center_code', 'asset_request_mutations.remark',
                            'asset_request_mutations.uom', 'asset_request_mutations.qty_web', 'asset_request_mutations.date_approve_hod',
                            )
                    ->where('asset_request_mutations.status', 0);

        $user = User::find(Auth::id());

        $position = User::getPositionById($user->id);

        if($user->hasRole('store manager')){

            // check dc / outlet
            $plantAuth = Plant::getPlantsIdByUserId($user->id);
            $plantAuths = explode(',' , $plantAuth);
            if( sizeof($plantAuths) == 1 ){
                $query = $query->where('asset_request_mutations.from_plant_id', $plantAuths[0])
                                ->where('asset_request_mutations.step_request', 5);
            }

        } else if( $position == 'Head of Department' || $position == 'Regional Manager' ){
            // approval request
            $query = $query->where('asset_request_mutations.level_request_id', $user->id)
                            ->where('asset_request_mutations.step_request', 1);
        } else {

            // validator
            $userValidators = AssetValidator::getValidatorByUserId($user->id);
            $query = $query->whereIn('asset_request_mutations.asset_validator_id', $userValidators)
                            ->where('asset_request_mutations.step_request', 3);
        }

        return Datatables::of($query)
                        ->addIndexColumn()
                        ->addColumn('validator', function ($data) {
                            return AssetValidator::getNameById($data->asset_validator_id);
                        })
                        ->addColumn('plant_sender', function ($data) {
                            return $data->from_plant_initital . ' ' . $data->from_plant_name;
                        })
                        ->addColumn('plant_receiver', function ($data) {
                            return $data->to_plant_initital . ' ' . $data->to_plant_name;
                        })
                        ->addColumn('date_submit_desc', function ($data) {
                            return ($data->date_submit != '' && $data->date_submit != null ) ? Helper::DateConvertFormat($data->date_submit, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-';
                        })
                        ->addColumn('date_approval_desc', function ($data) {
                            return ($data->date_approve_hod != '' && $data->date_approve_hod != null ) ? Helper::DateConvertFormat($data->date_approve_hod, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-';
                        })
                        ->addColumn('date_confirmation_validator_desc', function ($data) {
                            return ($data->date_confirmation_validator != '' && $data->date_confirmation_validator != null ) ? Helper::DateConvertFormat($data->date_confirmation_validator, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-';
                        })
                        ->addColumn('date_send_desc', function ($data) {
                            return ($data->date_send != '' && $data->date_send != null ) ? Helper::DateConvertFormat($data->date_send, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-';
                        })
                        ->addColumn('request_by', function ($data) {
                            return $data->request_name . ' ( ' . $data->depart . ' )';
                        })
                        ->rawColumns(['status_desc'])
                        ->make();
    }

    public function approveAssetRequest(Request $request)
    {
        $assetRequestMutation = AssetRequestMutation::find($request->id);
        $assetRequestMutation->date_approve_hod = \Carbon\Carbon::now();
        $assetRequestMutation->step_request = 3;
        $assetRequestMutation->step_request_desc = 'Approved By HOD/RM';
        if($assetRequestMutation->save()){
            // send email notification
            Mail::send(new NotificationRequestMutation($assetRequestMutation->id));

            $stat = 'success';
            $msg = \Lang::get("message.approve.success", ["data" => \Lang::get("mutation request")]);
        } else {
            $stat = 'failed';
            $msg = \Lang::get("message.approve.failed", ["data" => \Lang::get("mutation request")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function unapproveAssetRequest(Request $request)
    {
        $request->validate([
            'note_unapprove' => 'required'
        ]);

        $assetRequestMutation = AssetRequestMutation::find($request->id);
        $assetRequestMutation->date_reject_validator = \Carbon\Carbon::now();
        $assetRequestMutation->note_rejected = $request->note_unapprove;
        $assetRequestMutation->status = 1;
        $assetRequestMutation->step_request = 4;
        $assetRequestMutation->step_request_desc = 'UnApprove By Approval';
        if($assetRequestMutation->save()){
            // send email notification
            Mail::send(new NotificationRequestMutation($assetRequestMutation->id));

            $stat = 'success';
            $msg = \Lang::get("message.unapprove.success", ["data" => \Lang::get("mutation request")]);
        } else {
            $stat = 'failed';
            $msg = \Lang::get("message.unapprove.failed", ["data" => \Lang::get("mutation request")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function assignValidator(Request $request)
    {
        $request->validate([
            'assign_validator' => 'required'
        ]);

        $assetRequestMutation = AssetRequestMutation::find($request->id);
        $assetRequestMutation->assign_asset_validator_id = $assetRequestMutation->asset_validator_id;
        $assetRequestMutation->asset_validator_id = $request->assign_validator;
        if($assetRequestMutation->save()){
            // send email notification
            Mail::send(new NotificationRequestMutation($assetRequestMutation->id));

            $stat = 'success';
            $msg = \Lang::get("message.save.success", ["data" => \Lang::get("assign validator request")]);
        } else {
            $stat = 'failed';
            $msg = \Lang::get("message.save.failed", ["data" => \Lang::get("assign validator request")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function confirmationValidator(Request $request)
    {
        $assetRequestMutation = AssetRequestMutation::find($request->id);
        $assetRequestMutation->date_confirmation_validator = \Carbon\Carbon::now();
        $assetRequestMutation->number = $request->number;
        $assetRequestMutation->number_sub = $request->number_sub;
        $assetRequestMutation->description = $request->description;
        $assetRequestMutation->spec_user =  $request->spec_user;
        $assetRequestMutation->qty_web = $request->qty_web;
        $assetRequestMutation->uom = $request->uom;
        $assetRequestMutation->remark = $request->remark;
        $assetRequestMutation->step_request = 5;
        $assetRequestMutation->step_request_desc = 'Confirmed By Validator';
        if($assetRequestMutation->save()){
            // send email notification
            Mail::send(new NotificationRequestMutation($assetRequestMutation->id));

            $stat = 'success';
            $msg = \Lang::get("message.save.success", ["data" => \Lang::get("confirmation validator request")]);
        } else {
            $stat = 'failed';
            $msg = \Lang::get("message.save.failed", ["data" => \Lang::get("confirmation validator request")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function rejectValidator(Request $request)
    {
        $request->validate([
            'note_rejected' => 'required'
        ]);

        $assetRequestMutation = AssetRequestMutation::find($request->id);
        $assetRequestMutation->date_reject_validator = \Carbon\Carbon::now();
        $assetRequestMutation->note_rejected = $request->note_rejected;
        $assetRequestMutation->status = 1;
        $assetRequestMutation->step_request = 6;
        $assetRequestMutation->step_request_desc = 'Rejected By Validator';
        if($assetRequestMutation->save()){
            // send email notification
            Mail::send(new NotificationRequestMutation($assetRequestMutation->id));

            $stat = 'success';
            $msg = \Lang::get("message.save.success", ["data" => \Lang::get("reject validator request")]);
        } else {
            $stat = 'failed';
            $msg = \Lang::get("message.save.failed", ["data" => \Lang::get("reject validator request")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function sendAssetRequest(Request $request)
    {
        $request->validate([
            'sender' => 'required',
            'est_send_date' => 'required'
        ]);

        $assetRequestMutation = AssetRequestMutation::find($request->id);
        $assetRequestMutation->date_send = \Carbon\Carbon::now();
        $assetRequestMutation->step_request = 7;
        $assetRequestMutation->step_request_desc = 'Send By DC';
        $assetRequestMutation->status = 1;
        if($assetRequestMutation->save()){

            // create mutation asset
            $assetMutation = new AssetMutation;
            $assetMutation->number = $assetRequestMutation->number;
            $assetMutation->number_sub = $assetRequestMutation->number_sub;
            $assetMutation->description = $assetRequestMutation->description;
            $assetMutation->spec_user = (is_null($assetRequestMutation->spec_user)) ? '' : $assetRequestMutation->spec_user;
            $assetMutation->qty_web = $assetRequestMutation->qty_web;
            $assetMutation->qty_mutation = $assetRequestMutation->qty_mutation;
            $assetMutation->uom = $assetRequestMutation->uom;
            $assetMutation->remark = (is_null($assetRequestMutation->remark)) ? '' : $assetRequestMutation->remark;
            $assetMutation->from_plant_id = $assetRequestMutation->from_plant_id;
            $assetMutation->from_cost_center = $assetRequestMutation->from_cost_center;
            $assetMutation->from_cost_center_code = $assetRequestMutation->from_cost_center_code;
            $assetMutation->to_plant_id = $assetRequestMutation->to_plant_id;
            $assetMutation->to_cost_center = $assetRequestMutation->to_cost_center;
            $assetMutation->to_cost_center_code = $assetRequestMutation->to_cost_center_code;
            $assetMutation->pic_sender = $request->sender;
            $assetMutation->date_send_est = $request->est_send_date;
            $assetMutation->date_send = \Carbon\Carbon::now();
            $assetMutation->status_mutation = 1;
            $assetMutation->status_mutation_desc = 'Send By Plant Sender';
            if ($assetMutation->save()) {
                // send email
                Mail::send(new NotificationMutation($assetMutation->id));

                // send email notification
                Mail::send(new NotificationRequestMutation($assetRequestMutation->id));

                $stat = 'success';
                $msg = \Lang::get("message.save.success", ["data" => \Lang::get("send request asset transfer")]);
            }

        } else {
            $stat = 'failed';
            $msg = \Lang::get("message.save.failed", ["data" => \Lang::get("send request asset transfer")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

}
