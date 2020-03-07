<?php

return [
    'cipher' => 'AES-256-CBC',
    'env' => env('APP_ENV', 'production'),
    'fallback_locale' => 'en',
    'jwt_app_secret' => env('JWT_APP_SECRET'),
    'key' => env('APP_KEY'),
    'locale' => 'en',
    'name' => 'Automatic Lumen API',
    'timezone' => 'UTC',
];
