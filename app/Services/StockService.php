<?php

namespace App\Services;

interface StockService {
    public function getCurrentStockPlant($plantId, $type);
}
