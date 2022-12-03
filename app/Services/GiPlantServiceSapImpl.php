<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

use App\Library\Helper;

use App\Repositories\SapRepositorySapImpl;
use App\Entities\SapMiddleware;

use App\Models\Plant;
use App\Models\Material;
use App\Models\Company;
use App\Models\Inventory\GiPlant;
use App\Models\Inventory\GiPlantItem;

class GiPlantServiceSapImpl implements GiPlantService
{
    public function uploadGiPlant($giPlantId)
    {
        $status = true;
        $message = Lang::get("message.upload.success", ["data" => Lang::get("gi plant")]);

        $data_gi = GiPlant::getDataDetailById($giPlantId);

        $sapCodeComp = Company::getConfigByKey($data_gi['header']->company_id, 'sap_code');
        if (!$sapCodeComp || $sapCodeComp == '') {
            return [
                'status' => false,
                'message' => Lang::get('Please set sap_code in company configuration'),
            ];
        }

        $dataUpload = [];
        $coind = 1;
        $cokey = Helper::getKeySap();
        // foreach ($data_gi['items'] as $giItem) {
        //     $dataUpload[] = [
        //     ];
        //     $coind += 1;
        // }

        return [
            "status" => $status,
            "message" => $message
        ];
    }
}
