<?php

return [
    'driver' => env('PAYMENT_GATEWAY_DRIVER', 'mock'),

    'mpesa' => [
        'environment' => env('MPESA_ENV', 'sandbox'),
        'api_key' => env('MPESA_API_KEY'),
        'public_key' => env('MPESA_PUBLIC_KEY'),
        'service_provider_code' => env('MPESA_SERVICE_PROVIDER_CODE'),
        'initiator_identifier' => env('MPESA_INITIATOR_IDENTIFIER'),
        'security_credential' => env('MPESA_SECURITY_CREDENTIAL'),
        'default_short_code' => env('MPESA_DEFAULT_SHORTCODE', env('MPESA_SERVICE_PROVIDER_CODE')),
        'callback_url' => env('MPESA_CALLBACK_URL'),
    ],
];
