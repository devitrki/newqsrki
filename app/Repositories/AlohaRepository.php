<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

use App\Models\Pos;

class AlohaRepository {
    public $posId;
    public $connectionDB = false;
    public $connectionName = '';

    public function __construct($posId)
    {
        $this->posId = $posId;
    }

    public function initConnectionDB()
    {
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

        return [
            'status' => $status,
            'message' => $message
        ];
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
}
