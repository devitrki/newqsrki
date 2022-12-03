<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\DataTables;

use App\Library\Helper;
use App\Models\Company;

class CompanyController extends Controller
{
    public function index(Request $request){
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('master.company', $dataview)->render();
    }

    public function dtble()
    {
        $query = DB::table('companies')->select(['id', 'name', 'code']);
        return Datatables::of($query)->addIndexColumn()->make();
    }

    public function select(Request $request)
    {
        $query = DB::table('companies')->select(['id', 'name as text']);

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
                        'name' => 'required|unique:companies,name',
                        'code' => 'required|unique:companies,code',
                    ]);

        $company = new Company;
        $company->name = $request->name;
        $company->code = $request->code;
        if ($company->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("company")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("company")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                        'name' => 'required',
                        'code' => 'required',
                    ]);

        $company = Company::find($request->id);
        $company->name = $request->name;
        $company->code = $request->code;
        if ($company->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("company")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("company")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        if( Helper::used( $id, 'company_id', ['profiles'] ) ){
            return response()->json( Helper::resJSON( 'failed', Lang::get('validation.used') ) );
        }

        $company = Company::find($id);
        if ($company->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("company")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("company")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    // configuration
    public function dtbleConf($id)
    {
        $company = DB::table('companies')
                    ->where('id', $id)
                    ->select(
                        'configurations'
                    )
                    ->first();

        $configurations = [];

        if ($company->configurations) {
            $configurationJsons = json_decode($company->configurations, true);
            foreach ($configurationJsons as $key => $value) {
                $configuration = [
                    'key' => $key,
                    'value' => $value
                ];
                array_push($configurations, $configuration);
            }
        }

        $configurations = collect($configurations);

        return Datatables::of($configurations)->addIndexColumn()->toJson();
    }

    public function storeConf(Request $request, $id)
    {
        $request->validate([
            'key' => 'required',
            'value' => 'required'
        ]);

        $company = Company::find($id);

        $configurations = [];
        if ($company->configurations) {
            $configurations = json_decode($company->configurations, true);
        }
        $configurations[$request->key] = $request->value;
        $configurationJson = json_encode($configurations);

        $company->configurations = $configurationJson;
        if ($company->save()) {
            Cache::forget('company_configuration_id_' . $company->id);

            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("company configuration")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("company configuration")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroyConf(Request $request, $id)
    {
        $company = Company::find($id);

        $configurations = json_decode($company->configurations, true);
        unset($configurations[$request->key]);

        $configurationJson = json_encode($configurations);
        $company->configurations = $configurationJson;
        if ($company->save()) {
            Cache::forget('company_configuration_id_' . $company->id);

            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("company configuration")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("company configuration")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }
}
