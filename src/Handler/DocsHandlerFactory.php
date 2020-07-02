<?php

declare(strict_types=1);

namespace Api\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DocsHandlerFactory
{
    public function __invoke(ContainerInterface $container) : RequestHandlerInterface
    {
        return new DocsHandler($container->get(TemplateRendererInterface::class));
    }
}
