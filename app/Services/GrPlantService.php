<?php

namespace App\Services;

interface GrPlantService {
    public function getOutstandingPoPlant($plantId);
    public function getOutstandingGr($plantId, $documentNumber);
    public function uploadGrPlant($companyId, $request);
}
