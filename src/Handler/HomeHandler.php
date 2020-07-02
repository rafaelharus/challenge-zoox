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

class HomeHandler implements RequestHandlerInterface
{
    /** @var StateMapper */
    private $stateMapper;
    /** @var CityMapper */
    private $cityMapper;
    /** @var TemplateRendererInterface */
    private $template;

    public function __construct(
        StateMapper $stateMapper,
        CityMapper $cityMapper,
        TemplateRendererInterface $template
    ) {
        $this->stateMapper = $stateMapper;
        $this->cityMapper = $cityMapper;
        $this->template = $template;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $cities = $this->cityMapper->fetchAllBy();
        $states = $this->stateMapper->fetchAllByCached();

        return new HtmlResponse($this->template->render('app::home-page', [
            'cities' => $cities,
            'states' => $states,
        ]));
    }
}
