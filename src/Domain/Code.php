<?php

namespace App\Domain;

abstract readonly class Code
{
    public function __construct(private string $value)
    {
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
