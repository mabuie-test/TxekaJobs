<?php

return [
    'name' => env('APP_NAME', 'Txeka Jobs'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => 'UTC',
    'locale' => 'pt',
    'fallback_locale' => 'en',
    'faker_locale' => 'pt_PT',
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',
];
