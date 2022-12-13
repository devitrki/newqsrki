<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

use App\Repositories\SapRepositoryAppsImpl;

use App\Models\Plant;

class PlantServiceAppsImpl implements PlantService
{
    public function syncPlant($companyId)
    {
        $status = true;
        $message = '';

        $param = [];

        $sapRepository = new SapRepositoryAppsImpl();
        $sapResponse = $sapRepository->getMasterAsset($param);

        if ($sapResponse['status']) {
            $plants = $sapResponse['response'];

            DB::beginTransaction();
            $success = true;

            foreach ($plants as $p) {

                if(!in_array($p['WERKS'][0], ['F','R']) ){
                    continue;
                }

                $plant_type = Plant::getTypePlant($p['WERKS']);

                $count_plant = Plant::where('code', $p['WERKS'])->where('company_id', $companyId)->count();
                if ($count_plant > 0) {
                    // update plant
                    $plant = Plant::where('code', $p['WERKS'])->first();
                    $plant->short_name = Plant::cleanInisialPlant($p['NAME1']);
                    $plant->description = $p['NAME1'];
                    $plant->initital = Plant::getInitialPlant($p['WERKS']);
                    $plant->type = ($plant_type != 'Outlet') ? 2 : 1 ;
                    $plant->address = $p['STRAS'] . ' ' . $p['ORT01'];
                    if(!$plant->save()){
                        $success = false;
                        break;
                    }
                } else {
                    // insert plant
                    $plant = new Plant;
                    $plant->company_id = $companyId;
                    $plant->code = $p['WERKS'];
                    $plant->short_name = Plant::cleanInisialPlant($p['NAME1']);
                    $plant->description = $p['NAME1'];
                    $plant->initital = Plant::getInitialPlant($p['WERKS']);
                    $plant->type = ($plant_type != 'Outlet') ? 2 : 1 ;
                    $plant->address = $p['STRAS'] . ' ' . $p['ORT01'];
                    $plant->status = 1;
                    $plant->sloc_id_gi_plant = ($plant_type != 'Outlet') ? 'DR01' : 'S001';

                    if(!$plant->save()){
                        $success = false;
                        break;
                    }
                }
            }

            if($success){
                DB::commit();
                $status = true;
                $message = Lang::get("message.sync.success", ["data" => Lang::get("plant")]);
            }else{
                DB::rollback();
                $status = false;
                $message = Lang::get("message.sync.failed", ["data" => Lang::get("plant")]);
            }
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }
}
