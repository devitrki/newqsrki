<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Pos;

class PosController extends Controller
{
    public function index(Request $request){
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('master.pos', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('pos')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(
                        'id',
                        'name',
                        'code'
                    );
        return Datatables::of($query)->addIndexColumn()->make();
    }

    public function select(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('pos')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(DB::raw('id, name AS text'));

        if ($request->has('search')) {
            $query->whereRaw("LOWER(name) like '%" . $request->search . "%'");
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
            'name' => 'required|max:150|unique:pos,name',
            'code' => 'required|max:150|unique:pos,code'
        ]);

        $userAuth = $request->get('userAuth');

        $pos = new Pos;
        $pos->company_id = $userAuth->company_id_selected;
        $pos->name = $request->name;
        $pos->code = $request->code;
        if ($pos->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("pos")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("pos")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:150',
            'code' => 'required|max:150'
        ]);

        $pos = Pos::find($id);
        $pos->name = $request->name;
        $pos->code = $request->code;
        if ($pos->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("pos")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("pos")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        $pos = Pos::find($id);
        if ($pos->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("pos")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("pos")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    // configuration

    public function dtbleConf($id)
    {
        $pos = DB::table('pos')
                    ->where('id', $id)
                    ->select(
                        'configurations'
                    )
                    ->first();

        $configurations = [];

        if ($pos->configurations) {
            $configurationJsons = json_decode($pos->configurations, true);
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

        $pos = Pos::find($id);

        $configurations = [];
        if ($pos->configurations) {
            $configurations = json_decode($pos->configurations, true);
        }
        $configurations[$request->key] = $request->value;
        $configurationJson = json_encode($configurations);

        $pos->configurations = $configurationJson;
        if ($pos->save()) {
            Cache::forget('pos_configuration_id_' . $pos->id);

            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("pos configuration")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("pos configuration")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroyConf(Request $request, $id)
    {
        $pos = Pos::find($id);

        $configurations = json_decode($pos->configurations, true);
        unset($configurations[$request->key]);

        $configurationJson = json_encode($configurations);
        $pos->configurations = $configurationJson;
        if ($pos->save()) {
            Cache::forget('pos_configuration_id_' . $pos->id);

            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("pos configuration")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("pos configuration")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }
}
