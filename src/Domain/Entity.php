<?php

namespace App\Domain;

abstract class Entity
{
    public function __construct(protected ?int $id = null)
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    abstract public function serialize(): array;
}
