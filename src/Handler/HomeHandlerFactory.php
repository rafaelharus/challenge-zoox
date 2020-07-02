<?php

declare(strict_types=1);

namespace Api\Handler;

use Api\Mapper\CityMapper;
use Api\Mapper\StateMapper;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HomeHandlerFactory
{
    public function __invoke(ContainerInterface $container) : RequestHandlerInterface
    {
        return new HomeHandler(
            $container->get(StateMapper::class),
            $container->get(CityMapper::class),
            $container->get(TemplateRendererInterface::class)
        );
    }
}
