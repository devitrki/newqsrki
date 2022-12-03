<?php

namespace App\Services;

interface PettycashService {
    public function uploadPettyCash($companyId, $picFa, $receiveDate, $idSubmiteds);
}
