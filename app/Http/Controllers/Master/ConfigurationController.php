<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use App\Library\Helper;

use App\Models\ConfigurationGroup;
use App\Models\Configuration;


class ConfigurationController extends Controller
{
    public function index(Request $request)
    {
        $dataview = [
            'menu_id' => $request->query('menuid'),
            'configurations' => ConfigurationGroup::whereHas('configurations', function ($query) {
                return $query->where('for', '=', 'general_master');
            })->get()
        ];
        return view('master.configuration', $dataview)->render();
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        $success = true;
        foreach ($request->all() as $key => $val) {
            $configuration = Configuration::where('key', $key)->first();
            $configuration->value = $val;
            if (!$configuration->save()) {
                $success = false;
            }
        }
        if ($success) {
            DB::commit();
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("master configuration")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("master configuration")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }
}
