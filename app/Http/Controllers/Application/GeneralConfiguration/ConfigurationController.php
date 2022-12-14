<?php

namespace App\Http\Controllers\Application\GeneralConfiguration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Configuration;

class ConfigurationController extends Controller
{
    public function index(Request $request){
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('application.configuration.configuration', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $query = DB::table('configurations')
                    ->join('configuration_groups', 'configuration_groups.id', '=', 'configurations.configuration_group_id')
                    ->leftJoin('companies', 'companies.id', '=', 'configurations.company_id')
                    ->select([
                        'configurations.id',
                        'configurations.company_id',
                        'configurations.configuration_group_id',
                        'configurations.for',
                        'configurations.type',
                        'configurations.label',
                        'configurations.description',
                        'configurations.key',
                        'configurations.value',
                        'configurations.option',
                        'configuration_groups.name as group_name',
                        'companies.name as company_name',
                    ])
                    ->orderByDesc('configurations.company_id')
                    ->orderBy('configurations.configuration_group_id');

        return Datatables::of($query)
                ->addIndexColumn()
                ->filterColumn('group_name', function ($query, $keyword) {
                    $sql = "configuration_groups.name like ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->rawColumns(['option', 'for', 'label', 'description'])
                ->make();
    }

    public function store(Request $request)
    {
        $request->validate([
                        'configuration_group' => 'required',
                        'for' => 'required',
                        'type' => 'required',
                        'label' => 'required',
                        'description' => 'required',
                        'key' => 'required',
                        'value' => 'required',
                    ]);

        $configuration = new Configuration;
        $configuration->company_id = $request->company;
        $configuration->configuration_group_id = $request->configuration_group;
        $configuration->for = $request->for;
        $configuration->type = $request->type;
        $configuration->label = $request->label;
        $configuration->description = $request->description;
        $configuration->key = $request->key;
        $configuration->value = $request->value;
        $configuration->option = $request->option;
        if ($configuration->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("configuration")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("configuration")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                        'configuration_group' => 'required',
                        'for' => 'required',
                        'type' => 'required',
                        'label' => 'required',
                        'description' => 'required',
                        'key' => 'required',
                        'value' => 'required',
                    ]);

        $configuration = Configuration::find($request->id);
        $configuration->company_id = $request->company;
        $configuration->configuration_group_id = $request->configuration_group;
        $configuration->for = $request->for;
        $configuration->type = $request->type;
        $configuration->label = $request->label;
        $configuration->description = $request->description;
        $configuration->key = $request->key;
        $configuration->value = $request->value;
        $configuration->option = $request->option;
        if ($configuration->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("configuration")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("configuration")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        $configuration = Configuration::find($id);
        if ($configuration->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("configuration")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("configuration")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function copy(Request $request)
    {
        $request->validate([
            'company_to' => 'required',
            'id_copies' => 'required',
        ]);

        $idCopies = json_decode($request->id_copies);
        $stat = 'success';

        DB::beginTransaction();

        foreach ($idCopies as $idCopy) {
            $configurationCopy = Configuration::find($idCopy);

            $countCheck =  DB::table('configurations')
                            ->where('company_id', $request->company_to)
                            ->where('for', $configurationCopy->for)
                            ->where('key', $configurationCopy->key)
                            ->count();

            if ($countCheck > 0) {
                continue;
            }

            $configuration = new Configuration;
            $configuration->company_id = $request->company_to;
            $configuration->configuration_group_id = $configurationCopy->configuration_group_id;
            $configuration->for = $configurationCopy->for;
            $configuration->type = $configurationCopy->type;
            $configuration->label = $configurationCopy->label;
            $configuration->description = $configurationCopy->description;
            $configuration->key = $configurationCopy->key;
            $configuration->value = $configurationCopy->value;
            $configuration->option = $configurationCopy->option;

            if (!$configuration->save()) {
                $stat = 'failed';
                break;
            }
        }

        if ($stat == 'success') {
            DB::commit();
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("configuration copy")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("configuration copy")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }
}
