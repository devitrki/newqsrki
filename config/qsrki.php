<?php

return [
    'api' => [
        'apps' => [
            'url' => env('API_APPS_URL', 'http://appsqa.richeesefactory.com/')
        ],
        'sap' => [
            'url' => env('API_SAP_URL', 'http://10.1.212.34:8030/sap/bc/'),
            'client' => env('API_SAP_CLIENT', '300')
        ],
        'accurate' => [
            'ouath_url' => env('API_ACCURATE_OAUTH_URL', 'https://account.accurate.id/oauth/token'),
        ],
        'aloha' => [
            'url' => env('API_ALOHA', 'http://192.168.6.7:9000/api/'),
            'tax_transaction' => env('API_ALOHA_TAX_TRANSACTION', 'pos/tax'),
            'complete' => env('API_ALOHA_COMPLETE', 'pos/check/complete'),
        ],
        'sap_middleware' => [
            'url' => env('SAP_MIDDLEWARE_API_URL', 'https://pos.richeesefactory.com:3001'),
            'api_timeout' => env('SAP_MIDDLEWARE_API_TIMEOUT', '100'),
        ],
    ],
];
