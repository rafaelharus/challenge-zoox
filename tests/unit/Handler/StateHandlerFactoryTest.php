<?php

namespace ApiTest\Handler;

use Api\Handler\StateHandler;
use Api\Handler\StateHandlerFactory;

class StateHandlerFactoryTest extends AbstractHandlerTest
{

    /**
     * @covers \ApiTest\Handler\CityHandlerFactory::__invoke
     */
    public function testInvoke()
    {
        $factory = new StateHandlerFactory();
        $obj = $factory($this->container, StateHandler::class);
        $this->assertInstanceOf(StateHandler::class, $obj);
    }
}
