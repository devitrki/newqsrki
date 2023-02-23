<?php

namespace App\Entities;

class SapMiddleware
{
    const VERSION = 'v1';
    const MASTER_PLANT_PATH_URL         = '/' . self::VERSION . '/plant';
    const MASTER_MATERIAL_PATH_URL      = '/' . self::VERSION . '/material';
    const MASTER_ASSET_PATH_URL         = '/' . self::VERSION . '/asset';
    const UPLOAD_GI_PO_STO_PATH_URL     = '/' . self::VERSION . '/gi/posto';
    const UPLOAD_GR_PO_STO_PATH_URL     = '/' . self::VERSION . '/gr/po-sto';
    const UPLOAD_GR_PO_VENDOR_PATH_URL  = '/' . self::VERSION . '/gr/vendor';
    const UPLOAD_STOCK_OPNAME_PATH_URL  = '/' . self::VERSION . '/stock/opname';
    const UPLOAD_WASTE_PATH_URL         = '/' . self::VERSION . '/gi/scrap';
    const UPLOAD_SALES_PATH_URL         = '/' . self::VERSION . '/daily/console';
    const MUTATION_ASSET_PATH_URL       = '/' . self::VERSION . '/asset/mutation';
    const UPLOAD_PETTYCASH_PATH_URL     = '/' . self::VERSION . '/stag/petty-cash';
    const LIST_OUTSTANDING_GR_PATH_URL  = '/' . self::VERSION . '/gr/outstanding';
    const LIST_OUTSTANDING_PO_PATH_URL  = '/' . self::VERSION . '/po/outstanding';
    const LIST_CURRENT_STOCK_PATH_URL   = '/' . self::VERSION . '/stock/current';
    const LIST_TRANSACTION_LOG_PATH_URL   = '/' . self::VERSION . '/log/console';

    public static function generateSignature($apiKey, $secretKey, $timestamp, $path, $payload, $method = null){
        if ($method === null) {
            $method = 'POST';
        }

        $hashType = 'sha256';
        $pathUrl = $method . $path;

        $message =  $apiKey     .
                    $timestamp  .
                    $pathUrl    .
                    $payload;

        $hash = hash_hmac($hashType, $message, $secretKey, true);
        $signature = base64_encode($hash);

        return $signature;
    }

    public static function getHeaderHttp($apiKey, $timestamp, $signature){
        return [
            'X-Timestamp' => $timestamp,
            'X-Signature' => $signature,
            'X-Api-Key' => $apiKey,
        ];
    }

    public static function getLastErrorMessage($messageErrors){
        $message = 'Feedback SAP : ';

        if ($messageErrors && is_array($messageErrors)) {
            for ($i=sizeof($messageErrors)-1; $i >= 0; $i--) {
                $error = $messageErrors[$i];
                if ($error['type'] == 'E' || $error['type'] == '') {
                    $message .= $error['msg'];
                }
            }
        } else {
            $message = 'Feedback SAP : Unexpected Error';
        }

        return $message;
    }
}

