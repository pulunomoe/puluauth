<?php

namespace Tests\Unit\Domain;

use PHPUnit\Framework\TestCase;

class EntityTest extends TestCase
{
    public function testGetterAndSetter(): void
    {
        $entity = new TestEntity();
        $this->assertNull($entity->getId());

        $id = 123;
        $entity->setId($id);
        $this->assertEquals($id, $entity->getId());
    }
}
