<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Entities\SapMiddleware;

use App\Models\Company;

class SapRepositorySapImpl implements SapRepository
{
    public $companyId;
    public $baseUrl;
    public $timeout;
    public $timestamp;

    function __construct($companyId, $fake = null) {
        if ($fake === null) {
            $fake = false;
        }

        $this->companyId = $companyId;
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

    public function uploadGiPlant($payload)
    {
        $path = SapMiddleware::UPLOAD_GI_PO_STO_PATH_URL;
        return $this->doHttp($path, $payload);
    }

    public function mutationAsset($payload)
    {
        $path = SapMiddleware::MUTATION_ASSET_PATH_URL;
        return $this->doHttp($path, $payload);
    }

    public function uploadSales($payload)
    {
        $path = SapMiddleware::UPLOAD_SALES_PATH_URL;
        return $this->doHttp($path, $payload);
    }

    public function getMasterPlant($payload)
    {
        $path = SapMiddleware::MASTER_PLANT_PATH_URL;
        return $this->doHttp($path, $payload);
    }

    public function getMasterMaterial($payload)
    {
        $path = SapMiddleware::MASTER_MATERIAL_PATH_URL;
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

    public function getOutstandingPoPlantReport($payload)
    {
        return [];
    }

    public function getOutstandingGr($payload)
    {
        $path = SapMiddleware::LIST_OUTSTANDING_GR_PATH_URL;
        return $this->doHttp($path, $payload);
    }

    public function getTransactionLog($payload)
    {
        $path = SapMiddleware::LIST_TRANSACTION_LOG_PATH_URL;
        return $this->doHttp($path, $payload);
    }

    public function syncAsset($payload)
    {
        $path = SapMiddleware::MASTER_ASSET_PATH_URL;
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

    public function uploadGiPlantFake($success = null, $statusCode = null)
    {
        if ($success === null) {
            $success = true;
        }

        if ($statusCode === null) {
            $statusCode = 200;
        }

        $fakeResponse = [
            [
                'status' => 'S',
                'message' => 'PO Number 4570001128',
                'success' => $success,
                'document_number' => '100001',
                'document_year' => '2022',
                'stat1' => 'x',
                'stat2' => 'x',
                'stat3' => 'x',
                'po_status' => [
                    'success' => true,
                    'message' => 'Releases Error',
                    'document_number' => '100000001',
                    'document_year' => '',
                    'logs' => []
                ],
                'release_status' => [
                    'success' => true,
                    'message' => 'Releases Error',
                    'document_number' => '100000002',
                    'document_year' => '',
                    'logs' => []
                ],
                'gi_status' => [
                    'success' => true,
                    'message' => 'Releases Error',
                    'document_number' => '100000003',
                    'document_year' => '',
                    'logs' => []
                ],
                'logs' => [
                    [
                        'type' => 'E',
                        'msg' => 'Something Wrong'
                    ]
                ],
            ],
        ];

        return Http::response($fakeResponse, $statusCode);
    }

    public function mutationAssetFake($success = null, $statusCode = null)
    {
        if ($success === null) {
            $success = true;
        }

        if ($statusCode === null) {
            $statusCode = 200;
        }

        $fakeResponse = [
            'success' => $success,
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

    public function syncAssetFake($success = null, $statusCode = null)
    {
        if ($success === null) {
            $success = true;
        }

        if ($statusCode === null) {
            $statusCode = 200;
        }

        $fakeResponse = [
            [
                'plant_id' => 'F103',
                'asset_id' => '6000000150',
                'asset_sub_id' => '00',
                'asset_class_id' => '',
                'asset_type_id' => '',
                'asset_category_id' => '',
                'activation_date' => '',
                'name' => 'Open Table For Soup Warmer',
                'remark' => '',
                'user_spec' => '70 X 75 X 85',
                'cost_center_id' => 'C3111103',
                'cost_center_desc' => '003 RF JATOS CC',
                'qty' => 1.000,
                'uom_id' => 'UN',
                'surface_area_id' => '',
                'vendor_id' => '',
                'vendor_name' => ''
            ],
        ];

        return Http::response($fakeResponse, $statusCode);
    }

    public function getTransactionLogFake($success = null, $statusCode = null)
    {
        if ($success === null) {
            $success = true;
        }

        if ($statusCode === null) {
            $statusCode = 200;
        }

        $fakeResponse = [
            'outlet_id' => '6000004',
            'payments' => [
                'success' => true,
                'errors' => null
            ],
            'sales' => [
                'success' => true,
                'errors' => null
            ],
            'inventories' => [
                'success' => true,
                'errors' => null
            ],
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
            $this->baseUrl . SapMiddleware::UPLOAD_GI_PO_STO_PATH_URL => $this->uploadGiPlantFake(true),
            $this->baseUrl . SapMiddleware::MUTATION_ASSET_PATH_URL => $this->mutationAssetFake(false),
            $this->baseUrl . SapMiddleware::LIST_CURRENT_STOCK_PATH_URL => $this->getCurrentStockPlantFake(false),
            $this->baseUrl . SapMiddleware::LIST_OUTSTANDING_PO_PATH_URL => $this->getOutstandingPoPlantFake(false),
            $this->baseUrl . SapMiddleware::LIST_OUTSTANDING_GR_PATH_URL => $this->getOutstandingGrFake(false),
            $this->baseUrl . SapMiddleware::MASTER_ASSET_PATH_URL => $this->syncAssetFake(false),
            $this->baseUrl . SapMiddleware::LIST_TRANSACTION_LOG_PATH_URL => $this->getTransactionLogFake(true),
        ]);
    }

    private function doHttp($path, $payload, $debug = null){
        if ($debug === null) {
            $debug = false;
        }

        $sapApiKey = Company::getConfigByKey($this->companyId, 'SAP_API_KEY');
        $sapApiSecretKey = Company::getConfigByKey($this->companyId, 'SAP_API_SECRET_KEY');

        $url = $this->baseUrl . $path;
        $payload = json_encode($payload);
        $signature = SapMiddleware::generateSignature($sapApiKey, $sapApiSecretKey, $this->timestamp, $path, $payload);
        $headers = SapMiddleware::getHeaderHttp($sapApiKey, $this->timestamp, $signature);

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

        // !dd([
        //     $res->status(),
        //     $res->json()
        // ]);

        if ($res->ok()) {
            $status = true;
            $response = $res->json();
        } else {
            Log::error("Error call rf middleware", [
                $res->status(),
                $res->json()
            ]);
            $response = json_encode([
                $res->status(),
                $res->json()
            ]);
        }

        return [
            'status' => $status,
            'response' => $response
        ];
    }
}
