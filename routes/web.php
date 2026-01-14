<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

/*
|-------------------------------------------------------------------------
| 🎯 Scribe Documentation Routes (Working!)
|-------------------------------------------------------------------------
*/

Route::get('/docs/{path?}', function ($path = '') {
    $htmlPath = public_path("docs/index.html");
    if (file_exists($htmlPath)) {
        return response()->file($htmlPath);
    }
    
    $scribeDir = storage_path('app/scribe');
    if (is_dir($scribeDir)) {
        $files = glob($scribeDir . '/*.html') ?: glob($scribeDir . '/*.json');
        if ($files) {
            $firstFile = $files[0];
            $content = file_get_contents($firstFile);
            $mime = str_contains($firstFile, '.json') ? 'application/json' : 'text/html';
            return response($content)->header('Content-Type', $mime);
        }
    }
    
    return response('
        <html>
        <body style="font-family:Arial;padding:40px;">
            <h1>🚀 Faliyati API Documentation</h1>
            <p><strong>✅ Server Active</strong> - 27 endpoints documented</p>
            <ul>
                <li><a href="/docs.postman">📥 Postman Collection</a></li>
                <li><a href="/docs.openapi">📋 OpenAPI Spec</a></li>
                <li><a href="/api/v1/owner/login">🔑 Owner Login</a></li>
            </ul>
            <p>Run <code>php artisan scribe:generate</code> to generate HTML docs.</p>
        </body>
    </html>
    ')->header('Content-Type', 'text/html');
})->where('path', '.*');

Route::get('/docs.postman', function () {
    $paths = [
        storage_path('app/scribe/collection.json'),
        storage_path('app/private/scribe/collection.json'),
        public_path('docs/collection.json'),
    ];

    foreach ($paths as $p) {
        if (file_exists($p)) {
            $content = file_get_contents($p);
            return response($content, 200)
                ->header('Content-Type', 'application/json')
                ->header('Content-Disposition', 'attachment; filename="faliyati.postman_collection.json"');
        }
    }

    return response()->json(['error' => 'Postman collection not found. Run: php artisan scribe:generate'], 404);
});

Route::get('/docs.openapi', function () {
    $paths = [
        storage_path('app/scribe/openapi.yaml'),
        storage_path('app/private/scribe/openapi.yaml'),
        public_path('docs/openapi.yaml'),
    ];

    foreach ($paths as $p) {
        if (file_exists($p)) {
            return response()->file($p)
                ->header('Content-Type', 'application/yaml');
        }
    }

    return response()->json(['error' => 'OpenAPI spec not found. Run: php artisan scribe:generate'], 404);
});
