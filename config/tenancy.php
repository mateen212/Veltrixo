<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Domain
    |--------------------------------------------------------------------------
    | The base domain used to generate tenant subdomains.
    | In production: veltrixo.com  → alnoor.veltrixo.com
    | In development: veltrixo.test → alnoor.veltrixo.test
    */
    'app_domain' => env('APP_DOMAIN', 'veltrixo.com'),

    /*
    |--------------------------------------------------------------------------
    | Central Domain
    |--------------------------------------------------------------------------
    | The domain reserved for the Super Admin / platform management.
    | Tenant subdomains are BLOCKED from accessing routes behind this domain.
    */
    'central_domain' => env('CENTRAL_DOMAIN', 'veltrixo.com'),

    /*
    |--------------------------------------------------------------------------
    | Exempted Subdomains
    |--------------------------------------------------------------------------
    | Subdomains that are NOT tenant identifiers (reserved for platform use).
    */
    'exempt_subdomains' => ['www', 'admin', 'api', 'mail', 'ftp', 'static', 'cdn'],
];
