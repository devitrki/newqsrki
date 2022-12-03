<?php

namespace App\Http\Controllers\Financeacc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Cache;
use App\Library\Helper;

use App\Models\ConfigurationGroup;
use App\Models\Configuration;

class ConfigurationController extends Controller
{
    public function index(Request $request)
    {
        $userAuth = $request->get('userAuth');
        $configurationGroups = ConfigurationGroup::whereHas('configurations', function ($query) use ($userAuth) {
                                    return $query
                                            ->where('for', '=', 'financeacc')
                                            ->where('company_id', $userAuth->company_id_selected);
                                })
                                ->get();

        foreach ($configurationGroups as $configurationGroup) {
            $configurationGroup->configuration = DB::table('configurations')
                                                    ->where('configuration_group_id', $configurationGroup->id)
                                                    ->where('for', '=', 'financeacc')
                                                    ->where('company_id', $userAuth->company_id_selected)
                                                    ->get();
        }

        $dataview = [
            'menu_id' => $request->query('menuid'),
            'configuration_groups' => $configurationGroups
        ];
        return view('financeacc.configuration', $dataview)->render();
    }

    public function store(Request $request)
    {
        $userAuth = $request->get('userAuth');
        DB::beginTransaction();

        $success = true;
        foreach ($request->all() as $key => $val) {
            $configuration = Configuration::where('key', $key)->where('company_id', $userAuth->company_id_selected)->first();
            $configuration->value = $val;
            if (!$configuration->save()) {
                $success = false;
            }
        }
        if ($success) {
            DB::commit();
            Cache::forget('configuration_comp_' . 'financeacc' . $userAuth->company_id_selected);
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("finance accounting configuration")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("finance accounting configuration")]);
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }
}
