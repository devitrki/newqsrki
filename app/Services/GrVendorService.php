<?php

namespace App\Services;

interface GrVendorService {
    public function getOutstandingPoVendor($plantId);
    public function uploadGrVendor($companyId, $request);
}
