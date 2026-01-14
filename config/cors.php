<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*',
        'http://localhost:3000',
        'http://localhost:8080', 
        'http://127.0.0.1:3000',
        'http://192.168.1.*',   
        'https://faliyati.app', 
        'capacitor://localhost', 
        'ionic://localhost',     
        'exp://localhost:8081',
         ],

        'allowed_origins_patterns' => [
            'https://*.faliyati.app*',
            'capacitor://*.localhost*',
        ],
    

    'allowed_headers' => ['*'],

    'exposed_headers' => [
        'X-API-TOKEN',
        'X-Total-Count',
    ],

    'max_age' => 86400,  

    'supports_credentials' => true,

];
