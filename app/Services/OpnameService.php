<?php

namespace App\Services;

interface OpnameService {
    public function getPreviewData($opnameId);
    public function uploadOpname($opnameId);
}
