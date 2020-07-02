<?php

declare(strict_types=1);

namespace Api\Handler;

use Api\Mapper\CityMapper;
use Api\Mapper\StateMapper;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DocsHandler implements RequestHandlerInterface
{
    /** @var TemplateRendererInterface */
    private $template;

    public function __construct(TemplateRendererInterface $template)
    {
        $this->template = $template;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        return new HtmlResponse($this->template->render('app::docs', []));
    }
}
