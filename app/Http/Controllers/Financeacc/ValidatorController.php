<?php

namespace App\Http\Controllers\Financeacc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

use App\Library\Helper;

use App\Models\Financeacc\AssetValidator;
use App\Models\Financeacc\AssetValidatorMapping;
use App\Models\User;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

class ValidatorController extends Controller
{
    public function index(Request $request){
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('financeacc.asset-validator', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('asset_validators')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(['id', 'name']);

        return Datatables::of($query)->addIndexColumn()->make();
    }

    public function select(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('asset_validators')
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
                        'name' => 'required|unique:asset_validators,name',
                    ]);

        $userAuth = $request->get('userAuth');

        $assetValidator = new AssetValidator;
        $assetValidator->company_id = $userAuth->company_id_selected;
        $assetValidator->name = $request->name;
        if ($assetValidator->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("asset validator")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("asset validator")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                        'name' => 'required',
                    ]);

        $assetValidator = AssetValidator::find($request->id);
        $assetValidator->name = $request->name;
        if ($assetValidator->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("asset validator")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("asset validator")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        $assetValidator = AssetValidator::find($id);

        // delete mapping
        DB::table('asset_validator_mappings')->where('asset_validator_id', $assetValidator->id)->delete();

        if ($assetValidator->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("asset validator")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("asset validator")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    // pic dc

    public function dtblePic($id)
    {
        $query = DB::table('asset_validator_mappings')
                    ->join('asset_validators', 'asset_validators.id', 'asset_validator_mappings.asset_validator_id')
                    ->join('plants', 'plants.id', 'asset_validator_mappings.plant_id')
                    ->where('asset_validator_id', $id)
                    ->select('asset_validator_mappings.id', 'asset_validator_mappings.plant_id', 'plants.initital', 'plants.short_name',
                            'asset_validator_mappings.pic_validators', 'asset_validators.name');

        return Datatables::of($query)
                ->addIndexColumn()
                ->addColumn('plant', function ($data) {
                    return $data->initital . ' ' . $data->short_name;
                })
                ->addColumn('pic_validator_names', function ($data) {
                    $picValidators = explode(',', $data->pic_validators);
                    $pic = '';

                    foreach ($picValidators as $i => $picValidator) {
                        $pic .= User::getNameById($picValidator) .',';
                    }

                    return $pic;
                })
                ->addColumn('pic', function ($data) {
                    $picValidators = explode(',', $data->pic_validators);
                    $pic = '';

                    foreach ($picValidators as $i => $picValidator) {
                        $pic .= ($i != 0) ? '</br>' . '- ' . User::getNameById($picValidator) : '- ' . User::getNameById($picValidator);
                    }

                    return $pic;
                })
                ->rawColumns(['pic'])
                ->make();
    }

    public function storePic(Request $request)
    {
        $request->validate([
                        'dc' => ['required', Rule::unique('asset_validator_mappings', 'plant_id')->where(function ($query) use ($request) {
                            return $query->where('asset_validator_id', $request->asset_validator_id);
                        })],
                        'pic' => 'required'
                    ]);

        $userAuth = $request->get('userAuth');

        $assetValidatorMapping = new AssetValidatorMapping;
        $assetValidatorMapping->company_id = $userAuth->company_id_selected;
        $assetValidatorMapping->plant_id = $request->dc;
        $assetValidatorMapping->pic_validators = $request->pic;
        $assetValidatorMapping->asset_validator_id = $request->asset_validator_id;
        if ($assetValidatorMapping->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("validator pic dc")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("validator pic dc")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function updatePic(Request $request, $id)
    {
        $request->validate([
                        'dc' => 'required',
                        'pic' => 'required',
                    ]);

        $assetValidatorMapping = AssetValidatorMapping::find($request->id);
        $assetValidatorMapping->plant_id = $request->dc;
        $assetValidatorMapping->pic_validators = $request->pic;
        if ($assetValidatorMapping->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("validator pic dc")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("validator pic dc")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroyPic($id)
    {
        $assetValidatorMapping = AssetValidatorMapping::find($id);

        if ($assetValidatorMapping->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("validator pic dc")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("validator pic dc")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }
}
