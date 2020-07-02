<?php

namespace ApiTest\Handler;

use Api\Handler\CityHandler;
use Api\Handler\CityHandlerFactory;

class CityHandlerFactoryTest extends AbstractHandlerTest
{

    /**
     * @covers \ApiTest\Handler\CityHandlerFactory::__invoke
     */
    public function testInvoke()
    {
        $factory = new CityHandlerFactory();
        $obj = $factory($this->container, CityHandler::class);
        $this->assertInstanceOf(CityHandler::class, $obj);
    }
}
