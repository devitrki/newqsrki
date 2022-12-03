<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use App\Library\Helper;
use Yajra\DataTables\DataTables;

use App\Models\Auth\Languange;
use App\Models\User;

class LanguageController extends Controller
{
    public function index(Request $request){
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('master.language', $dataview)->render();
    }

    public function dtble()
    {
        $query = DB::table('languanges')->select(['id', 'lang', 'short_lang']);
        return Datatables::of($query)->addIndexColumn()->make();
    }

    public function select(Request $request)
    {
        $query = DB::table('languanges')->select(['id', 'lang as text']);

        if ($request->has('search')) {
            $query->where('lang', 'like', '%' . $request->search . '%');
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
                        'lang' => 'required|unique:languanges,lang',
                        'short_lang' => 'required|unique:languanges,short_lang',
                    ]);

        DB::beginTransaction();
        $languange = new Languange;
        $languange->lang = $request->lang;
        $languange->short_lang = $request->short_lang;
        if ($languange->save()) {
            DB::commit();
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("language")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("language")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                        'lang' => 'required|unique:languanges,lang',
                        'short_lang' => 'required|unique:languanges,short_lang',
                    ]);

        $languange = Languange::find($id);
        $languange->lang = $request->lang;
        $languange->short_lang = $request->short_lang;
        if ( $languange->save() ) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("language")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("language")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        if( Helper::used( $id, 'languange_id', ['users'] ) ){
            return response()->json( Helper::resJSON( 'failed', Lang::get('validation.used') ) );
        }

        $languange = Languange::find($id);
        if ($languange->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("language")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("language")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function changeLanguageUser(Request $request){

        $user = User::find($request->user_id);
        $user->languange_id = $request->languange_id;
        if($user->save()){
            $status = true;
        } else {
            $status = false;
        }
        return response()->json([
                                    'status' => $status,
                                    'message' => "",
                                    'data' => []
                                ]);
    }
}
