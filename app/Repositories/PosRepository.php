<?php

namespace App\Repositories;

interface PosRepository {
    public function initConnectionDB();
    public function getTotalPaymentByMethodPayment($storeID, $date, $methodPaymentName);
    public function getTotalQtyByMethodPayment($storeID, $date, $methodPaymentName);
    public function getTotalSales($storeID, $date);
    public function getDataSalesByMenu($storeID, $dateFrom, $dateUntil);
    public function getDataSalesByInventory($storeID, $dateFrom, $dateUntil);
    public function getDataSummaryPromotion($storeID, $date);
    public function getDataSalesMenuPerHour($storeID, $dateFrom, $dateUntil);
    public function getDataSalesInventoryPerHour($storeID, $dateFrom, $dateUntil);
    public function getDataVoid($storeID, $dateFrom, $dateUntil);
    public function getDataSalesPerHour($storeID, $dateFrom, $dateUntil);
}
