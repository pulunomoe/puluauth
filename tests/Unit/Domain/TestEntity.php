<?php

namespace Tests\Unit\Domain;

use App\Domain\Code;
use App\Domain\Entity;

class TestEntity extends Entity
{
    public function serialize(): array
    {
        return [];
    }

    public function getCode(): ?Code
    {
        return null;
    }

    public function setCode(string $code): void
    {
    }
}
