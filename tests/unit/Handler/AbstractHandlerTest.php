<?php

namespace ApiTest\Handler;

use LosMiddleware\ApiServer\Mapper\MapperInterface;
use PHPUnit\Framework\TestCase;
use Mezzio\Application;
use Mezzio\Helper\UrlHelper;
use Mezzio\Router\RouteResult;
use Laminas\ServiceManager\Config;
use Laminas\ServiceManager\ServiceManager;

class AbstractHandlerTest extends TestCase
{
    /** @var MapperInterface */
    protected $mapper;

    protected $middleware;
    protected $container;
    protected $entity;
    protected $routes = [];
    protected $data = [];

    protected function setUp() : void
    {
        if ($this->container == null) {
            $config = require __DIR__ . '/../../../config/config.php';
            $this->container = new ServiceManager();
            (new Config($config['dependencies']))->configureServiceManager($this->container);
            $this->container->setService('config', $config);
            $factory = $this->container->get(\Mezzio\MiddlewareFactory::class);

            $app = $this->container->get(Application::class);
            (require __DIR__.'/../../../config/routes.php')($app, $factory, $this->container);
            $this->injectRoutes($app->getRoutes());
        }
    }

    protected function injectRoutes($routes)
    {
        foreach ($routes as $route) {
            $name = $route->getName();
            $this->routes[$name] = $route;
        }
    }

    protected function matchRoute($routeName)
    {
        $urlHelper = $this->container->get(UrlHelper::class);
        $urlHelper->setRouteResult(RouteResult::fromRoute($this->routes[$routeName], []));
    }
}
