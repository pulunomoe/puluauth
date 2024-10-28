<?php

namespace Tests\Unit\Domain;

use App\Domain\Exception\ImmutableEntityIdException;
use PHPUnit\Framework\TestCase;

class EntityTest extends TestCase
{
    /**
     * @throws ImmutableEntityIdException
     */
    public function testConstructor(): void
    {
        $entity = new TestEntity();
        $this->assertNull($entity->getId());

        $id = 123;
        $entity->setId($id);
        $this->assertEquals($id, $entity->getId());

        $entity = new TestEntity(123);
        $this->assertEquals(123, $entity->getId());
    }

    public function testImmutableId(): void
    {
        $entity = new TestEntity(123);

        $this->expectException(ImmutableEntityIdException::class);
        $entity->setId(456);
    }
}
