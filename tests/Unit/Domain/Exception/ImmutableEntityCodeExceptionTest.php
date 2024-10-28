<?php

namespace Tests\Unit\Domain\Exception;

use App\Domain\Exception\ImmutableEntityCodeException;

class ImmutableEntityCodeExceptionTest extends EntityExceptionTestCase
{
    public function testConstructor(): void
    {
        $exception = new ImmutableEntityCodeException($this->entity);
        $expectedMessage = 'Entity code cannot be modified after it is set. Entity: ' . get_class($this->entity);
        $this->assertEquals($expectedMessage, $exception->getMessage());
    }
}
