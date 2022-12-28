<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

use App\Repositories\SapRepositorySapImpl;

use App\Models\Material;
use App\Models\Company;
use App\Models\Plant;

class MaterialServiceSapImpl implements MaterialService
{
    public function syncMaterial($companyId)
    {
        $status = true;
        $message = Lang::get("message.sync.success", ["data" => Lang::get("material")]);

        $sapCodeComp = Company::getConfigByKey($companyId, 'SAP_CODE');
        if (!$sapCodeComp || $sapCodeComp == '') {
            return [
                'status' => false,
                'message' => Lang::get('Please set SAP_CODE in company configuration'),
            ];
        }

        $plantIdOutlet = Plant::getCodeById(Plant::getFirstPlantIdSelect($companyId, 'outlet'));
        $plantIdDc = Plant::getCodeById(Plant::getFirstPlantIdSelect($companyId, 'dc'));

        $payload = [
            'company_id' => $sapCodeComp,
            'plant_id' => [$plantIdOutlet, $plantIdDc]
        ];

        $sapRepository = new SapRepositorySapImpl($companyId);
        $sapResponse = $sapRepository->getMasterMaterial($payload);

        if ($sapResponse['status']) {
            $materials = $sapResponse['response'];

            DB::beginTransaction();

            if($this->syncMaterials($companyId, $materials)){
                DB::commit();
                $status = true;
                $message = Lang::get("message.sync.success", ["data" => Lang::get("material")]);
            } else {
                DB::rollback();
                $status = false;
                $message = Lang::get("message.sync.failed", ["data" => Lang::get("material")]);
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

    public function syncMaterials($companyId, $materials){
        $result = true;
        // empty table convertion of material
        // DB::table('material_convertions')->delete();

        foreach ($materials as $m) {

            $material_code = substr($m['material_id'], -7);
            if ($m['uom_alternative_id']) {
                $material_alternative = $m['uom_alternative_id'];
            } else {
                $material_alternative = $m['uom_id'];
            }

            // what it material already exist in database
            $cMaterial = DB::table('materials')
                            ->where('company_id', $companyId)
                            ->where('code', $material_code)
                            ->count();
            if( $cMaterial > 0 ){
                // replace data except code
                $material = Material::where('code', $material_code)->first();
                $material->description = $m['name'];
                $material->type = $m['material_type_id'];
                $material->group = $m['material_group_id'];
                $material->uom = $m['uom_id'];
                $material->alternative_uom = $material_alternative;
                $material->consolidation_flag = '';
                if( $material->save() ){
                    $material_id = $material->id;
                }else{
                    $result = false;
                    break;
                }
            }else{
                // insert material
                $material = new Material;
                $material->company_id = $companyId;
                $material->code = $material_code;
                $material->description = $m['name'];
                $material->type = $m['material_type_id'];
                $material->group = $m['material_group_id'];
                $material->uom = $m['uom_id'];
                $material->alternative_uom = $material_alternative;
                $material->consolidation_flag = '';
                if( $material->save() ){
                    $material_id = $material->id;
                }else{
                    $result = false;
                    break;
                }
            }

        }

        return $result;

    }
}
