<?php
namespace ApiTest\Entity;

use Api\Entity\CityEntity;
use DateTime;
use PHPUnit\Framework\TestCase;

class CityEntityTest extends TestCase
{
    private $data = [
        'name' => 'Rio de Janeiro',
        'stateId' => '6f090073-037c-4a2f-8b06-5103530b366a',
    ];

    /**
     * @covers \Api\Entity\CityEntity::__construct
     */
    public function testConstruct()
    {
        $entity = new CityEntity();
        $entity->exchangeArray($this->data);
        $data = $entity->getArrayCopy();

        $this->assertSame($this->data['name'], $data['name']);
        $this->assertSame($this->data['stateId'], $data['stateId']);
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
        $entity = new CityEntity();
        $inputFilter = $entity->getInputFilter();
        foreach ([
             'created',
             'updated',
             'name',
             'stateId',
         ] as $key) {
            $this->assertTrue($inputFilter->has($key));
        }
    }
}
