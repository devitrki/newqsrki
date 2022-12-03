<?php

namespace App\Repositories;

interface SapRepository {
    public function uploadPettyCash($payload);
    public function uploadWaste($payload);
    public function uploadOpname($payload);
    public function uploadGrVendor($payload);
    public function uploadGrPlant($payload);
    public function uploadGiPlant($payload);
    public function getOutstandingPoVendor($param);
    public function getOutstandingPoPlant($param);
    public function getOutstandingGr($param);
    public function getCurrentStockPlant($param);
}
