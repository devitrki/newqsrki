<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

use App\Repositories\SapRepositoryAppsImpl;

use App\Models\Material;

class MaterialServiceAppsImpl implements MaterialService
{
    public function syncMaterial($companyId)
    {
        $status = true;
        $message = '';

        $param = [];

        $sapRepository = new SapRepositoryAppsImpl();
        $sapResponse = $sapRepository->getMasterMaterial($param);

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
            $message = Lang::get("message.sync.failed", ["data" => Lang::get("material")]);
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    // utility
    public function syncMaterials($companyId, $materials){

        $result = true;
        // empty table convertion of material
        DB::table('material_convertions')->delete();

        foreach ($materials as $m) {

            $material_id = 0;

            // what it material already exist in database
            $cMaterial = DB::table('materials')->where('code', $m['MATNR'])->count();
            if( $cMaterial > 0 ){
                // replace data except code
                $material = Material::where('code', $m['MATNR'])->first();
                $material->description = $m['MAKTX'];
                $material->type = $m['MTART'];
                $material->group = $m['MATKL'];
                $material->uom = $m['MEINS'];
                $material->alternative_uom = $m['ZMEIN'];
                $material->consolidation_flag = $m['EXTWG'];
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
                $material->code = $m['MATNR'];
                $material->description = $m['MAKTX'];
                $material->type = $m['MTART'];
                $material->group = $m['MATKL'];
                $material->uom = $m['MEINS'];
                $material->alternative_uom = $m['ZMEIN'];
                $material->consolidation_flag = $m['EXTWG'];
                if( $material->save() ){
                    $material_id = $material->id;
                }else{
                    $result = false;
                    break;
                }
            }

            // insert convertion of material
            if($m['WERKS'] == 'R101' && $material_id != 0){
                $meins = explode(',', $m['ZMEIN']); #uom
                $meuns = explode(';', $m['ZMEUN']); #uom unit
                $c     = count($meins);
                if($c > 0){
                    $base_qty = $meuns[0];
                    for ($i = 0; $i < $c; $i++) {
                        $material_convertion = DB::table('material_convertions')->insert(
                            [
                                'material_id' => $material_id,
                                'base_qty' => $base_qty,
                                'base_uom' => $m['MEINS'],
                                'convertion_qty' => $meuns[$i],
                                'convertion_uom' => $meins[$i]
                            ]
                        );
                        if ($m['MEINS'] == $meins[$i]) {
                            $base_qty = $meuns[$i];
                        }
                    }

                    DB::table("material_convertions")
                            ->where('material_id', $material_id)
                            ->update(['base_qty' => $base_qty]);

                }
            }

        }

        return $result;

    }
}
