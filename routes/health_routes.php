<?php

$router->get(
    '/health/api',
    [
        'uses' => 'HealthApiController@process',
    ]
);

$router->get(
    '/health/db',
    [
        'uses' => 'HealthDbController@process',
    ]
);
