<?php

namespace App\Domain\Administrator;

use App\Domain\Entity;
use App\Domain\Exception\ImmutableEntityCodeException;

class Administrator extends Entity
{
    public function __construct(
        ?int $id,
        ?AdministratorCode $code,
        private string $name,
        private string $email
    ) {
        parent::__construct($id, $code);
    }

    public function getCode(): ?AdministratorCode
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        if ($this->code != null) {
            throw new ImmutableEntityCodeException($this);
        }

        $this->code = new AdministratorCode($code);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function serialize(): array
    {
        return [
            'code' => $this->code->getValue(),
            'name' => $this->name,
            'email' => $this->email
        ];
    }
}
