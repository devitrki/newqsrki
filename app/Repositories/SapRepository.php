<?php

namespace App\Repositories;

interface SapRepository {
    public function uploadPettyCash($payload);
    public function uploadWaste($payload);
    public function uploadOpname($payload);
    public function uploadGrVendor($payload);
    public function uploadGrPlant($payload);
    public function uploadGiPlant($payload);
    public function mutationAsset($payload);
    public function getMasterPlant($param);
    public function getMasterMaterial($param);
    public function getOutstandingPoVendor($param);
    public function getOutstandingPoPlant($param);
    public function getOutstandingPoPlantReport($param);
    public function getOutstandingGr($param);
    public function getCurrentStockPlant($param);
    public function getTransactionLog($payload);
    public function syncAsset($param);
}
