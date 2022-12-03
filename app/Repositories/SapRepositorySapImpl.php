<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Http;

use App\Entities\SapMiddleware;

class SapRepositorySapImpl implements SapRepository
{
    public $baseUrl;
    public $timeout;
    public $timestamp;

    function __construct($fake = null) {
        if ($fake === null) {
            $fake = false;
        }

        $this->baseUrl = config('qsrki.api.sap_middleware.url');
        $this->timeout = config('qsrki.api.sap_middleware.api_timeout');
        $this->timestamp = time();

        if ($fake) {
            $this->setupHttpFake();
        }
    }

    public function uploadPettyCash($payload)
    {
        $path = SapMiddleware::UPLOAD_PETTYCASH_PATH_URL;
        return $this->doHttp($path, $payload);
    }

    public function uploadWaste($payload)
    {
        $path = SapMiddleware::UPLOAD_WASTE_PATH_URL;
        return $this->doHttp($path, $payload);
    }

    public function uploadOpname($payload)
    {
        $path = SapMiddleware::UPLOAD_STOCK_OPNAME_PATH_URL;
        return $this->doHttp($path, $payload);
    }

    public function uploadGrVendor($payload)
    {
        $path = SapMiddleware::UPLOAD_GR_PO_VENDOR_PATH_URL;
        return $this->doHttp($path, $payload);
    }

    public function uploadGrPlant($payload)
    {
        $path = SapMiddleware::UPLOAD_GR_PO_STO_PATH_URL;
        return $this->doHttp($path, $payload);
    }

    public function getCurrentStockPlant($payload)
    {
        $path = SapMiddleware::LIST_CURRENT_STOCK_PATH_URL;
        return $this->doHttp($path, $payload);
    }

    public function getOutstandingPoVendor($payload)
    {
        $path = SapMiddleware::LIST_OUTSTANDING_PO_PATH_URL;
        return $this->doHttp($path, $payload);
    }

    public function getOutstandingPoPlant($payload)
    {
        $path = SapMiddleware::LIST_OUTSTANDING_PO_PATH_URL;
        return $this->doHttp($path, $payload);
    }

    public function getOutstandingGr($payload)
    {
        $path = SapMiddleware::LIST_OUTSTANDING_GR_PATH_URL;
        return $this->doHttp($path, $payload);
    }

    // fake response
    public function uploadPettyCashFake($success = null, $statusCode = null)
    {
        if ($success === null) {
            $success = true;
        }

        if ($statusCode === null) {
            $statusCode = 200;
        }

        $fakeResponse = [
            'success' => $success,
            'document_number' => '100001',
            'logs' => [
                [
                    'type' => 'E',
                    'msg' => 'Something Wrong'
                ]
            ]
        ];

        return Http::response($fakeResponse, $statusCode);
    }

    public function uploadWasteFake($success = null, $statusCode = null)
    {
        if ($success === null) {
            $success = true;
        }

        if ($statusCode === null) {
            $statusCode = 200;
        }

        $fakeResponse = [
            'success' => $success,
            'document_number' => '100001',
            'document_year' => '2022',
            'logs' => [
                [
                    'type' => 'E',
                    'msg' => 'Something Wrong'
                ]
            ]
        ];

        return Http::response($fakeResponse, $statusCode);
    }

    public function uploadOpnameFake($success = null, $statusCode = null)
    {
        if ($success === null) {
            $success = true;
        }

        if ($statusCode === null) {
            $statusCode = 200;
        }

        $fakeResponse = [
            'success' => $success,
            'document_number' => '100001',
            'document_year' => '2022',
            'logs' => [
                [
                    'type' => 'E',
                    'msg' => 'Something Wrong'
                ]
            ]
        ];

        return Http::response($fakeResponse, $statusCode);
    }

    public function uploadGrVendorFake($success = null, $statusCode = null)
    {
        if ($success === null) {
            $success = true;
        }

        if ($statusCode === null) {
            $statusCode = 200;
        }

        $fakeResponse = [
            'success' => $success,
            'document_number' => '100001',
            'document_year' => '2022',
            'logs' => [
                [
                    'type' => 'E',
                    'msg' => 'Something Wrong'
                ]
            ]
        ];

        return Http::response($fakeResponse, $statusCode);
    }

    public function uploadGrPlantFake($success = null, $statusCode = null)
    {
        if ($success === null) {
            $success = true;
        }

        if ($statusCode === null) {
            $statusCode = 200;
        }

        $fakeResponse = [
            'success' => $success,
            'document_number' => '100001',
            'document_year' => '2022',
            'logs' => [
                [
                    'type' => 'E',
                    'msg' => 'Something Wrong'
                ]
            ]
        ];

        return Http::response($fakeResponse, $statusCode);
    }

    public function getCurrentStockPlantFake($success = null, $statusCode = null)
    {
        if ($success === null) {
            $success = true;
        }

        if ($statusCode === null) {
            $statusCode = 200;
        }

        $fakeResponse = [
            [
                'material_id' => '100001',
                'material_name' => 'POTATO WEDGES',
                'sloc_id' => 'S001',
                'plant_id' => 'R101',
                'material_type_id' => 'FERT',
                'qty' => 1,
                'uom_id' => 'KG'
            ],
            [
                'material_id' => '1000018',
                'material_name' => 'FRYING OIL RF/CK',
                'sloc_id' => 'S001',
                'plant_id' => 'R101',
                'material_type_id' => 'FERT',
                'qty' => 66.66,
                'uom_id' => 'KG'
            ]
        ];

        return Http::response($fakeResponse, $statusCode);
    }

