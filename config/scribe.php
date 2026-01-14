<?php

return [
    'title' => 'Faliyati API' . ' API Documentation',

    'description' => 'Faliyati API for event management, owners, attendees ',

    'intro_text' => <<<INTRO
        This documentation aims to provide all the information you need to work with **Faliyati API**.

        <aside>
            As you scroll, you'll see code examples for working with the API in different programming languages.
            <br/>
            <strong>Authentication:</strong> Use Bearer tokens from `/api/v1/owner/login` or `/api/v1/owner/register`
        </aside>
    INTRO,

    'base_url' => env('SCRIBE_BASE_URL', config('app.url')),

    'routes' => [
        [
            'match' => [
                'prefixes' => ['api/v1/*'],
                'domains' => ['*'],
            ],
            'include' => [],
            'exclude' => ['scribe*'],
        ],
    ],

    'type' => 'static',

    'theme' => 'default',

    'static' => [
        'output_path' => 'public/docs',
    ],


    'try_it_out' => [
        'enabled' => true,
        'base_url' => null,
        'use_csrf' => false,
        'csrf_url' => '/sanctum/csrf-cookie',
    ],

    'auth' => [
        'enabled' => true,
        'default' => false,
        'in' => 'bearer',                          // ✅ String, not AuthIn class
        'name' => 'Authorization',
        'use_value' => env('SCRIBE_AUTH_KEY'),
        'placeholder' => '{YOUR_ACCESS_TOKEN}',
        'extra_info' => 'Get your token from <code>POST /api/v1/owner/login</code>',
    ],

    'example_languages' => [
        'bash',
        'javascript',
        'php',
    ],

    'postman' => [
        'enabled' => true,
        'overrides' => [
            'info.version' => '1.0.0',
        ],
    ],

    'openapi' => [
        'enabled' => true,
        'version' => '3.0.3',
        'overrides' => [
            'info.title' => config('app.name') . ' API',
            'info.version' => '1.0.0',
        ],
    ],

    'groups' => [
        'default' => 'General',
        'order' => [
            'Owner Authentication',
            'Owner Profile',
            'Events',
            'Books',
            'Attendees',
        ],
    ],

    'logo' => false,

    'last_updated' => 'Last updated: {date:F j, Y}',

    'examples' => [
        'faker_seed' => 1234,
        'models_source' => ['factoryCreate', 'factoryMake', 'databaseFirst'],
    ],

    'strategies' => [
        'metadata' => [],
        'headers' => [],
        'urlParameters' => [],
        'queryParameters' => [],
        'bodyParameters' => [],
        'responses' => [],
        'responseFields' => [],
    ],

    'database_connections_to_transact' => [],

    'fractal' => [
        'serializer' => null,
    ],
];
