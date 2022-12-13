<?php

namespace App\Services;

interface AssetService {
    public function syncAsset($plantId);
    public function mutationAsset($assetMutation);
    public function checkChangeAsset($id, $number_asset, $sub_number_asset, $from_plant_id, $from_cost_center_code, $to_plant_id, $to_cost_center_code);
}
