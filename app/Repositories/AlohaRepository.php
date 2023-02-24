<?php

namespace App\Repositories;

use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Library\Helper;

use App\Models\Pos;
use App\Models\Plant;
use App\Models\Material;
use App\Models\PaymentPos;
use App\Models\OrderModePos;
use Illuminate\Support\Facades\Log;

class AlohaRepository implements PosRepository{
    public $posId;
    public $connectionDB = false;
    public $connectionName = '';

    public function __construct($posId)
    {
        $this->posId = $posId;
    }

    public function initConnectionDB()
    {
        try {
            $status = true;
            $message = '';

            $key_db_host = 'DB_HOST';
            $key_db_port = 'DB_PORT';
            $key_db_name = 'DB_NAME';
            $key_db_username = 'DB_USERNAME';
            $key_db_password = 'DB_PASSWORD';

            $db_host = '';
            $db_port = '';
            $db_name = '';
            $db_username = '';
            $db_password = '';

            $configurations = Pos::getConfigs($this->posId);
            $configurations = json_decode($configurations);
            foreach ($configurations as $k => $v) {
                if ($key_db_host == $k) {
                    $db_host = $v;
                }
                if ($key_db_port == $k) {
                    $db_port = $v;
                }
                if ($key_db_name == $k) {
                    $db_name = $v;
                }
                if ($key_db_username == $k) {
                    $db_username = $v;
                }
                if ($key_db_password == $k) {
                    $db_password = $v;
                }
            }

            if ($db_host | $db_port | $db_name | $db_username | $db_password) {
                $this->connectionDB = true;
            } else {
                $status = false;
                $message = 'Please check your pos aloha config';

                return [
                    'status' => $status,
                    'message' => $message
                ];
            }

            $connectionName = 'aloha_' . $this->posId;

            Config::set('database.connections.' . $connectionName, [
                'driver' => 'sqlsrv',
                'host' => $db_host,
                'port' => $db_port,
                'database' => $db_name,
                'username' => $db_username,
                'password' => $db_password,
                'charset' => 'utf8',
                'prefix' => '',
                'prefix_indexes' => true,
            ]);

            $this->connectionName = $connectionName;

        } catch (\Throwable $th) {
            $status = false;
            $message = 'Cannot connect db aloha';

            Log::error('Cannot connect db aloha');

        } finally {
            return [
                'status' => $status,
                'message' => $message
            ];
        }
    }

    // utility
    public function checkCompleteStoreAloha($customerCode, $date)
    {
        return DB::connection($this->connectionName)
                ->table('HstImportCompletion')
                ->leftJoin('gblStore', 'gblStore.storeID', 'HstImportCompletion.FKstoreID')
                ->where('gblStore.SecondaryStoreID', $customerCode)
                ->where('HstImportCompletion.dateofbusiness', $date)
                ->where('HstImportCompletion.ReplVerified', 1)
                ->count();
    }

    public function checkCorrectionStoreAloha($customerCode, $date)
    {
        return DB::connection($this->connectionName)
                ->table('dpvHstGndTender')
                ->leftJoin('Promotion', 'Promotion.PromotionId', 'dpvHstGndTender.typeid')
                ->leftJoin('gblStore', 'gblStore.storeID', 'dpvHstGndTender.FKStoreId')
                ->where('gblStore.SecondaryStoreID', $customerCode)
                ->where('dpvHstGndTender.dateofbusiness', $date)
                ->where('dpvHstGndTender.type', 2)
                ->where('dpvHstGndTender.amount', 0)
                ->whereBetween('dpvHstGndTender.typeid', [1001, 1199])
                ->count('dpvHstGndTender.FKStoreId');
    }

