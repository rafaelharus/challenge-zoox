<?php

declare(strict_types=1);

namespace Api\Handler;

use Api\Mapper\StateMapper;
use Mezzio\Hal\HalResponseFactory;
use Mezzio\Hal\ResourceGenerator;
use Mezzio\ProblemDetails\ProblemDetailsResponseFactory;
use Psr\Container\ContainerInterface;

class StateHandlerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new StateHandler(
            $container->get(StateMapper::class),
            $container->get(ResourceGenerator::class),
            $container->get(HalResponseFactory::class),
            $container->get(ProblemDetailsResponseFactory::class)
        );
    }
}
