<?php

declare(strict_types=1);

use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;

return static function (Application $app, MiddlewareFactory $factory, ContainerInterface $container) : void {
    $app->route(
        '/',
        Api\Handler\HomeHandler::class,
        ['GET'],
        'home'
    );
    $app->route(
        '/v1/state',
        Api\Handler\StateHandler::class,
        ['GET', 'POST'],
        'api.state.collection'
    );
    $app->route(
        '/v1/state/{id}',
        Api\Handler\StateHandler::class,
        ['GET', 'PATCH', 'DELETE', 'PUT'],
        'api.state.entity'
    );
    $app->route(
        '/v1/city',
        Api\Handler\CityHandler::class,
        ['GET', 'POST'],
        'api.city.collection'
    );
    $app->route(
        '/v1/city/{id}',
        Api\Handler\CityHandler::class,
        ['GET', 'PATCH', 'DELETE', 'PUT'],
        'api.city.entity'
    );
};
