<?php

namespace App\Services;

interface GrPlantService {
    public function getOutstandingPoPlant($plantId, $filter = true);
    public function getOutstandingGr($plantId, $documentNumber);
    public function uploadGrPlant($companyId, $request);
}
