<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Http;

class SapRepositoryAppsImpl implements SapRepository
{
    public function uploadPettyCash($payload)
    {
        $url = config('qsrki.api.sap.url') . 'zfv60post?sap-client=' . config('qsrki.api.sap.client') . '&pgmna=zws_fv60_a';
        $res = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->timeout(100)
                ->post($url, $payload);

        $status = false;
        $response = false;

        if ($res->ok()) {
            $status = true;
            $response = $res->json();
        }

        return [
            'status' => $status,
            'response' => $response
        ];
    }

    public function uploadWaste($payload)
    {
        $url = config('qsrki.api.apps.url') . 'recheese/daily-sales/sap/waste/submit';
        $res = Http::asForm()
                ->timeout(100)
                ->post($url, $payload);

        $status = false;
        $response = false;

        if ($res->ok()) {
            $status = true;
            $response = $res->json();
        }

        return [
            'status' => $status,
            'response' => $response
        ];
    }

    public function uploadOpname($payload)
    {
        $url = config('qsrki.api.apps.url') . 'recheese/daily-sales/sap/opname/submit';
        $res = Http::asForm()
                ->timeout(100)
                ->post($url, $payload);

        $status = false;
        $response = false;

        if ($res->ok()) {
            $status = true;
            $response = $res->json();
        }

        return [
            'status' => $status,
            'response' => $response
        ];
    }

    public function uploadGrVendor($payload)
    {
        $url = config('qsrki.api.apps.url') . 'recheese/daily-sales/sap/grvendor/upload';
        $res = Http::asForm()
                ->timeout(100)
                ->post($url, $payload);

        $status = false;
        $response = false;

        if ($res->ok()) {
            $status = true;
            $response = $res->json();
        }

        return [
            'status' => $status,
            'response' => $response
        ];
    }

    public function uploadGrPlant($payload)
    {
        $url = config('qsrki.api.apps.url') . 'recheese/daily-sales/sap/gr/upload';
        $res = Http::asForm()
                ->timeout(100)
                ->post($url, $payload);

        $status = false;
        $response = false;

        if ($res->ok()) {
            $status = true;
            $response = $res->json();
        }

        return [
            'status' => $status,
            'response' => $response
        ];
    }

    public function uploadGiPlant($payload)
    {
        $url = config('qsrki.api.sap.url') . 'zpostpo?sap-client=' . config('qsrki.api.sap.client') . '&pgmna=zmmposto&p_type=t';
        $res = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->timeout(100)
                ->post($url, $payload);

        $status = false;
        $response = false;

        if ($res->ok()) {
            $status = true;
            $response = $res->json();
        }

        return [
            'status' => $status,
            'response' => $response
        ];
    }

    public function mutationAsset($param)
    {
        $url = config('qsrki.api.sap.url') . 'zserv';
        $res = Http::timeout(100)
                ->get($url, $param);

        $status = false;
        $response = false;

        if ($res->ok()) {
            $status = true;
            $response = $res->json();
        }

        return [
            'status' => $status,
            'response' => $response
        ];
    }

    public function getMasterPlant($param)
    {
        $url = config('qsrki.api.apps.url') . 'recheese/daily-sales/sap/plant';

        $res = Http::timeout(100)
                ->get($url);

        $status = false;
        $response = false;

        if ($res->ok()) {
            $status = true;
            $response = $res->json();
        }

        return [
            'status' => $status,
            'response' => $response
        ];
    }

    public function getMasterMaterial($param)
    {
        $url = config('qsrki.api.apps.url') . 'recheese/daily-sales/sap/material';

        $res = Http::timeout(100)
                ->get($url, $param);

        $status = false;
        $response = false;

        if ($res->ok()) {
            $status = true;
            $response = $res->json();
        }

        return [
            'status' => $status,
            'response' => $response
        ];
    }

    public function getCurrentStockPlant($param)
    {
        $url = config('qsrki.api.apps.url') . 'recheese/daily-sales/sap/stock';

        $res = Http::timeout(100)
                ->get($url, $param);

        $status = false;
        $response = false;

        if ($res->ok()) {
            $status = true;
            $response = $res->json();
        }

        return [
            'status' => $status,
            'response' => $response
        ];
    }

    public function getOutstandingPoVendor($param)
    {
        $url = config('qsrki.api.apps.url') . 'recheese/daily-sales/sap/grvendor/outstanding';

        $res = Http::timeout(100)
                ->get($url, $param);

        $status = false;
        $response = false;

        if ($res->ok()) {
            $status = true;
            $response = $res->json();
        }

        return [
            'status' => $status,
            'response' => $response
        ];
    }

    public function getOutstandingPoPlant($param)
    {
        $url = config('qsrki.api.apps.url') . 'recheese/daily-sales/sap/gr/outstanding';

        $res = Http::timeout(100)
                ->get($url, $param);

        $status = false;
        $response = false;

        if ($res->ok()) {
            $status = true;
            $response = $res->json();
        }

        return [
            'status' => $status,
            'response' => $response
        ];
    }

    public function getOutstandingPoPlantReport($param)
    {
        $url = config('qsrki.api.apps.url') . 'recheese/daily-sales/sap/posto/outstanding';

        $res = Http::timeout(100)
                ->get($url, $param);

        $status = false;
        $response = false;

        if ($res->ok()) {
            $status = true;
            $response = $res->json();
        }

        return [
            'status' => $status,
            'response' => $response
        ];
    }

    public function getOutstandingGr($param)
    {
        $url = config('qsrki.api.apps.url') . 'recheese/daily-sales/sap/gr/outstanding/detail';

        $res = Http::timeout(100)
                ->get($url, $param);

        $status = false;
        $response = false;

        if ($res->ok()) {
            $status = true;
            $response = $res->json();
        }

        return [
            'status' => $status,
            'response' => $response
        ];
    }

    public function syncAsset($param)
    {
        $url = config('qsrki.api.apps.url') . 'recheese/daily-sales/sap/asset/list';

        $res = Http::timeout(100)
                ->get($url, $param);

        $status = false;
        $response = false;

        if ($res->ok()) {
            $status = true;
            $response = $res->json();
        }

        return [
            'status' => $status,
            'response' => $response
        ];
    }

}
