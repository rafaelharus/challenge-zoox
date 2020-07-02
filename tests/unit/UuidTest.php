<?php
namespace ApiTest;

use PHPUnit\Framework\TestCase;
use Api\Uuid;

class UuidTest extends TestCase
{
    /**
     * @covers \Api\Uuid::uuid4
     * @covers \Api\Uuid::isValid
     */
    public function testUuidv4()
    {
        $uuid = Uuid::uuid4();
        $this->assertTrue(Uuid::isValid($uuid));
    }
}
