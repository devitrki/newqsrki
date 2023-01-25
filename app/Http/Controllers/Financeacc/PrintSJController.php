<?php

namespace App\Http\Controllers\Financeacc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

use App\Library\Helper;

use Yajra\DataTables\DataTables;

use App\Models\Plant;
use App\Models\Financeacc\Asset;
use App\Models\Financeacc\AssetValidator;
use App\Models\Financeacc\AssetAdminDepart;
use App\Models\User;

class PrintSJController extends Controller
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
            'first_cost_center_code' => Asset::getCostCenterCodeByPlantBy($first_plant_id),
        ];
        return view('financeacc.asset-printsj', $dataview)->render();
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

        if( $request->has('plant_id') ) {
            $query = $query
                        ->where('from_plant_id', $request->query('plant_id'))
                        ->where('from_cost_center_code', $request->query('cost_center_code'))
                        ->where('status_mutation', '>=', 11);
        }

        $user = User::find(Auth::id());

        if(!$user->hasRole('store manager')){
            $query = $query->where('sender_cost_center_id', $user->id);
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

    public function preview(Request $request)
    {
        $id = [];
        if( $request->has('id') ) {
            $id = explode(',', $request->query('id'));
        }

        if( sizeof($id) > 0 ){
            $qAssetMutation = DB::table('asset_mutations')
                                ->whereIn('id', $id)
                                ->where('status', 0)
                                ->where('status_mutation', '>=', 11);

            if( $qAssetMutation->count() > 0 ){
                $assetMutations = $qAssetMutation->get();

                $header = [];
                $rows = [];

                foreach ($assetMutations as $key => $assetMutation) {
                    if( $key <= 0 ){
                        $header = [
                            'plant_sender' => Plant::getShortNameById($assetMutation->from_plant_id),
                            'plant_sender_code' => Plant::getCodeById($assetMutation->from_plant_id),
                            'plant_sender_address' => Plant::getAddressById($assetMutation->from_plant_id),
                            'plant_receiver' => Plant::getShortNameById($assetMutation->to_plant_id),
                            'plant_receiver_code' => Plant::getCodeById($assetMutation->to_plant_id),
                            'plant_receiver_address' => Plant::getAddressById($assetMutation->to_plant_id),
                            'date_confirmation_sender' => $assetMutation->date_confirmation_sender
                        ];
                    }

                    $rows[] = [
                        'number' => $assetMutation->number,
                        'number_sub' => $assetMutation->number_sub,
                        'description' => $assetMutation->description,
                        'spec_user' => $assetMutation->spec_user,
                        'qty_mutation' => $assetMutation->qty_mutation,
                        'uom' => $assetMutation->uom,
                        'remark' => $assetMutation->remark
                    ];
                }

                $dataview = [
                    'header' => $header,
                    'rows' => $rows
                ];

                return view('financeacc.asset-printsj-preview', $dataview);
            } else {
                echo Lang::get("This Mutation Cannot Preview !");
            }
        } else {
            echo Lang::get("This Mutation Cannot Preview !");
        }
    }
}
