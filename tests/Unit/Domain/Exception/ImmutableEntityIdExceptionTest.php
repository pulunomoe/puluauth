<?php

namespace Tests\Unit\Domain\Exception;

use App\Domain\Exception\ImmutableEntityIdException;

class ImmutableEntityIdExceptionTest extends EntityExceptionTestCase
{
    public function testConstructor(): void
    {
        $exception = new ImmutableEntityIdException($this->entity);
        $expectedMessage = 'Entity ID cannot be modified after it is set. Entity: ' . get_class($this->entity);
        $this->assertEquals($expectedMessage, $exception->getMessage());
    }
}
