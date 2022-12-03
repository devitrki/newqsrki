<?php

namespace App\Http\Controllers\Application\GeneralConfiguration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\ConfigurationGroup;

class GroupConfigurationController extends Controller
{
    public function index(Request $request){
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('application.configuration.configuration-group', $dataview)->render();
    }

    public function dtble()
    {
        $query = DB::table('configuration_groups')
                    ->select(['id', 'name']);

        return Datatables::of($query)->addIndexColumn()->make();
    }

    public function select(Request $request)
    {
        $query = DB::table('configuration_groups')->select(['id', 'name as text']);

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
                        'name' => 'required|unique:configuration_groups,name',
                    ]);

        $configurationGroup = new ConfigurationGroup;
        $configurationGroup->name = $request->name;
        if ($configurationGroup->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("configuration group")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("configuration group")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                        'name' => 'required',
                    ]);

        $configurationGroup = ConfigurationGroup::find($request->id);
        $configurationGroup->name = strtolower($request->name);
        if ($configurationGroup->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("configuration group")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("configuration group")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        if( Helper::used( $id, 'configuration_group_id', ['configurations'] ) ){
            return response()->json( Helper::resJSON( 'failed', Lang::get('validation.used') ) );
        }

        $configurationGroup = ConfigurationGroup::find($id);
        if ($configurationGroup->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("configuration group")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("configuration group")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }
}