    public function getTaxTransaction($customerCode, $date)
    {
        return DB::connection($this->connectionName)
                ->table('dpvHstGndSale')
                ->leftJoin('gblStore', 'gblStore.storeID', 'dpvHstGndSale.FKstoreID')
                ->whereRaw('
                    gblStore.SecondaryStoreID = ? AND
                    dpvHstGndSale.DateOfBusiness = ? AND
                    dpvHstGndSale.type IN (4,5,6)
                ', [$customerCode, $date])
                ->selectRaw('
                    dpvHstGndSale.DateOfBusiness AS Date,
                    (
                    SELECT TOP 1 a.SystemDate
                    FROM dbo.dpvHstGndItem a
                    WHERE a.DateOfBusiness = dpvHstGndSale.DateOfBusiness AND
                            a.CheckNumber = dpvHstGndSale.CheckNumber AND
                            a.FKStoreID = dpvHstGndSale.FKStoreID
                    ) AS SystemDate,
                    dpvHstGndSale.CheckNumber,
                    ROUND(
                    SUM(
                    CASE
                        WHEN (dpvHstGndSale.type = 4) THEN dpvHstGndSale.Amount / 1.1
                        ELSE dpvHstGndSale.Amount
                    END
                    ), 0, 1) AS Total,
                    ROUND(
                    SUM(
                    CASE
                        WHEN (dpvHstGndSale.type = 4) THEN 0
                        ELSE dpvHstGndSale.Amount
                    END
                    ), 0, 1) AS TotalDiscount
                ')
                ->groupByRaw('
                    gblStore.SecondaryStoreID,
                    dpvHstGndSale.DateOfBusiness,
                    dpvHstGndSale.CheckNumber,
                    dpvHstGndSale.FKStoreID
                ')
                ->get();
    }

    // module
    public function getTotalPaymentByMethodPayment($storeID, $date, $methodPaymentName)
    {
        $payAmount = 0;

        $customerCode = Plant::getCustomerCodeById($storeID);

        $complete = $this->checkCompleteStoreAloha($customerCode, $date);
        $rangeTender = PaymentPos::getRangeTenderByMethodName($methodPaymentName);
        $rangeTender = explode(',', $rangeTender);

        if($complete > 0 && !empty($rangeTender)){
            // complete
            if ($methodPaymentName == 'DW') {
                $discountDpvHstComp =  DB::connection($this->connectionName)
                                            ->table('dpvHstComp')
                                            ->leftJoin('gblStore', 'gblStore.storeID', 'dpvHstComp.FKstoreID')
                                            ->leftJoin('Comp', 'Comp.CompId', 'dpvHstComp.FKCompId')
                                            ->where('gblStore.SecondaryStoreID', $customerCode)
                                            ->where('dpvHstComp.DateOfBusiness', $date)
                                            ->where('Comp.UserNumber', '<>', '401')
                                            ->sum(DB::raw('ROUND(dpvHstComp.Amount * 1.1, 0)'));

                $discountDpvHstPromotion =  DB::connection($this->connectionName)
                                                ->table('dpvHstPromotion')
                                                ->leftJoin('gblStore', 'gblStore.storeID', 'dpvHstPromotion.FKstoreID')
                                                ->where('gblStore.SecondaryStoreID', $customerCode)
                                                ->where('dpvHstPromotion.DateOfBusiness', $date)
                                                ->sum(DB::raw('ROUND(dpvHstPromotion.Amount * 1.1, 0)'));

                $payAmount = $discountDpvHstComp + $discountDpvHstPromotion;

            } else {
                $qPayment =  DB::connection($this->connectionName)
                                ->table('dpvHstTender')
                                ->leftJoin('gblStore', 'gblStore.storeID', 'dpvHstTender.FKstoreID')
                                ->where('gblStore.SecondaryStoreID', $customerCode)
                                ->where('dpvHstTender.DateOfBusiness', $date);

                if( count($rangeTender) > 1 ){
                    $qPayment = $qPayment->whereBetween('dpvHstTender.FKTenderId', [$rangeTender[0], $rangeTender[1]]);
                } else {
                    $qPayment = $qPayment->where('dpvHstTender.FKTenderId', $rangeTender[0]);
                }
                $payAmount = $qPayment->sum('dpvHstTender.Amount');

                if ($methodPaymentName == 'DG') {
                    $discountDpvHstComp =  DB::connection($this->connectionName)
                                                ->table('dpvHstComp')
                                                ->leftJoin('gblStore', 'gblStore.storeID', 'dpvHstComp.FKstoreID')
                                                ->leftJoin('Comp', 'Comp.CompId', 'dpvHstComp.FKCompId')
                                                ->where('gblStore.SecondaryStoreID', $customerCode)
                                                ->where('dpvHstComp.DateOfBusiness', $date)
                                                ->where('Comp.UserNumber', '401')
                                                ->sum(DB::raw('ROUND(dpvHstComp.Amount * 1.1, 0)'));

                    $payAmount += $discountDpvHstComp;
                }

            }
        }

        return (int)$payAmount;
    }

    public function getTotalQtyByMethodPayment($storeID, $date, $methodPaymentName)
    {
        $payQty = 0;

        $customerCode = Plant::getCustomerCodeById($storeID);

        $complete = $this->checkCompleteStoreAloha($customerCode, $date);
        $rangeTender = PaymentPos::getRangeTenderByMethodName($methodPaymentName);
        $rangeTender = explode(',', $rangeTender);

        if($complete > 0 && !empty($rangeTender)){
            // complete
            if ($methodPaymentName != 'DW') {
                $qPayment =  DB::connection($this->connectionName)
                                ->table('dpvHstTender')
                                ->leftJoin('gblStore', 'gblStore.storeID', 'dpvHstTender.FKstoreID')
                                ->where('gblStore.SecondaryStoreID', $customerCode)
                                ->where('dpvHstTender.DateOfBusiness', $date);

                if( count($rangeTender) > 1 ){
                    $qPayment = $qPayment->whereBetween('dpvHstTender.FKTenderId', [$rangeTender[0], $rangeTender[1]]);
                } else {
                    $qPayment = $qPayment->where('dpvHstTender.FKTenderId', $rangeTender[0]);
                }
                $payQty = $qPayment->sum('dpvHstTender.lCount');

                if ($methodPaymentName == 'DG') {
                    $qtyDpvHstComp =  DB::connection($this->connectionName)
                                        ->table('dpvHstComp')
                                        ->leftJoin('gblStore', 'gblStore.storeID', 'dpvHstComp.FKstoreID')
                                        ->leftJoin('Comp', 'Comp.CompId', 'dpvHstComp.FKCompId')
                                        ->where('gblStore.SecondaryStoreID', $customerCode)
                                        ->where('dpvHstComp.DateOfBusiness', $date)
                                        ->where('Comp.UserNumber', '401')
                                        ->sum('dpvHstComp.lCount');

                    $payQty += $qtyDpvHstComp;
                }

            }
        }

        return $payQty;
    }

    public function getTotalSales($storeID, $date)
    {
        $customerCode = Plant::getCustomerCodeById($storeID);

        $salesItem =  DB::connection($this->connectionName)
                        ->table('dpvHstGndItem')
                        ->leftJoin('gblStore', 'gblStore.storeID', 'dpvHstGndItem.FKstoreID')
                        ->where('gblStore.SecondaryStoreID', $customerCode)
                        ->where('dpvHstGndItem.DateOfBusiness', $date)
                        ->sum(DB::raw('ROUND(dpvHstGndItem.Price * 1.1, 0)'));

        $salesCharge = DB::connection($this->connectionName)
                        ->table('dpvHstSalesSummary')
                        ->leftJoin('gblStore', 'gblStore.storeID', 'dpvHstSalesSummary.FKstoreID')
                        ->where('gblStore.SecondaryStoreID', $customerCode)
                        ->where('dpvHstSalesSummary.DateOfBusiness', $date)
                        ->where('dpvHstSalesSummary.Type', 18)
                        ->sum(DB::raw('ROUND(dpvHstSalesSummary.Amount * 1.1, 0)'));

        $totalSales = $salesItem + $salesCharge;

        return $totalSales;
    }

    public function getDataSalesByMenu($storeID, $dateFrom, $dateUntil)
    {
        $customerCode = Plant::getCustomerCodeById($storeID);

        $results = DB::connection($this->connectionName)
                        ->select( DB::raw("
                        SELECT
                            un.SecondaryStoreID,
                            'date' AS DateOfBusiness,
                            un.ShortName,
                            un.BohName,
                            SUM(un.Quantity) AS Quantity,
                            SUM(un.GrossSales) AS GrossSales,
                            SUM(un.Discount) AS Discount,
                            SUM(un.NettoSales) AS NettoSales,
                            SUM(un.Tax) AS Tax,
                            un.ItemType,
                            un.SalesMode
                        FROM
                        (
                            SELECT
                                aln.SecondaryStoreID,
                                aln.ShortName,
                                aln.BohName,
                                aln.Quantity,
                                aln.GrossSales,
                                aln.Discount,
                                aln.NettoSales,
                                aln.Tax,
                                aln.ItemType,
                                aln.SalesMode
                            FROM
                            (
                            SELECT
                                d.SecondaryStoreID,
                                b.ShortName,
                                b.BohName,
                                sum(
                                    CASE
                                    WHEN
                                        a.Type = 1
                                    THEN
                                        a.Quantity * -1
                                    ELSE
                                        a.Quantity
                                    END
                                ) AS Quantity,
                                SUM(a.Price * 1.1) AS GrossSales,
                                0 AS Discount,
                                sum(a.Price) AS NettoSales,
                                SUM(a.Price * 0.1) AS Tax,
                                'NORM' AS ItemType,
                                c.Name AS SalesMode
                            FROM RF_Datamart.dbo.dpvHstGndItem a
                            LEFT JOIN RF_Datamart.dbo.Item b ON a.FKItemId = b.ItemId
                            LEFT JOIN RF_Datamart.dbo.OrderMode c ON a.FKOrderModeId = c.OrderModeId
                            LEFT JOIN RF_Datamart.dbo.gblStore d ON a.FKstoreID = d.storeID
                            WHERE a.QSQuickComboID = 0 AND a.ParentId = 0 AND d.SecondaryStoreID = :storeId AND a.DateOfBusiness BETWEEN :dateFrom AND :dateUntil
                            GROUP BY d.SecondaryStoreID,
                                        b.ShortName,
                                        b.BohName,
                                        c.Name
                            ) aln

                            UNION ALL

                            SELECT normcmb.SecondaryStoreID,
                                    normcmb.ShortName,
                                    normcmb.BohName,
                                    SUM(normcmb.Quantity) AS Quantity,
                                    SUM(normcmb.NettoSales * 1.1) AS GrossSales,
                                    0 AS Discount,
                                    SUM(normcmb.NettoSales) AS NettoSales,
                                    SUM(normcmb.NettoSales * 0.1) AS Tax,
                                    normcmb.ItemType,
                                    CASE
                                        WHEN normcmb.SalesMode = 1 THEN 'DINE IN'
                                        WHEN normcmb.SalesMode = 2 THEN 'TAKE AWAY'
                                        WHEN normcmb.SalesMode = 3 THEN 'DELIVERY'
                                        WHEN normcmb.SalesMode = 4 THEN 'Drive Thru'
                                        WHEN normcmb.SalesMode = 5 THEN 'GO FOOD'
                                        WHEN normcmb.SalesMode = 6 THEN 'GRAB FOOD'
                                        WHEN normcmb.SalesMode = 7 THEN 'BIG ORDER'
                                        WHEN normcmb.SalesMode = 8 THEN 'CATERING'
                                        WHEN normcmb.SalesMode = 9 THEN 'SOFT SERV'
                                        WHEN normcmb.SalesMode = 10 THEN 'SHOPEE FOOD'
                                        WHEN normcmb.SalesMode = 51 THEN 'RB-DINE IN'
                                        WHEN normcmb.SalesMode = 52 THEN 'RB-TAKE AWAY'
                                        WHEN normcmb.SalesMode = 53 THEN 'RB-GRAB FOOD'
                                        WHEN normcmb.SalesMode = 54 THEN 'RB-GO FOOD'
                                        WHEN normcmb.SalesMode = 71 THEN 'KIOSK - DI'
                                        WHEN normcmb.SalesMode = 72 THEN 'KIOSK - TA'
                                    END AS SalesMode
                            FROM (
                                SELECT
                                    d.SecondaryStoreID,
                                    f.name AS ShortName,
                                    CAST(
                                        CASE
                                        WHEN
                                            f.EXPORTID IS NULL
                                        THEN
                                            '-'
                                        ELSE
                                            f.EXPORTID
                                        END AS VARCHAR(26)
                                    ) AS BohName,
                                    a.lcount AS Quantity,
                                    0 AS GrossSales,
                                    0 AS Discount,
                                    a.amount AS NettoSales,
                                    0 AS Tax,
                                    'NORM' AS ItemType,
                                    o.FKOrderModeId AS SalesMode
                                FROM RF_Datamart.dbo.dpvHstGndSale a
                                LEFT JOIN RF_Datamart.dbo.gblStore d ON a.FKstoreID = d.storeID
                                LEFT JOIN RF_Datamart.dbo.Promotion f ON a.TypeID = f.PromotionID
                                JOIN (
                                    SELECT DISTINCT sg.CheckNumber, sg.FKOrderModeId, sg.DateOfBusiness
                                    FROM RF_Datamart.dbo.dpvHstGndItem sg
                                    LEFT JOIN RF_Datamart.dbo.gblStore sd ON sg .FKstoreID = sd.storeID
                                    WHERE sd.SecondaryStoreID = :storeId2 AND sg.DateOfBusiness BETWEEN :dateFrom2 AND :dateUntil2
                                ) o ON a.CheckNumber = o.CheckNumber AND o.DateOfBusiness = a.DateOfBusiness
                                WHERE d.SecondaryStoreID = :storeId3 AND a.DateOfBusiness BETWEEN :dateFrom3 AND :dateUntil3 AND a.type = 87
                            ) normcmb
                            GROUP BY normcmb.SecondaryStoreID,
                                    normcmb.ShortName,
                                    normcmb.BohName,
                                    normcmb.ItemType,
                                    normcmb.SalesMode,
                                    CASE
                                        WHEN normcmb.SalesMode = 1 THEN 'DINE IN'
                                        WHEN normcmb.SalesMode = 2 THEN 'TAKE AWAY'
                                        WHEN normcmb.SalesMode = 3 THEN 'DELIVERY'
                                        WHEN normcmb.SalesMode = 4 THEN 'Drive Thru'
                                        WHEN normcmb.SalesMode = 5 THEN 'GO FOOD'
                                        WHEN normcmb.SalesMode = 6 THEN 'GRAB FOOD'
                                        WHEN normcmb.SalesMode = 7 THEN 'BIG ORDER'
                                        WHEN normcmb.SalesMode = 8 THEN 'CATERING'
                                        WHEN normcmb.SalesMode = 9 THEN 'SOFT SERV'
                                        WHEN normcmb.SalesMode = 10 THEN 'SHOPEE FOOD'
                                        WHEN normcmb.SalesMode = 51 THEN 'RB-DINE IN'
                                        WHEN normcmb.SalesMode = 52 THEN 'RB-TAKE AWAY'
                                        WHEN normcmb.SalesMode = 53 THEN 'RB-GRAB FOOD'
                                        WHEN normcmb.SalesMode = 54 THEN 'RB-GO FOOD'
                                        WHEN normcmb.SalesMode = 71 THEN 'KIOSK - DI'
                                        WHEN normcmb.SalesMode = 72 THEN 'KIOSK - TA'
                                    END

                            UNION ALL

                            SELECT
                                tc.SecondaryStoreID,
                                tc.ShortName,
                                tc.BohName,
                                tc.Quantity,
                                tc.GrossSales,
                                tc.Discount,
                                tc.NettoSales,
                                tc.Tax,
                                tc.ItemType,
                                tc.SalesMode
                            FROM
                            (
                            SELECT
                                b.SecondaryStoreID,
                                'Take Away Charge' AS ShortName,
                                '9999993' AS BohName,
                                sum(a.lCount) AS Quantity,
                                sum(a.Amount*1.1) AS GrossSales,
                                0 AS Discount,
                                sum(a.Amount) AS NettoSales,
                                sum(a.Amount*0.1) AS Tax,
                                'NORM' AS ItemType,
                                '' AS SalesMode
                            from RF_Datamart.dbo.dpvHstSalesSummary a
                            LEFT JOIN RF_Datamart.dbo.gblStore b ON a.FKstoreID = b.storeID
                            WHERE a.Type = 18 AND a.TypeId <> 3 AND  b.SecondaryStoreID = :storeId4 AND a.DateOfBusiness BETWEEN :dateFrom4 AND :dateUntil4
                            GROUP BY b.SecondaryStoreID
                            ) tc

                            UNION ALL

                            SELECT
                                dlv.SecondaryStoreID,
                                dlv.ShortName,
                                dlv.BohName,
                                dlv.Quantity,
                                dlv.GrossSales,
                                dlv.Discount,
                                dlv.NettoSales,
                                dlv.Tax,
                                dlv.ItemType,
                                dlv.SalesMode
                            FROM
                            (
                            SELECT
                                b.SecondaryStoreID,
                                'Delivery Charge' AS ShortName,
                                '9999991' AS BohName,
                                sum(a.lCount) AS Quantity,
                                sum(a.Amount*1.1) AS GrossSales,
                                0 AS Discount,
                                sum(a.Amount) AS NettoSales,
                                sum(a.Amount*0.1) AS Tax,
                                'NORM' AS ItemType,
                                '' AS SalesMode
                            from RF_Datamart.dbo.dpvHstSalesSummary a
                            LEFT JOIN RF_Datamart.dbo.gblStore b ON a.FKstoreID = b.storeID
                            WHERE a.Type = 18 AND a.TypeId = 3 AND  b.SecondaryStoreID = :storeId5 AND a.DateOfBusiness BETWEEN :dateFrom5 AND :dateUntil5
                            GROUP BY b.SecondaryStoreID
                            ) dlv

                        ) un
                        WHERE un.BohName NOT IN ('8888888', '9999999')
                        GROUP BY un.SecondaryStoreID,
                                    un.ShortName,
                                    un.BohName,
                                    un.ItemType,
                                    un.SalesMode
                        ORDER BY un.BohName
                        "), [
                            'storeId' => $customerCode,
                            'dateFrom' => $dateFrom,
                            'dateUntil' => $dateUntil,
                            'storeId2' => $customerCode,
                            'dateFrom2' => $dateFrom,
                            'dateUntil2' => $dateUntil,
                            'storeId3' => $customerCode,
                            'dateFrom3' => $dateFrom,
                            'dateUntil3' => $dateUntil,
                            'storeId4' => $customerCode,
                            'dateFrom4' => $dateFrom,
                            'dateUntil4' => $dateUntil,
                            'storeId5' => $customerCode,
                            'dateFrom5' => $dateFrom,
                            'dateUntil5' => $dateUntil,
                        ]);

        return $results;
    }

    public function getDataSalesByInventory($storeID, $dateFrom, $dateUntil)
    {
        $customerCode = Plant::getCustomerCodeById($storeID);

        $results = DB::connection($this->connectionName)
                        ->select( DB::raw("
                        SELECT
                            un.SecondaryStoreID,
                            un.DateOfBusiness,
                            un.ShortName,
                            un.BohName,
                            SUM(un.Quantity) AS Quantity,
                            SUM(un.GrossSales) AS GrossSales,
                            SUM(un.Discount) AS Discount,
                            SUM(un.NettoSales) AS NettoSales,
                            SUM(un.Tax) AS Tax,
                            un.ItemType,
                            un.SalesMode
                        FROM
                        (
                            SELECT
                                aln.SecondaryStoreID,
                                aln.DateOfBusiness,
                                aln.ShortName,
                                aln.BohName,
                                aln.Quantity,
                                aln.GrossSales,
                                aln.Discount,
                                aln.NettoSales,
                                aln.Tax,
                                aln.ItemType,
                                aln.SalesMode
                            FROM
                            (
                            SELECT
                                d.SecondaryStoreID,
                                'date' AS DateOfBusiness,
                                b.ShortName,
                                b.BohName,
                                sum(
                                    CASE
                                    WHEN
                                        a.Type = 1
                                    THEN
                                        a.Quantity * -1
                                    ELSE
                                        a.Quantity
                                    END
                                ) AS Quantity,
                                SUM(a.Price * 1.1) AS GrossSales,
                                0 AS Discount,
                                sum(a.Price) AS NettoSales,
                                SUM(a.Price * 0.1) AS Tax,
                                'ERLA' AS ItemType,
                                c.Name AS SalesMode
                            FROM RF_Datamart.dbo.dpvHstGndItem a
                            LEFT JOIN RF_Datamart.dbo.Item b ON a.FKItemId = b.ItemId
                            LEFT JOIN RF_Datamart.dbo.OrderMode c ON a.FKOrderModeId = c.OrderModeId
                            LEFT JOIN RF_Datamart.dbo.gblStore d ON a.FKstoreID = d.storeID
                            WHERE a.QSQuickComboID = 0 AND a.ParentId <> 0 AND d.SecondaryStoreID = :storeId AND a.DateOfBusiness BETWEEN :dateFrom AND :dateUntil
                            GROUP BY d.SecondaryStoreID,
                                        b.ShortName,
                                        b.BohName,
                                        c.Name
                            ) aln

                            UNION ALL

                            SELECT
                                ale.SecondaryStoreID,
                                ale.DateOfBusiness,
                                ale.ShortName,
                                ale.BohName,
                                ale.Quantity,
                                ale.GrossSales,
                                ale.Discount,
                                ale.NettoSales,
                                ale.Tax,
                                ale.ItemType,
                                ale.SalesMode
                            FROM
                            (
                            SELECT
                                d.SecondaryStoreID,
                                'date' AS DateOfBusiness,
                                b.ShortName,
                                b.BohName,
                                sum(
                                    CASE
                                    WHEN
                                        a.Type = 1
                                    THEN
                                        a.Quantity * -1
                                    ELSE
                                        a.Quantity
                                    END
                                ) AS Quantity,
                                0 AS GrossSales,
                                0 AS Discount,
                                0 AS NettoSales,
                                0 AS Tax,
                                'ERLA' AS ItemType,
                                c.Name AS SalesMode
                            FROM RF_Datamart.dbo.dpvHstGndItem a
                            LEFT JOIN RF_Datamart.dbo.Item b ON a.FKItemId = b.ItemId
                            LEFT JOIN RF_Datamart.dbo.OrderMode c ON a.FKOrderModeId = c.OrderModeId
                            LEFT JOIN RF_Datamart.dbo.gblStore d ON a.FKstoreID = d.storeID
                            WHERE a.QSQuickComboID = 0 AND a.ParentId = 0 AND b.ExportId <> 1 AND d.SecondaryStoreID = :storeId2 AND a.DateOfBusiness BETWEEN :dateFrom2 AND :dateUntil2
                            GROUP BY d.SecondaryStoreID,
                                        b.ShortName,
                                        b.BohName,
                                        c.Name
                            ) ale

                            UNION ALL

                            SELECT
                                cmberla.SecondaryStoreID,
                                cmberla.DateOfBusiness,
                                cmberla.ShortName,
                                cmberla.BohName,
                                cmberla.Quantity,
                                cmberla.GrossSales,
                                cmberla.Discount,
                                cmberla.NettoSales,
                                cmberla.Tax,
                                cmberla.ItemType,
                                cmberla.SalesMode
                            FROM
                            (
                            SELECT
                                d.SecondaryStoreID,
                                'date' AS DateOfBusiness,
                                b.ShortName,
                                b.BohName AS BohName,
                                sum(
                                    CASE
                                    WHEN
                                        a.Type = 1
                                    THEN
                                        a.Quantity * -1
                                    ELSE
                                        a.Quantity
                                    END
                                ) AS Quantity,
                                0 AS GrossSales,
                                0 AS Discount,
                                0 AS NettoSales,
                                0 AS Tax,
                                'ERLA' AS ItemType,
                                c.Name AS SalesMode
                            FROM RF_Datamart.dbo.dpvHstGndItem a
                            LEFT JOIN RF_Datamart.dbo.Item b ON a.FKItemId = b.ItemId
                            LEFT JOIN RF_Datamart.dbo.OrderMode c ON a.FKOrderModeId = c.OrderModeId
                            LEFT JOIN RF_Datamart.dbo.gblStore d ON a.FKstoreID = d.storeID
                            LEFT JOIN RF_Datamart.dbo.QuickComboPromotion e ON a.QSQuickComboID = e.FKPromotionID AND a.FKstoreID = e.FKstoreID
                            LEFT JOIN RF_Datamart.dbo.Promotion f ON a.QSQuickComboID = f.PromotionID
                            WHERE a.QSQuickComboID <> 0 AND b.BohName <> '9999999' AND b.ExportId <> 1 AND d.SecondaryStoreID = :storeId3 AND a.DateOfBusiness BETWEEN :dateFrom3 AND :dateUntil3
                            GROUP BY d.SecondaryStoreID,
                                        b.ShortName,
                                        b.BohName,
                                        c.Name
                            ) cmberla

                        ) un
                        WHERE un.BohName NOT IN ('8888888', '9999999')
                        GROUP BY un.SecondaryStoreID,
                                    un.DateOfBusiness,
                                    un.ShortName,
                                    un.BohName,
                                    un.ItemType,
                                    un.SalesMode
                        ORDER BY un.BohName
                        "), [
                            'storeId' => $customerCode,
                            'dateFrom' => $dateFrom,
                            'dateUntil' => $dateUntil,
                            'storeId2' => $customerCode,
                            'dateFrom2' => $dateFrom,
                            'dateUntil2' => $dateUntil,
                            'storeId3' => $customerCode,
                            'dateFrom3' => $dateFrom,
                            'dateUntil3' => $dateUntil
                        ]);

        return $results;
    }

    public function getDataSummaryPromotion($storeID, $date)
    {
        $customerCode = Plant::getCustomerCodeById($storeID);

        $items = [];

        $dpvHstTenders = DB::connection($this->connectionName)
                            ->table('dpvHstTender')
                            ->leftJoin('Tender', 'Tender.TenderId', 'dpvHstTender.FKTenderId')
                            ->leftJoin('gblStore', 'gblStore.storeID', 'dpvHstTender.FKstoreID')
                            ->where('gblStore.SecondaryStoreID', $customerCode)
                            ->whereDate('dpvHstTender.DateOfBusiness', $date)
                            ->select('Tender.name', DB::raw('SUM(dpvHstTender.lCount) as count'), DB::raw('SUM( IIF(Tender.UserNumber = 401, dpvHstTender.Amount * 1.1, dpvHstTender.Amount) ) as amount'))
                            ->groupBy('Tender.name')
                            ->get();

        foreach ($dpvHstTenders as $dpvHstTender) {
            $items[] = (object)[
                'PayTypeName' => $dpvHstTender->name,
                'TotalQty' => $dpvHstTender->count,
                'PayAmount' => $dpvHstTender->amount,
            ];
        }

        $dpvHstComps = DB::connection($this->connectionName)
                        ->table('dpvHstComp')
                        ->leftJoin('Comp', 'Comp.CompId', 'dpvHstComp.FKCompId')
                        ->leftJoin('gblStore', 'gblStore.storeID', 'dpvHstComp.FKstoreID')
                        ->where('gblStore.SecondaryStoreID', $customerCode)
                        ->whereDate('dpvHstComp.DateOfBusiness', $date)
                        ->select('Comp.name', DB::raw('SUM(dpvHstComp.lCount) as count'), DB::raw('SUM(dpvHstComp.Amount*1.1) as amount'))
                        ->groupBy('Comp.name')
                        ->get();

        $totalAmount = 0;
        $totalQty = 0;

        foreach ($dpvHstComps as $dpvHstComp) {
            $totalAmount += $dpvHstComp->amount;
            $totalQty += $dpvHstComp->count;
        }

        if( $totalAmount > 0 ){
            $items[] = (object)[
                'PayTypeName' => 'COMPLIMENT',
                'TotalQty' => $totalQty,
                'PayAmount' => $totalAmount,
            ];
        }

        $dpvHstPromotions = DB::connection($this->connectionName)
                            ->table('dpvHstPromotion')
                            ->leftJoin('Promotion', 'Promotion.PromotionId', 'dpvHstPromotion.FKPromotionId')
                            ->leftJoin('gblStore', 'gblStore.storeID', 'dpvHstPromotion.FKstoreID')
                            ->where('gblStore.SecondaryStoreID', $customerCode)
                            ->whereDate('dpvHstPromotion.DateOfBusiness', $date)
                            ->where('dpvHstPromotion.Amount', '>', 0)
                            ->select('Promotion.name', DB::raw('SUM(dpvHstPromotion.lCount) as count'), DB::raw('SUM(dpvHstPromotion.Amount*1.1) as amount'))
                            ->groupBy('Promotion.name')
                            ->get();

        $totalAmount = 0;
        $totalQty = 0;

        foreach ($dpvHstPromotions as $dpvHstPromotion) {
            $totalAmount += $dpvHstPromotion->amount;
            $totalQty += $dpvHstPromotion->count;
        }

        if( $totalAmount > 0 ){
            $items[] = (object)[
                'PayTypeName' => 'DISCOUNT',
                'TotalQty' => $totalQty,
                'PayAmount' => $totalAmount,
            ];
        }

        return $items;

    }

    public function getDataSalesMenuPerHour($storeID, $dateFrom, $dateUntil)
    {
        $customerCode = Plant::getCustomerCodeById($storeID);

        $items = [];
        $total = 0;

        $dpvHstGndItems = DB::connection($this->connectionName)
                            ->table('dpvHstGndItem')
                            ->leftJoin('Item', 'Item.ItemId', 'dpvHstGndItem.FKItemId')
                            ->leftJoin('gblStore', 'gblStore.storeID', 'dpvHstGndItem.FKstoreID')
                            ->where('gblStore.SecondaryStoreID', $customerCode)
                            ->where('dpvHstGndItem.QSQuickComboID', 0)
                            ->where('dpvHstGndItem.ParentId', 0)
                            ->whereBetween('dpvHstGndItem.DateOfBusiness', [$dateFrom . ' 00:00:00', $dateUntil . ' 23:59:59'])
                            ->select('Item.BohName as ProductCode', 'dpvHstGndItem.hour', DB::raw('SUM( IIF(dpvHstGndItem.Type = 1, dpvHstGndItem.Quantity * -1, dpvHstGndItem.Quantity) ) as quantity'))
                            ->groupBy('Item.BohName', 'dpvHstGndItem.hour');

        $dpvHstGndSales = DB::connection($this->connectionName)
                            ->table('dpvHstGndSale')
                            ->leftJoin('gblStore', 'gblStore.storeID', 'dpvHstGndSale.FKstoreID')
                            ->leftJoin('Promotion', 'Promotion.PromotionID', 'dpvHstGndSale.TypeID')
                            ->where('gblStore.SecondaryStoreID', $customerCode)
                            ->where('dpvHstGndSale.Type', 87)
                            ->whereBetween('dpvHstGndSale.DateOfBusiness', [$dateFrom . ' 00:00:00', $dateUntil . ' 23:59:59'])
                            ->select(DB::raw('IIF(Promotion.EXPORTID IS NULL, \'-\', Promotion.EXPORTID) as ProductCode'), 'dpvHstGndSale.OrderHour as hour', DB::raw('SUM(dpvHstGndSale.lcount) as quantity'))
                            ->groupBy(DB::raw('IIF(Promotion.EXPORTID IS NULL, \'-\', Promotion.EXPORTID)'), 'dpvHstGndSale.OrderHour')
                            ->unionAll($dpvHstGndItems)
                            ->get();

        foreach ($dpvHstGndSales as $dpvHstGndSale) {

            $total = $dpvHstGndSale->quantity;
            if( isset( $items[$dpvHstGndSale->ProductCode]['h' . $dpvHstGndSale->hour] ) ){
                $total += $items[$dpvHstGndSale->ProductCode]['h' . $dpvHstGndSale->hour];
            }

            $items[$dpvHstGndSale->ProductCode]['ProductName'] = Material::getDescByCode($dpvHstGndSale->ProductCode);
            $items[$dpvHstGndSale->ProductCode]['h' . $dpvHstGndSale->hour] = $total;
        }

        return $items;
    }

    public function getDataSalesInventoryPerHour($storeID, $dateFrom, $dateUntil)
    {
        $customerCode = Plant::getCustomerCodeById($storeID);

        $items = [];
        $total = 0;

        $dpvHstGndItems = DB::connection($this->connectionName)
                            ->table('dpvHstGndItem')
                            ->leftJoin('Item', 'Item.ItemId', 'dpvHstGndItem.FKItemId')
                            ->leftJoin('gblStore', 'gblStore.storeID', 'dpvHstGndItem.FKstoreID')
                            ->where('gblStore.SecondaryStoreID', $customerCode)
                            ->where('dpvHstGndItem.QSQuickComboID', 0)
                            ->where('dpvHstGndItem.ParentId', '<>', 0)
                            ->whereBetween('dpvHstGndItem.DateOfBusiness', [$dateFrom . ' 00:00:00', $dateUntil . ' 23:59:59'])
                            ->select('Item.BohName as ProductCode', 'dpvHstGndItem.hour', DB::raw('SUM( IIF(dpvHstGndItem.Type = 1, dpvHstGndItem.Quantity * -1, dpvHstGndItem.Quantity) ) as quantity'))
                            ->groupBy('Item.BohName', 'dpvHstGndItem.hour');

        $dpvHstGndItemAles = DB::connection($this->connectionName)
                                ->table('dpvHstGndItem')
                                ->leftJoin('Item', 'Item.ItemId', 'dpvHstGndItem.FKItemId')
                                ->leftJoin('gblStore', 'gblStore.storeID', 'dpvHstGndItem.FKstoreID')
                                ->where('gblStore.SecondaryStoreID', $customerCode)
                                ->where('dpvHstGndItem.QSQuickComboID', 0)
                                ->where('dpvHstGndItem.ParentId', 0)
                                ->where('Item.ExportId', '<>', 1)
                                ->whereBetween('dpvHstGndItem.DateOfBusiness', [$dateFrom . ' 00:00:00', $dateUntil . ' 23:59:59'])
                                ->select('Item.BohName as ProductCode', 'dpvHstGndItem.hour', DB::raw('SUM( IIF(dpvHstGndItem.Type = 1, dpvHstGndItem.Quantity * -1, dpvHstGndItem.Quantity) ) as quantity'))
                                ->groupBy('Item.BohName', 'dpvHstGndItem.hour');

        $dpvHstGndItemCmbAles = DB::connection($this->connectionName)
                                ->table('dpvHstGndItem')
                                ->leftJoin('Item', 'Item.ItemId', 'dpvHstGndItem.FKItemId')
                                ->leftJoin('gblStore', 'gblStore.storeID', 'dpvHstGndItem.FKstoreID')
                                ->where('gblStore.SecondaryStoreID', $customerCode)
                                ->where('dpvHstGndItem.QSQuickComboID', '<>', 0)
                                ->whereNotIn('Item.BohName', ['8888888', '9999999'])
                                ->where('Item.ExportId', '<>', 1)
                                ->whereBetween('dpvHstGndItem.DateOfBusiness', [$dateFrom . ' 00:00:00', $dateUntil . ' 23:59:59'])
                                ->select('Item.BohName as ProductCode', 'dpvHstGndItem.hour', DB::raw('SUM( IIF(dpvHstGndItem.Type = 1, dpvHstGndItem.Quantity * -1, dpvHstGndItem.Quantity) ) as quantity'))
                                ->groupBy('Item.BohName', 'dpvHstGndItem.hour')
                                ->unionAll($dpvHstGndItems)
                                ->unionAll($dpvHstGndItemAles)
                                ->get();

        foreach ($dpvHstGndItemCmbAles as $dpvHstGndItemCmbAle) {

            if( $dpvHstGndItemCmbAle->ProductCode == ''){
                continue;
            }

            $total = $dpvHstGndItemCmbAle->quantity;
            if( isset( $items[$dpvHstGndItemCmbAle->ProductCode]['h' . $dpvHstGndItemCmbAle->hour] ) ){
                $total += $items[$dpvHstGndItemCmbAle->ProductCode]['h' . $dpvHstGndItemCmbAle->hour];
            }

            $items[$dpvHstGndItemCmbAle->ProductCode]['ProductName'] = Material::getDescByCode($dpvHstGndItemCmbAle->ProductCode);
            $items[$dpvHstGndItemCmbAle->ProductCode]['h' . $dpvHstGndItemCmbAle->hour] = $total;
        }

        return $items;

    }

    public function getDataVoid($storeID, $dateFrom, $dateUntil)
    {
        $customerCode = Plant::getCustomerCodeById($storeID);

        $items = [];

        $dpvHstGndTenders = DB::connection($this->connectionName)
                            ->table('dpvHstGndTender')
                            ->leftJoin('EmployeeByStore', 'EmployeeByStore.EmployeeNumber', 'dpvHstGndTender.FKEmployeeNumber')
                            ->leftJoin('gblStore', 'gblStore.storeID', 'dpvHstGndTender.FKstoreID')
                            ->where('gblStore.SecondaryStoreID', $customerCode)
                            ->where('dpvHstGndTender.Amount', '<', 0)
                            ->whereBetween('dpvHstGndTender.DateOfBusiness', [$dateFrom . ' 00:00:00', $dateUntil . ' 23:59:59'])
                            ->select(DB::raw('CAST(dpvHstGndTender.DateOfBusiness AS date) as date'), 'dpvHstGndTender.CheckNumber', 'dpvHstGndTender.Amount',
                                    'dpvHstGndTender.Hour', 'dpvHstGndTender.Minute', 'EmployeeByStore.FirstName', 'EmployeeByStore.LastName' )
                            ->get();

        foreach ($dpvHstGndTenders as $dpvHstGndTender) {
            $items[] = (object)[
                'SaleDate' => $dpvHstGndTender->date,
                'ReceiptNumber' => $dpvHstGndTender->CheckNumber,
                'PayAmount' => abs($dpvHstGndTender->Amount),
                'time' => $dpvHstGndTender->Hour . ':' . $dpvHstGndTender->Minute,
                'VoidStaff' => $dpvHstGndTender->FirstName .' ' . $dpvHstGndTender->LastName,
                'VoidReason' => '-',
            ];
        }

        return $items;
    }

    public function getDataSalesPerHour($storeID, $dateFrom, $dateUntil)
    {
        $customerCode = Plant::getCustomerCodeById($storeID);

        $items = [];

        $dpvHstGndItems = DB::connection($this->connectionName)
                            ->table('dpvHstGndItem as dgi')
                            ->leftJoin('gblStore', 'gblStore.storeID', 'dgi.FKstoreID')
                            ->where('gblStore.SecondaryStoreID', $customerCode)
                            ->whereBetween('dgi.DateOfBusiness', [$dateFrom . ' 00:00:00', $dateUntil . ' 23:59:59'])
                            ->select(
                                'dgi.Hour',
                                'dgi.CheckNumber',
                                'dgi.DateOfBusiness',
                                DB::raw('SUM(dgi.price) as totalPrice'),
                                DB::raw('SUM(dgi.price * 1.1) as GrossSales'),
                                DB::raw('SUM((dgi.price - dgi.DiscPric) * 1.1) as disc'),
                                DB::raw('SUM(dgi.price * 0.1) as tax'),
                                DB::raw('(
                                    SELECT SUM(Amount)
                                    FROM dpvHstGndTender as dgt
                                    WHERE dgt.DateOfBusiness = dgi.DateOfBusiness AND
                                        dgt.CheckNumber = dgi.CheckNumber AND
                                        dgt.FKstoreID = dgi.FKstoreID AND
                                        dgt.TypeId IN (
                                            SELECT TenderId from Tender
                                        )
                                    ) as PayAmount
                                ')
                            )
                            ->groupBy(
                                'dgi.Hour',
                                'dgi.CheckNumber',
                                'dgi.DateOfBusiness',
                                'dgi.FKstoreID'
                            )
                            ->get();

        foreach ($dpvHstGndItems as $dpvHstGndItem) {

            if( isset( $items['h' . $dpvHstGndItem->Hour] ) ){

                $items['h' . $dpvHstGndItem->Hour]['bill'] += 1;
                $items['h' . $dpvHstGndItem->Hour]['totalPrice'] += $dpvHstGndItem->totalPrice;
                $items['h' . $dpvHstGndItem->Hour]['disc'] += $dpvHstGndItem->disc;
                $items['h' . $dpvHstGndItem->Hour]['subTotal'] += $dpvHstGndItem->totalPrice - $dpvHstGndItem->disc;
                $items['h' . $dpvHstGndItem->Hour]['tax'] += (int)$dpvHstGndItem->tax;
                $items['h' . $dpvHstGndItem->Hour]['netSales'] += $dpvHstGndItem->GrossSales - $dpvHstGndItem->disc;
                $items['h' . $dpvHstGndItem->Hour]['totalPayment'] += $dpvHstGndItem->PayAmount;

            } else {

                $items['h' . $dpvHstGndItem->Hour]['hours'] = $dpvHstGndItem->Hour;
                $items['h' . $dpvHstGndItem->Hour]['bill'] = 1;
                $items['h' . $dpvHstGndItem->Hour]['totalPrice'] = $dpvHstGndItem->totalPrice;
                $items['h' . $dpvHstGndItem->Hour]['disc'] = $dpvHstGndItem->disc;
                $items['h' . $dpvHstGndItem->Hour]['subTotal'] = $dpvHstGndItem->totalPrice - $dpvHstGndItem->disc;
                $items['h' . $dpvHstGndItem->Hour]['tax'] = (int)$dpvHstGndItem->tax;
                $items['h' . $dpvHstGndItem->Hour]['netSales'] = $dpvHstGndItem->GrossSales - $dpvHstGndItem->disc;
                $items['h' . $dpvHstGndItem->Hour]['totalPayment'] = $dpvHstGndItem->PayAmount;

            }
        }

        return $items;
    }

    public function getStoreAloha($dateFrom, $dateUntil, $plantCode = null)
    {
        $query = DB::connection($this->connectionName)
                    ->table('dpvHstGndItem')
                    ->leftJoin('gblStore', 'gblStore.storeID', 'dpvHstGndItem.FKStoreId')
                    ->whereBetween('dpvHstGndItem.DateOfBusiness', [$dateFrom, $dateUntil])
                    ->where('gblStore.FKRegionId', 2)
                    ->select('dpvHstGndItem.DateOfBusiness', 'gblStore.SecondaryStoreID' )
                    ->groupBy('dpvHstGndItem.DateOfBusiness', 'gblStore.SecondaryStoreID');

        if( $plantCode && $plantCode != ''){
            $query = $query->where('gblStore.SecondaryStoreID', $plantCode);
        }

        return $query->get();
    }

    public function getPaymentData($customerCode, $date, $pos){
        $paymentCondition = PaymentPos::getQueryCondtionAloha($pos->company_id, 'b.UserNumber');
        $paymentConditionOrder = PaymentPos::getQueryCondtionAloha($pos->company_id, 'b.UserNumber', 'order');

        $payments = DB::connection($this->connectionName)
                        ->select( DB::raw("
                            SELECT pl.DateOfBusiness, pl.DocumentType, pl.Periode, pl.StoreName, pl.TotalAmount, pl.AccountStore FROM
                            (
                                SELECT
                                    a.DateOfBusiness,
                                    " . $paymentCondition . " AS DocumentType,
                                    MONTH( a.DateOfBusiness ) as Periode,
                                    c.Name AS StoreName,
                                    SUM( a.Amount ) AS TotalAmount,
                                    c.SecondaryStoreID AS AccountStore,
                                    " . $paymentConditionOrder . " AS SortDocType
                                FROM RF_Datamart.dbo.dpvHstTender a
                                LEFT JOIN RF_Datamart.dbo.Tender b ON a.FKTenderId = b.TenderId
                                LEFT JOIN RF_Datamart.dbo.gblStore c ON a.FKstoreID = c.storeID
                                WHERE c.SecondaryStoreID = :storeCode1 AND a.DateOfBusiness = :date1
                                GROUP BY a.DateOfBusiness,
                                            " . $paymentCondition . " ,
                                            c.SecondaryStoreID,
                                            c.Name,
                                            " . $paymentConditionOrder . "
                                UNION ALL
                                SELECT
                                    a.DateOfBusiness,
                                    CASE
                                    WHEN
                                            b.UserNumber <> 401
                                    THEN
                                            'DW'
                                    ELSE
                                            'DG'
                                    END AS DocumentType,
                                    MONTH(a.DateOfBusiness) as Periode,
                                    c.Name AS StoreName,
                                    sum(a.Amount) AS TotalAmount,
                                    c.SecondaryStoreID AS AccountStore,
                                    CASE
                                    WHEN
                                            b.UserNumber <> 401
                                    THEN
                                            5
                                    ELSE
                                            4
                                    END AS SortDocType
                                FROM RF_Datamart.dbo.dpvHstComp a
                                LEFT JOIN RF_Datamart.dbo.Comp b ON a.FKCompId = b.CompId
                                LEFT JOIN RF_Datamart.dbo.gblStore c ON a.FKstoreID = c.storeID
                                WHERE c.SecondaryStoreID = :storeCode2 AND a.DateOfBusiness = :date2
                                GROUP BY a.DateOfBusiness,
                                            CASE
                                            WHEN
                                                    b.UserNumber <> 401
                                            THEN
                                                    'DW'
                                            ELSE
                                                    'DG'
                                            END,
                                            c.SecondaryStoreID,
                                            c.Name,
                                            CASE
                                            WHEN
                                                    b.UserNumber <> 401
                                            THEN
                                                    5
                                            ELSE
                                                    4
                                            END

                                UNION ALL

                                SELECT
                                    a.DateOfBusiness,
                                    'DW' AS DocumentType,
                                    MONTH(a.DateOfBusiness) as Periode,
                                    c.Name AS StoreName,
                                    SUM(a.Amount) AS TotalAmount,
                                    c.SecondaryStoreID AS AccountStore,
                                    4 AS SortDocType
                                FROM RF_Datamart.dbo.dpvHstPromotion a
                                LEFT JOIN RF_Datamart.dbo.gblStore c ON a.FKstoreID = c.storeID
                                WHERE c.SecondaryStoreID = :storeCode3 and a.DateOfBusiness = :date3
                                GROUP BY a.DateOfBusiness,
                                            c.Name,
                                            c.SecondaryStoreID
                            ) pl
                            WHERE PL.TotalAmount > 0
                            ORDER BY pl.SortDocType
                        "), [
                            'storeCode1' => $customerCode,
                            'date1' => $date,
                            'storeCode2' => $customerCode,
                            'date2' => $date,
                            'storeCode3' => $customerCode,
                            'date3' => $date,
                        ]);

        return $payments;
    }

    public function getSalesInventoryData($customerCode, $date, $pos){
        $taxMultiplication = Company::getConfigByKey($pos->company_id, 'TAX_MULTIPLICATION');
        $orderMode = OrderModePos::getQueryCondtionAloha($pos->company_id, 'c.Name');
        $orderModeNormcmb = OrderModePos::getQueryCondtionAloha($pos->company_id, 'normcmb.SalesMode', 'id');
        $orderModeRounding = OrderModePos::getQueryCondtionAloha($pos->company_id, 'rounding.SalesMode', 'id');

        $transactions = DB::connection($this->connectionName)
                        ->select( DB::raw("
                            SELECT
                                un.SecondaryStoreID,
                                un.DateOfBusiness,
                                un.ShortName,
                                un.BohName,
                                SUM(un.Quantity) AS Quantity,
                                SUM(un.GrossSales) AS GrossSales,
                                SUM(un.Discount) AS Discount,
                                SUM(un.NettoSales) AS NettoSales,
                                SUM(un.Tax) AS Tax,
                                un.ItemType,
                                un.SalesMode
                            FROM
                            (
                                SELECT
                                    aln.SecondaryStoreID,
                                    aln.DateOfBusiness,
                                    aln.ShortName,
                                    aln.BohName,
                                    aln.Quantity,
                                    aln.GrossSales,
                                    aln.Discount,
                                    aln.GrossSales / " . $taxMultiplication . " AS NettoSales,
                                    aln.GrossSales - (aln.GrossSales / " . $taxMultiplication . ") AS Tax,
                                    aln.ItemType,
                                    aln.SalesMode
                                FROM
                                (
                                    SELECT
                                        d.SecondaryStoreID,
                                        a.DateOfBusiness,
                                        b.ShortName,
                                        b.BohName,
                                        sum(
                                        CASE
                                        WHEN
                                            a.Type = 1
                                        THEN
                                            a.Quantity * -1
                                        ELSE
                                            a.Quantity
                                        END
                                        ) AS Quantity,
                                        SUM( a.Price ) AS GrossSales,
                                        0 AS Discount,
                                        0 AS NettoSales,
                                        0 AS Tax,
                                        CASE
                                            WHEN
                                                a.ParentId <> 0 AND a.QSQuickComboID = 0
                                            THEN
                                                'ERLA'
                                            ELSE
                                                'NORM'
                                        END AS ItemType,
                                        " . $orderMode . " AS SalesMode
                                    FROM RF_Datamart.dbo.dpvHstGndItem a
                                    LEFT JOIN RF_Datamart.dbo.Item b ON a.FKItemId = b.ItemId
                                    LEFT JOIN RF_Datamart.dbo.OrderMode c ON a.FKOrderModeId = c.OrderModeId
                                    LEFT JOIN RF_Datamart.dbo.gblStore d ON a.FKstoreID = d.storeID
                                    WHERE (a.QSQuickComboID = 0 OR (a.QSQuickComboID <> 0 AND a.ParentId <> 0 AND a.FKCategoryId = 102 AND a.price > 0)) AND d.SecondaryStoreID = :storeCode1 AND a.DateOfBusiness = :date1
                                    GROUP BY d.SecondaryStoreID,
                                        a.DateOfBusiness,
                                        b.ShortName,
                                        b.BohName,
                                        CASE
                                            WHEN
                                            a.ParentId <> 0 AND a.QSQuickComboID = 0
                                            THEN
                                                'ERLA'
                                            ELSE
                                                'NORM'
                                        END,
                                        " . $orderMode . "
                                ) aln

                                UNION ALL

                                SELECT
                                    normcmb.SecondaryStoreID,
                                    normcmb.DateOfBusiness,
                                    normcmb.ShortName,
                                    normcmb.BohName,
                                    SUM(normcmb.Quantity) AS Quantity,
                                    SUM(normcmb.GrossSales) AS GrossSales,
                                    0 AS Discount,
                                    SUM(normcmb.GrossSales / " . $taxMultiplication . ") AS NettoSales,
                                    SUM(normcmb.GrossSales - (normcmb.GrossSales / " . $taxMultiplication . ") ) AS Tax,
                                    normcmb.ItemType,
                                    " . $orderModeNormcmb . " AS SalesMode
                                FROM (
                                    SELECT
                                        d.SecondaryStoreID,
                                        a.DateOfBusiness,
                                        f.name AS ShortName,
                                        CAST(
                                            CASE
                                            WHEN
                                                f.EXPORTID IS NULL
                                            THEN
                                                '-'
                                            ELSE
                                                f.EXPORTID
                                            END AS VARCHAR(26)
                                        ) AS BohName,
                                        a.lcount AS Quantity,
                                        a.amount AS GrossSales,
                                        0 AS Discount,
                                        0 AS NettoSales,
                                        0 AS Tax,
                                        'NORM' AS ItemType,
                                        o.FKOrderModeId AS SalesMode
                                    FROM RF_Datamart.dbo.dpvHstGndSale a
                                    LEFT JOIN RF_Datamart.dbo.gblStore d ON a.FKstoreID = d.storeID
                                    LEFT JOIN RF_Datamart.dbo.Promotion f ON a.TypeID = f.PromotionID
                                    JOIN (
                                        SELECT DISTINCT sg.CheckNumber, sg.FKOrderModeId
                                        FROM RF_Datamart.dbo.dpvHstGndItem sg
                                        LEFT JOIN RF_Datamart.dbo.gblStore sd ON sg .FKstoreID = sd.storeID
                                        WHERE sd.SecondaryStoreID = :storeCode2 AND sg.DateOfBusiness = :date2
                                    ) o ON a.CheckNumber = o.CheckNumber
                                    WHERE d.SecondaryStoreID = :storeCode3 AND a.DateOfBusiness = :date3 AND a.type = 87
                                ) normcmb
                                GROUP BY normcmb.SecondaryStoreID,
                                            normcmb.DateOfBusiness,
                                            normcmb.ShortName,
                                            normcmb.BohName,
                                            normcmb.ItemType,
                                            normcmb.SalesMode,
                                            " . $orderModeNormcmb . "

                                UNION ALL

                                SELECT
                                    rounding.SecondaryStoreID,
                                    rounding.DateOfBusiness,
                                    rounding.ShortName,
                                    rounding.BohName,
                                    SUM(rounding.Quantity) AS Quantity,
                                    SUM(rounding.GrossSales) AS GrossSales,
                                    0 AS Discount,
                                    SUM(rounding.GrossSales / " . $taxMultiplication . ") AS NettoSales,
                                    SUM(rounding.GrossSales - (rounding.GrossSales / " . $taxMultiplication . ") ) AS Tax,
                                    rounding.ItemType,
                                    " . $orderModeRounding . " AS SalesMode
                                FROM (
                                    SELECT
                                        d.SecondaryStoreID,
                                        a.DateOfBusiness,
                                        'Rounding' AS ShortName,
                                        '9799999' AS BohName,
                                        a.lcount AS Quantity,
                                        a.amount AS GrossSales,
                                        0 AS Discount,
                                        0 AS NettoSales,
                                        0 AS Tax,
                                        'NORM' AS ItemType,
                                        o.FKOrderModeId AS SalesMode
                                    FROM RF_Datamart.dbo.dpvHstGndSale a
                                    LEFT JOIN RF_Datamart.dbo.gblStore d ON a.FKstoreID = d.storeID
                                    JOIN (
                                        SELECT DISTINCT sg.CheckNumber, sg.FKOrderModeId
                                        FROM RF_Datamart.dbo.dpvHstGndItem sg
                                        LEFT JOIN RF_Datamart.dbo.gblStore sd ON sg.FKstoreID = sd.storeID
                                        WHERE sd.SecondaryStoreID = :storeCode4 AND sg.DateOfBusiness = :date4
                                    ) o ON a.CheckNumber = o.CheckNumber
                                    WHERE d.SecondaryStoreID = :storeCode5 AND a.DateOfBusiness = :date5 AND a.type = 45
                                ) rounding
                                GROUP BY rounding.SecondaryStoreID,
                                            rounding.DateOfBusiness,
                                            rounding.ShortName,
                                            rounding.BohName,
                                            rounding.ItemType,
                                            rounding.SalesMode,
                                            " . $orderModeRounding . "

                                UNION ALL

                                SELECT
                                    ale.SecondaryStoreID,
                                    ale.DateOfBusiness,
                                    ale.ShortName,
                                    ale.BohName,
                                    ale.Quantity,
                                    ale.GrossSales,
                                    ale.Discount,
                                    ale.NettoSales,
                                    ale.Tax,
                                    ale.ItemType,
                                    ale.SalesMode
                                FROM
                                (
                                SELECT
                                    d.SecondaryStoreID,
                                    a.DateOfBusiness,
                                    b.ShortName,
                                    b.BohName,
                                    sum(
                                        CASE
                                        WHEN
                                            a.Type = 1
                                        THEN
                                            a.Quantity * -1
                                        ELSE
                                            a.Quantity
                                        END
                                    ) AS Quantity,
                                    0 AS GrossSales,
                                    0 AS Discount,
                                    0 AS NettoSales,
                                    0 AS Tax,
                                    CASE
                                    WHEN
                                        a.ParentId = 0
                                    THEN
                                        'ERLA'
                                    ELSE
                                        'NORM'
                                    END AS ItemType,
                                    " . $orderMode . " AS SalesMode
                                FROM RF_Datamart.dbo.dpvHstGndItem a
                                LEFT JOIN RF_Datamart.dbo.Item b ON a.FKItemId = b.ItemId
                                LEFT JOIN RF_Datamart.dbo.OrderMode c ON a.FKOrderModeId = c.OrderModeId
                                LEFT JOIN RF_Datamart.dbo.gblStore d ON a.FKstoreID = d.storeID
                                WHERE a.QSQuickComboID = 0 AND a.ParentId = 0 AND b.ExportId <> 1 AND d.SecondaryStoreID = :storeCode6 AND a.DateOfBusiness = :date6
                                GROUP BY d.SecondaryStoreID,
                                            a.DateOfBusiness,
                                            b.ShortName,
                                            b.BohName,
                                            CASE
                                            WHEN
                                                a.ParentId = 0
                                            THEN
                                                'ERLA'
                                            ELSE
                                                'NORM'
                                            END,
                                            " . $orderMode . "
                                ) ale

                                UNION ALL

                                SELECT
                                    cmberla.SecondaryStoreID,
                                    cmberla.DateOfBusiness,
                                    cmberla.ShortName,
                                    cmberla.BohName,
                                    cmberla.Quantity,
                                    cmberla.GrossSales,
                                    cmberla.Discount,
                                    cmberla.NettoSales,
                                    cmberla.Tax,
                                    cmberla.ItemType,
                                    cmberla.SalesMode
                                FROM
                                (
                                SELECT
                                    d.SecondaryStoreID,
                                    a.DateOfBusiness,
                                    b.ShortName,
                                    b.BohName AS BohName,
                                    sum(
                                        CASE
                                        WHEN
                                            a.Type = 1
                                        THEN
                                            a.Quantity * -1
                                        ELSE
                                            a.Quantity
                                        END
                                    ) AS Quantity,
                                    0 AS GrossSales,
                                    0 AS Discount,
                                    0 AS NettoSales,
                                    0 AS Tax,
                                    'ERLA' AS ItemType,
                                    " . $orderMode . " AS SalesMode
                                FROM RF_Datamart.dbo.dpvHstGndItem a
                                LEFT JOIN RF_Datamart.dbo.Item b ON a.FKItemId = b.ItemId
                                LEFT JOIN RF_Datamart.dbo.OrderMode c ON a.FKOrderModeId = c.OrderModeId
                                LEFT JOIN RF_Datamart.dbo.gblStore d ON a.FKstoreID = d.storeID
                                LEFT JOIN RF_Datamart.dbo.QuickComboPromotion e ON a.QSQuickComboID = e.FKPromotionID AND a.FKstoreID = e.FKstoreID
                                LEFT JOIN RF_Datamart.dbo.Promotion f ON a.QSQuickComboID = f.PromotionID
                                WHERE a.QSQuickComboID <> 0 AND b.BohName <> '9999999' AND d.SecondaryStoreID = :storeCode7 AND b.ExportId <> 1 AND a.DateOfBusiness = :date7
                                GROUP BY d.SecondaryStoreID,
                                            a.DateOfBusiness,
                                            b.ShortName,
                                            b.BohName,
                                            " . $orderMode . "
                                ) cmberla

                                UNION ALL

                                SELECT
                                    tn.SecondaryStoreID,
                                    tn.DateOfBusiness,
                                    tn.ShortName,
                                    tn.BohName,
                                    tn.Quantity,
                                    tn.GrossSales,
                                    tn.Discount,
                                    tn.NettoSales,
                                    tn.Tax,
                                    tn.ItemType,
                                    tn.SalesMode
                                FROM
                                (
                                    SELECT
                                        b.SecondaryStoreID,
                                        a.DateOfBusiness,
                                        'Transaction Number' AS ShortName,
                                        '9999995' AS BohName,
                                        a.lCount AS Quantity,
                                        0 AS GrossSales,
                                        0 AS Discount,
                                        0 AS NettoSales,
                                        0 AS Tax,
                                        'NORM' AS ItemType,
                                        '' AS SalesMode
                                    from RF_Datamart.dbo.dpvHstSalesSummary a
                                    LEFT JOIN RF_Datamart.dbo.gblStore b ON a.FKstoreID = b.storeID
                                    WHERE a.Type = 11 AND  b.SecondaryStoreID = :storeCode8 AND a.DateOfBusiness = :date8
                                ) tn

                                UNION ALL

                                SELECT
                                    rf.SecondaryStoreID,
                                    rf.DateOfBusiness,
                                    rf.ShortName,
                                    rf.BohName,
                                    rf.Quantity,
                                    rf.GrossSales,
                                    rf.Discount,
                                    rf.NettoSales,
                                    rf.Tax,
                                    rf.ItemType,
                                    rf.SalesMode
                                FROM
                                (
                                    SELECT
                                        b.SecondaryStoreID,
                                        a.DateOfBusiness,
                                        'Refund' AS ShortName,
                                        '9999997' AS BohName,
                                        a.lCount * -1 AS Quantity,
                                        a.Amount AS GrossSales,
                                        0 AS Discount,
                                        0 AS NettoSales,
                                        0 AS Tax,
                                        'NORM' AS ItemType,
                                        '' AS SalesMode
                                    from RF_Datamart.dbo.dpvHstSalesSummary a
                                    LEFT JOIN RF_Datamart.dbo.gblStore b ON a.FKstoreID = b.storeID
                                    WHERE a.Type = 35 AND  b.SecondaryStoreID = :storeCode9 AND a.DateOfBusiness = :date9
                                ) rf

                                UNION ALL

                                SELECT
                                    vi.SecondaryStoreID,
                                    vi.DateOfBusiness,
                                    vi.ShortName,
                                    vi.BohName,
                                    vi.Quantity,
                                    vi.GrossSales,
                                    vi.Discount,
                                    vi.NettoSales,
                                    vi.Tax,
                                    vi.ItemType,
                                    vi.SalesMode
                                FROM
                                (
                                    SELECT
                                        b.SecondaryStoreID,
                                        a.DateOfBusiness,
                                        'Void' AS ShortName,
                                        '9999998' AS BohName,
                                        sum(a.lCount) * -1 AS Quantity,
                                        sum(a.Amount) * -1 AS GrossSales,
                                        0 AS Discount,
                                        0 AS NettoSales,
                                        0 AS Tax,
                                        'NORM' AS ItemType,
                                        '' AS SalesMode
                                    from RF_Datamart.dbo.dpvHstSalesSummary a
                                    LEFT JOIN RF_Datamart.dbo.gblStore b ON a.FKstoreID = b.storeID
                                    WHERE a.Type = 71 AND  b.SecondaryStoreID = :storeCode10 AND a.DateOfBusiness = :date10
                                    GROUP BY b.SecondaryStoreID,
                                                a.DateOfBusiness
                                ) vi

                                UNION ALL

                                SELECT
                                    tc.SecondaryStoreID,
                                    tc.DateOfBusiness,
                                    tc.ShortName,
                                    tc.BohName,
                                    tc.Quantity,
                                    tc.GrossSales,
                                    tc.Discount,
                                    tc.NettoSales,
                                    tc.Tax,
                                    tc.ItemType,
                                    tc.SalesMode
                                FROM
                                (
                                    SELECT
                                        b.SecondaryStoreID,
                                        a.DateOfBusiness,
                                        'Take Away Charge' AS ShortName,
                                        '9999993' AS BohName,
                                        sum(a.lCount) AS Quantity,
                                        sum(a.Amount*1.1) AS GrossSales,
                                        0 AS Discount,
                                        sum(a.Amount) AS NettoSales,
                                        sum(a.Amount*0.1) AS Tax,
                                        'NORM' AS ItemType,
                                        '' AS SalesMode
                                    from RF_Datamart.dbo.dpvHstSalesSummary a
                                    LEFT JOIN RF_Datamart.dbo.gblStore b ON a.FKstoreID = b.storeID
                                    WHERE a.Type = 18 AND a.TypeId <> 3 AND  b.SecondaryStoreID = :storeCode11 AND a.DateOfBusiness = :date11
                                    GROUP BY b.SecondaryStoreID,
                                                a.DateOfBusiness
                                ) tc

                                UNION ALL

                                SELECT
                                    dlv.SecondaryStoreID,
                                    dlv.DateOfBusiness,
                                    dlv.ShortName,
                                    dlv.BohName,
                                    dlv.Quantity,
                                    dlv.GrossSales,
                                    dlv.Discount,
                                    dlv.NettoSales,
                                    dlv.Tax,
                                    dlv.ItemType,
                                    dlv.SalesMode
                                FROM
                                (
                                    SELECT
                                        b.SecondaryStoreID,
                                        a.DateOfBusiness,
                                        'Delivery Charge' AS ShortName,
                                        '9999991' AS BohName,
                                        sum(a.lCount) AS Quantity,
                                        sum(a.Amount*1.1) AS GrossSales,
                                        0 AS Discount,
                                        sum(a.Amount) AS NettoSales,
                                        sum(a.Amount*0.1) AS Tax,
                                        'NORM' AS ItemType,
                                        '' AS SalesMode
                                    from RF_Datamart.dbo.dpvHstSalesSummary a
                                    LEFT JOIN RF_Datamart.dbo.gblStore b ON a.FKstoreID = b.storeID
                                    WHERE a.Type = 18 AND a.TypeId = 3 AND  b.SecondaryStoreID = :storeCode12 AND a.DateOfBusiness = :date12
                                    GROUP BY b.SecondaryStoreID,
                                                a.DateOfBusiness
                                ) dlv

                            ) un
                            WHERE un.BohName NOT IN ('8888888', '9999999')
                            GROUP BY un.SecondaryStoreID,
                                    un.DateOfBusiness,
                                    un.ShortName,
                                    un.BohName,
                                    un.ItemType,
                                    un.SalesMode
                        "), [
                            'storeCode1' => $customerCode, 'date1' => $date,
                            'storeCode2' => $customerCode, 'date2' => $date,
                            'storeCode3' => $customerCode, 'date3' => $date,
                            'storeCode4' => $customerCode, 'date4' => $date,
                            'storeCode5' => $customerCode, 'date5' => $date,
                            'storeCode6' => $customerCode, 'date6' => $date,
                            'storeCode7' => $customerCode, 'date7' => $date,
                            'storeCode8' => $customerCode, 'date8' => $date,
                            'storeCode9' => $customerCode, 'date9' => $date,
                            'storeCode10' => $customerCode, 'date10' => $date,
                            'storeCode11' => $customerCode, 'date11' => $date,
                            'storeCode12' => $customerCode, 'date12' => $date
                        ]);

        return $transactions;
    }

    public function getPaymentSales($customerCode, $date)
    {
        $pos = DB::table('pos')
                ->where('id', $this->posId)
                ->select('company_id')
                ->first();

        $payments = $this->getPaymentData($customerCode, $date, $pos);

        $data = [];
        $compCode = Company::getConfigByKey($pos->company_id, 'SAP_CODE');

        foreach ($payments as $payment) {
            $data[] = [
                'COL01' => date("d:m:Y", strtotime($payment->DateOfBusiness)),
                'COL02' => $payment->DocumentType,
                'COL03' => str_pad($payment->Periode, 2, '0', STR_PAD_LEFT),
                'COL04' => $compCode,
                'COL05' => $payment->StoreName,
                'COL06' => round($payment->TotalAmount, 2),
                'COL07' => $payment->AccountStore,
                'COL08' => '',
                'COL09' => '',
                'COL10' => '',
                'COL11' => '',
                'COL12' => '',
                'COL13' => '',
                'COL14' => '',
                'COL15' => '',
                'COL16' => ''
            ];
        }

        return $data;
    }

    public function getSalesInventory($customerCode, $date)
    {
        $pos = DB::table('pos')
                ->where('id', $this->posId)
                ->select('company_id')
                ->first();

        $transactions = $this->getSalesInventoryData($customerCode, $date, $pos);
        $sales = [];
        $inventories = [];

        foreach ($transactions as $transaction) {
            if ( !in_array($transaction->BohName, ['8888888','9999999']) && strlen($transaction->BohName) > 0) {
                $sales[] = [
                    'COL01' => $transaction->SecondaryStoreID,
                    'COL02' => '601',
                    'COL03' => date("d:m:Y", strtotime($transaction->DateOfBusiness)),
                    'COL04' => '601',
                    'COL05' => $transaction->ItemType,
                    'COL06' => $transaction->BohName,
                    'COL07' => (int)$transaction->Quantity,
                    'COL08' => round($transaction->GrossSales, 2),
                    'COL09' => round($transaction->Discount, 2),
                    'COL10' => round($transaction->NettoSales, 2),
                    'COL11' => round($transaction->Tax, 2),
                    'COL12' => '',
                    'COL13' => '',
                    'COL14' => '',
                    'COL15' => '',
                    'COL16' => $transaction->SalesMode
                ];
            }

            if ( !in_array($transaction->BohName, ['8888888','9999999']) && strlen($transaction->BohName) > 0 && $transaction->ItemType != 'NORM') {
                $inventories[] = [
                    'COL01' => date("d:m:Y", strtotime($transaction->DateOfBusiness)),
                    'COL02' => '601',
                    'COL03' => $transaction->BohName,
                    'COL04' => $transaction->SecondaryStoreID,
                    'COL05' => (int)$transaction->Quantity,
                    'COL06' => '',
                    'COL07' => '',
                    'COL08' => '',
                    'COL09' => '',
                    'COL10' => '',
                    'COL11' => '',
                    'COL12' => '',
                    'COL13' => '',
                    'COL14' => '',
                    'COL15' => '',
                    'COL16' => ''
                ];
            }
        }

        return [
            'sales' => $sales,
            'inventories' => $inventories
        ];
    }

    public function getSalesFormatSAP($customerCode, $date)
    {
        $salesInventory = $this->getSalesInventory($customerCode, $date);

        return [
            'payment' => $this->getPaymentSales($customerCode, $date),
            'sales' => $salesInventory['sales'],
            'inventory' => $salesInventory['inventories']
        ];
    }

    public function getPaymentSalesSapMiddleware($customerCode, $date)
    {
        $pos = DB::table('pos')
                ->where('id', $this->posId)
                ->select('company_id')
                ->first();

        $data = [];
        $payments = $this->getPaymentData($customerCode, $date, $pos);
        foreach ($payments as $payment) {
            $data[] = [
                'reference' => $payment->StoreName,
                'amount' => round($payment->TotalAmount, 2),
                'payment_method' => $payment->DocumentType
            ];
        }

        return $data;
    }

    public function getSalesInventorySapMiddleware($customerCode, $date)
    {
        $pos = DB::table('pos')
                ->where('id', $this->posId)
                ->select('company_id')
                ->first();

        $transactions = $this->getSalesInventoryData($customerCode, $date, $pos);

        $sales = [];
        $inventories = [];
        foreach ($transactions as $transaction) {
            if ( !in_array($transaction->BohName, ['8888888','9999999']) && strlen($transaction->BohName) > 0) {
                $sales[] = [
                    'material_id' => $transaction->BohName,
                    'is_foc' => false,
                    'is_combo' => ($transaction->ItemType != 'NORM') ? false : true,
                    'sales_mode' => $transaction->SalesMode,
                    'qty' => (int)$transaction->Quantity,
                    'gross' => round($transaction->GrossSales, 2),
                    'discount' => round($transaction->Discount, 2),
                    'net' => round($transaction->NettoSales, 2),
                    'tax' => round($transaction->Tax, 2),
                    'refund' => 0,
                    'void' => 0
                ];
            }

            if ( !in_array($transaction->BohName, ['8888888','9999999']) && strlen($transaction->BohName) > 0 && $transaction->ItemType != 'NORM') {
                $inventories[] = [
                    'material_id' => $transaction->BohName,
                    'is_foc' => false,
                    'qty' => (int)$transaction->Quantity
                ];
            }
        }

        return [
            'sales' => $sales,
            'inventories' => $inventories
        ];
    }

    public function getSalesFormatSAPMiddleware($customerCode, $date)
    {
        $salesInventory = $this->getSalesInventorySapMiddleware($customerCode, $date);

        return [
            'payments' => $this->getPaymentSalesSapMiddleware($customerCode, $date),
            'sales' => $salesInventory['sales'],
            'inventories' => $salesInventory['inventories']
        ];
    }
}
