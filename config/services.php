<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'domains' => [
        'pro' => 'api.wepark.pro',
        'pre' => '',
        'qa' => '',
        'test' => 'api.glcp-demos.es',
        'local' => 'localhost',
    ],

    'freightVerify' => [
        'url' => env('FREIGHT_VERIFY_URL', 'https://test.api.freightverify.com'),
        'user' => env('FREIGHT_VERIFY_USER'),
        'pass' => env('FREIGHT_VERIFY_PASS')
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'ford_services' => [
        'st8' => [
            'url' => 'http://vacdm.valencia.ford.com:8080',
        ],
        'recirculations' => [
            'url' => '',
        ],
    ],

    "ford_services" => [
        "recirculations" => [
            "wsdl" => env('WSDL_FORD_URL'),
        ]
    ],

];
