<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

use App\Repositories\SapRepositorySapImpl;

use App\Models\Plant;
use App\Models\Company;

class PlantServiceSapImpl implements PlantService
{
    public function syncPlant($companyId)
    {
        $status = true;
        $message = Lang::get("message.sync.success", ["data" => Lang::get("plant")]);

        $sapCodeComp = Company::getConfigByKey($companyId, 'SAP_CODE');
        if (!$sapCodeComp || $sapCodeComp == '') {
            return [
                'status' => false,
                'message' => Lang::get('Please set SAP_CODE in company configuration'),
            ];
        }

        $allowPlant = Company::getConfigByKey($companyId, 'ALLOW_PLANTS');
        if (!$allowPlant || $allowPlant == '') {
            return [
                'status' => false,
                'message' => Lang::get('Please set ALLOW_PLANTS in company configuration'),
            ];
        }

        $allowPlants = explode(',', $allowPlant);

        $payload = [
            'company_id' => $sapCodeComp
        ];

        $sapRepository = new SapRepositorySapImpl($companyId);
        $sapResponse = $sapRepository->getMasterPlant($payload);

        if ($sapResponse['status']) {
            $plants = $sapResponse['response'];

            DB::beginTransaction();
            $success = true;

            foreach ($plants as $p) {

                if(!in_array($p['plant_id'][0], $allowPlants) ){
                    continue;
                }

                $plant_type = Plant::getTypePlant($companyId, $p['plant_id']);

                $count_plant = Plant::where('code', $p['plant_id'])->where('company_id', $companyId)->count();
                if ($count_plant > 0) {
                    // update plant
                    $plant = Plant::where('code', $p['plant_id'])->first();
                    $plant->short_name = Plant::cleanInisialPlant($p['name']);
                    $plant->description = $p['name'];
                    $plant->initital = Plant::getInitialPlant($companyId, $p['plant_id']);
                    $plant->type = ($plant_type != 'Outlet') ? 2 : 1 ;
                    $plant->address = $p['address'] . ' ' . $p['city'];
                    $plant->cost_center = $p['cost_center_id'];
                    $plant->cost_center_desc = $p['cost_center_name'];
                    if(!$plant->save()){
                        $success = false;
                        break;
                    }
                } else {
                    // insert plant
                    $plant = new Plant;
                    $plant->company_id = $companyId;
                    $plant->code = $p['plant_id'];
                    $plant->short_name = Plant::cleanInisialPlant($p['name']);
                    $plant->description = $p['name'];
                    $plant->initital = Plant::getInitialPlant($companyId, $p['plant_id']);
                    $plant->type = ($plant_type != 'Outlet') ? 2 : 1 ;
                    $plant->address = $p['address'] . ' ' . $p['city'];
                    $plant->status = 1;
                    $plant->sloc_id_gr = ($plant_type != 'Outlet') ? 'DR01' : 'S001';
                    $plant->sloc_id_gr_vendor = ($plant_type != 'Outlet') ? 'DR01' : 'S001';
                    $plant->sloc_id_waste = ($plant_type != 'Outlet') ? 'DR01' : 'S001';
                    $plant->sloc_id_asset_mutation = ($plant_type != 'Outlet') ? 'DR01' : 'S001';
                    $plant->sloc_id_current_stock = ($plant_type != 'Outlet') ? 'DR01' : 'S001';
                    $plant->sloc_id_opname = ($plant_type != 'Outlet') ? 'DR01' : 'S001';
                    $plant->sloc_id_gi_plant = ($plant_type != 'Outlet') ? 'DR01' : 'S001';
                    $plant->cost_center = $p['cost_center_id'];
                    $plant->cost_center_desc = $p['cost_center_name'];

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
        } else {
            $status = false;
            $message = Lang::get("Sorry, an error occurred, please try again later");
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }
}
