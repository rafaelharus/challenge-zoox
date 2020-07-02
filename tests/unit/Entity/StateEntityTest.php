<?php
namespace ApiTest\Entity;

use Api\Entity\StateEntity;
use DateTime;
use PHPUnit\Framework\TestCase;

class StateEntityTest extends TestCase
{
    private $data = [
        'name' => 'Rio de Janeiro',
        'uf' => 'RJ',
    ];

    /**
     * @covers \Api\Entity\StateEntity::__construct
     */
    public function testConstruct()
    {
        $entity = new StateEntity();
        $entity->exchangeArray($this->data);
        $data = $entity->getArrayCopy();

        $this->assertSame($this->data['name'], $data['name']);
        $this->assertSame($this->data['uf'], $data['uf']);
        $this->assertTrue(
            DateTime::createFromFormat('Y-m-d H:i:s', $data['createdAt']) instanceof DateTime
        );
        $this->assertTrue(
            DateTime::createFromFormat('Y-m-d H:i:s', $data['updatedAt']) instanceof DateTime
        );
    }

    /**
     * @covers \Api\Entity\CityEntity::getInputFilter
     */
    public function testGetInputFilter()
    {
        $entity = new StateEntity();
        $inputFilter = $entity->getInputFilter();
        foreach ([
             'created',
             'updated',
             'name',
             'uf',
         ] as $key) {
            $this->assertTrue($inputFilter->has($key));
        }
    }
}
