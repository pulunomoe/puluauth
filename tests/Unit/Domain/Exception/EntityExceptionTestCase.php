<?php

namespace Tests\Unit\Domain\Exception;

use PHPUnit\Framework\TestCase;
use Tests\Unit\Domain\TestEntity;

abstract class EntityExceptionTestCase extends TestCase
{
    protected TestEntity $entity;

    protected function setUp(): void
    {
        $this->entity = new TestEntity();
    }
}
