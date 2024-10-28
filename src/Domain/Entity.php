<?php

namespace App\Domain;

use App\Domain\Exception\ImmutableEntityCodeException;
use App\Domain\Exception\ImmutableEntityIdException;

abstract class Entity
{
    public function __construct(
        protected ?int $id = null,
        protected ?Code $code = null
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @throws ImmutableEntityIdException
     */
    public function setId(?int $id): void
    {
        if ($this->id != null) {
            throw new ImmutableEntityIdException($this);
        }

        $this->id = $id;
    }

    abstract public function getCode(): ?Code;

    /**
     * @throws ImmutableEntityCodeException
     */
    abstract public function setCode(string $code): void;

    abstract public function serialize(): array;
}