    public function getOutstandingPoPlantFake($success = null, $statusCode = null)
    {
        if ($success === null) {
            $success = true;
        }

        if ($statusCode === null) {
            $statusCode = 200;
        }

        $fakeResponse = [
            [
                'po_number' => '1000034432',
                'gi_number' => '4814906847',
                'po_item' => '1',
                'vendor_id' => '',
                'vendor_name' => '',
                'material_id' => '',
                'material_name' => '',
                'supplying_plant_id' => 'R101',
                'receiving_plant_id' => 'F109',
                'delivery_date' => '2022-11-14',
                'posting_date' => '2022-11-14',
                'schedule_qty' => 0.000,
                'gr_qty' => 0.000,
                'uom_id' => '',
                'is_delivery_complete' => false,
            ]
        ];

        return Http::response($fakeResponse, $statusCode);
    }

    public function getOutstandingGrFake($success = null, $statusCode = null)
    {
        if ($success === null) {
            $success = true;
        }

        if ($statusCode === null) {
            $statusCode = 200;
        }

        $fakeResponse = [
            [
                'material_doc_id' => '',
                'material_doc_item' => '',
                'material_doc_ref' => '',
                'posting_date' => '2022-11-14',
                'document_date' => '2022-11-14',
                'external_material_id' => '',
                'header_text' => '',
                'movement_type_id' => '',
                'sloc_id' => '',
                'batch_number' => '',
                'name' => '',
                'receiving_plant_id' => 'F109',
                'receiving_sloc_id' => 'S001',
                'receiving_batch_number' => '',
                'material_id' => '1000018',
                'material_name' => 'FRYING OIL RF/CK',
                'qty' => 0,
                'uom_id' => 'KG',
                'entry_qty' => 72,
                'receive_qty' => 0.000,
                'confirm_qty' => 1,
                'entry_uom_id' => 1,
                'base_oum_id' => '',
                'ship_to' => '',
                'item_text' => '',
                'is_check_data' => '',
                'transaction_key' => '',
                'po_number' => '4570720259',
                'po_item' => '1',
            ],
            [
                'material_doc_id' => '',
                'material_doc_item' => '',
                'material_doc_ref' => '',
                'posting_date' => '2022-11-14',
                'document_date' => '2022-11-14',
                'external_material_id' => '',
                'header_text' => '',
                'movement_type_id' => '',
                'sloc_id' => '',
                'batch_number' => '',
                'name' => '',
                'receiving_plant_id' => 'F109',
                'receiving_sloc_id' => 'S001',
                'receiving_batch_number' => '',
                'material_id' => '2000169',
                'material_name' => 'SATCHEL BAG - RC',
                'qty' => 0,
                'uom_id' => 'PC',
                'entry_qty' => 200,
                'receive_qty' => 0.000,
                'confirm_qty' => 1,
                'entry_uom_id' => 1,
                'base_oum_id' => '',
                'ship_to' => '',
                'item_text' => '',
                'is_check_data' => '',
                'transaction_key' => '',
                'po_number' => '4570720259',
                'po_item' => '30',
            ]
        ];

        return Http::response($fakeResponse, $statusCode);
    }

    // utility
    private function setupHttpFake(){
        Http::fake([
            $this->baseUrl . SapMiddleware::UPLOAD_PETTYCASH_PATH_URL => $this->uploadPettyCashFake(false),
            $this->baseUrl . SapMiddleware::UPLOAD_WASTE_PATH_URL => $this->uploadPettyCashFake(false),
            $this->baseUrl . SapMiddleware::UPLOAD_STOCK_OPNAME_PATH_URL => $this->uploadOpnameFake(false),
            $this->baseUrl . SapMiddleware::UPLOAD_GR_PO_VENDOR_PATH_URL => $this->uploadGrVendorFake(true),
            $this->baseUrl . SapMiddleware::UPLOAD_GR_PO_STO_PATH_URL => $this->uploadGrPlantFake(true),
            $this->baseUrl . SapMiddleware::LIST_CURRENT_STOCK_PATH_URL => $this->getCurrentStockPlantFake(false),
            $this->baseUrl . SapMiddleware::LIST_OUTSTANDING_PO_PATH_URL => $this->getOutstandingPoPlantFake(false),
            $this->baseUrl . SapMiddleware::LIST_OUTSTANDING_GR_PATH_URL => $this->getOutstandingGrFake(false),
        ]);
    }

    private function doHttp($path, $payload, $debug = null){
        if ($debug === null) {
            $debug = false;
        }

        $url = $this->baseUrl . $path;
        $payload = json_encode($payload);
        $signature = SapMiddleware::generateSignature($this->timestamp, $path, $payload);
        $headers = SapMiddleware::getHeaderHttp($this->timestamp, $signature);

        if ($debug) {
            $res = Http::dd()
                    ->withHeaders($headers)
                    ->acceptJson()
                    ->timeout($this->timeout)
                    ->withBody($payload, 'application/json')
                    ->post($url);
        } else {
            $res = Http::withHeaders($headers)
                    ->acceptJson()
                    ->timeout($this->timeout)
                    ->withBody($payload, 'application/json')
                    ->post($url);
        }

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
