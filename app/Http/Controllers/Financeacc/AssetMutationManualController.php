<?php

namespace App\Http\Controllers\Financeacc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Financeacc\AssetMutation;
use App\Models\Financeacc\AssetValidator;
use App\Models\Plant;
use App\Models\User;

class AssetMutationManualController extends Controller
{
    public function index(Request $request)
    {
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('financeacc.asset-mutation-manual', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('asset_mutations')
                    ->leftJoin('plants as plant_from', 'plant_from.id', '=', 'asset_mutations.from_plant_id')
                    ->leftJoin('plants as plant_to', 'plant_to.id', '=', 'asset_mutations.to_plant_id')
                    ->join('users', 'users.id', '=', 'asset_mutations.user_id')
                    ->join('profiles', 'profiles.id', '=', 'users.profile_id')
                    ->join('departments', 'departments.id', '=', 'profiles.department_id')
                    ->where('asset_mutations.company_id', $userAuth->company_id_selected)
                    ->select(
                        'asset_mutations.*',
                        'plant_from.initital as from_plant_initital',
                        'plant_from.short_name as from_plant_name',
                        'plant_from.code as from_plant_code',
                        'plant_to.initital as to_plant_initital',
                        'plant_to.short_name as to_plant_name',
                        'plant_to.code as to_plant_code',
                        'profiles.name as request_name', 'departments.name as depart'
                    )
                    ->where('asset_mutations.status', 1)
                    ->where('asset_mutations.status_mutation', 13)
                    ->where('asset_mutations.status_changed', 0);

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

    public function confirm($id)
    {
        $assetMutation = AssetMutation::find($id);
        $assetMutation->status_changed = 1;
        if ($assetMutation->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("confirmation transfer asset")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("confirmation transfer asset")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }
}
