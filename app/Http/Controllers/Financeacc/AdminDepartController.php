<?php

namespace App\Http\Controllers\Financeacc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\User;
use App\Models\Plant;
use App\Models\Financeacc\AssetAdminDepart;

class AdminDepartController extends Controller
{
    public function index(Request $request){
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('financeacc.asset-admin-depart', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('asset_admin_departs')
                    ->join('plants', 'plants.id', 'asset_admin_departs.plant_id')
                    ->where('asset_admin_departs.company_id', $userAuth->company_id_selected)
                    ->select(
                        'asset_admin_departs.id',
                        'asset_admin_departs.plant_id',
                        'asset_admin_departs.cost_center',
                        'asset_admin_departs.cost_center_code',
                        'asset_admin_departs.admin_depart_id',
                        'asset_admin_departs.hod_id',
                        'plants.initital',
                        'plants.short_name',
                    );

        return Datatables::of($query)
                    ->addIndexColumn()
                    ->addColumn('admin_depart_name', function ($data) {
                        return User::getNameById($data->admin_depart_id);
                    })
                    ->addColumn('hod_name', function ($data) {
                        return User::getNameById($data->hod_id);
                    })
                    ->addColumn('plant', function ($data) {
                        return $data->initital . ' ' . $data->short_name;
                    })
                    ->filterColumn('plant', function ($query, $keyword) {
                        $sql = "plants.short_name like ?";
                        $query->whereRaw($sql, ["%{$keyword}%"]);
                    })
                    ->make();
    }

    public function store(Request $request)
    {
        $request->validate([
                        'plant' => 'required',
                        'cost_center' => 'required',
                        'hod' => 'required',
                        'admin_department' => 'required'
                    ]);

        $userAuth = $request->get('userAuth');

        // check plant and cost center must unique in db table
        $countAdminDepart = DB::table('asset_admin_departs')
                        ->where('plant_id', $request->plant)
                        ->where('cost_center_code', $request->cost_center_code)
                        ->count('plant_id');

        if( $countAdminDepart > 0 ){
            $stat = 'failed';
            $msg = Lang::get("Plant and cost center already mapping.");
            return response()->json( Helper::resJSON( $stat, $msg ) );
        }

        $assetAdminDepart = new AssetAdminDepart;
        $assetAdminDepart->company_id = $userAuth->company_id_selected;
        $assetAdminDepart->plant_id = $request->plant;
        $assetAdminDepart->cost_center = $request->cost_center;
        $assetAdminDepart->cost_center_code = $request->cost_center_code;
        $assetAdminDepart->admin_depart_id = $request->admin_department;
        $assetAdminDepart->hod_id = $request->hod;
        if ($assetAdminDepart->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("admin department")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("admin department")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                        'hod' => 'required',
                        'admin_department' => 'required'
                    ]);

        $assetAdminDepart = AssetAdminDepart::find($request->id);
        $assetAdminDepart->admin_depart_id = $request->admin_department;
        $assetAdminDepart->hod_id = $request->hod;
        if ($assetAdminDepart->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("admin department")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("admin department")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        $assetAdminDepart = AssetAdminDepart::find($id);
        if ($assetAdminDepart->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("admin department")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("admin department")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }
}
