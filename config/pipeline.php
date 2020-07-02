<?php

declare(strict_types=1);

use Api\Middleware\AuthMiddleware;
use Laminas\Stratigility\Middleware\ErrorHandler;
use Laminas\Stratigility\Middleware\OriginalMessages;
use Mezzio\Application;
use Mezzio\Handler\NotFoundHandler;
use Mezzio\Helper\BodyParams\BodyParamsMiddleware;
use Mezzio\Helper\ServerUrlMiddleware;
use Mezzio\Helper\UrlHelperMiddleware;
use Mezzio\MiddlewareFactory;
use Mezzio\ProblemDetails\ProblemDetailsMiddleware;
use Mezzio\ProblemDetails\ProblemDetailsNotFoundHandler;
use Mezzio\Router\Middleware\DispatchMiddleware;
use Mezzio\Router\Middleware\ImplicitHeadMiddleware;
use Mezzio\Router\Middleware\ImplicitOptionsMiddleware;
use Mezzio\Router\Middleware\MethodNotAllowedMiddleware;
use Mezzio\Router\Middleware\RouteMiddleware;
use Psr\Container\ContainerInterface;

/**
 * Setup middleware pipeline:
 */
return function (Application $app, MiddlewareFactory $factory, ContainerInterface $container) : void {
    $app->pipe(OriginalMessages::class);
    $app->pipe(ErrorHandler::class);
    $app->pipe(ServerUrlMiddleware::class);
    $app->pipe(BodyParamsMiddleware::class);
    $app->pipe(ProblemDetailsMiddleware::class);
    $app->pipe(RouteMiddleware::class);
    $app->pipe(AuthMiddleware::class);
    $app->pipe(ImplicitHeadMiddleware::class);
    $app->pipe(ImplicitOptionsMiddleware::class);
    $app->pipe(MethodNotAllowedMiddleware::class);
    $app->pipe(UrlHelperMiddleware::class);
    $app->pipe(DispatchMiddleware::class);
    $app->pipe(ProblemDetailsNotFoundHandler::class);
    $app->pipe(NotFoundHandler::class);
};
