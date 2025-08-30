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

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    'allowed_origins' => [
        'http://localhost:3000',    // React dev server
        'http://localhost:3001',    // Alternative port
        'http://127.0.0.1:3000',
        'http://127.0.0.1:3001',
        'http://localhost:8080',    // Vue/other frameworks
        'http://localhost:8081',
        'http://localhost:3002',    // Next.js additional ports
        'http://localhost:4000',
        '*', // Allow all origins for development (remove in production)
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => [
        'Accept',
        'Authorization',
        'Content-Type',
        'X-Requested-With',
        'Origin',
        'X-CSRF-TOKEN',
        'X-Socket-ID',
    ],

    'exposed_headers' => [
        'Authorization',
    ],

    'max_age' => 86400, // 24 hours

    'supports_credentials' => true,   // Can be true with specific origins

];
