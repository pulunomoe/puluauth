<?php

namespace Tests\Unit\Domain;

use App\Domain\Entity;

class TestEntity extends Entity
{
    public function serialize(): array
    {
        return [];
    }
}
