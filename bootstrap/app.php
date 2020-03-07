<?php

require_once __DIR__.'/../vendor/autoload.php';

try {
    Dotenv\Dotenv::create(dirname(__DIR__))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
}

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

$app->withFacades();

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->routeMiddleware([
    'auth' => App\Http\Middlewares\AuthenticateJwt::class,
    'filters' => App\Http\Middlewares\RequestFilters::class,
    'parameter' => App\Http\Middlewares\RequestParameters::class,
    'start' => App\Http\Middlewares\RequestStart::class,
    'validator' => App\Http\Middlewares\RequestValidator::class,
]);

$app->middleware([
    App\Http\Middlewares\Cors::class,
]);

$app->register(App\Providers\AppServiceProvider::class);

require __DIR__.'/list_routes.php';

foreach ($listRoutes as $namespaceRoute => $fileRoute) {
    $config = [
        'namespace' => $namespaceRoute,
        'middleware' => [
            'start',
            'auth',
            'validator',
            'filters',
            'parameter',
        ],
    ];

    $app->router->group($config, function($router) use ($fileRoute) {
        $file = __DIR__."/../routes/{$fileRoute}_routes.php";
        if (file_exists($file)) {
            require $file;
        }
    });
}

$app->router->group(
    [
        'namespace' => 'App\Domains\Auth\Http\Controllers',
        'middleware' => [
            'start',
            'validator',
        ],
    ],
    function ($router) {
        require __DIR__.'/../routes/auth_routes.php';
    }
);

$app->router->group(
    [
        'namespace' => 'App\Domains\Health\Http\Controllers'
    ],
    function ($router) {
        require __DIR__.'/../routes/health_routes.php';
    }
);

$app->configure('app');
$app->configure('tokens');
$app->configure('version');

return $app;
