<?php

namespace App\Domain\Exception;

use App\Domain\Entity;
use Exception;

class ImmutableEntityCodeException extends Exception
{
    public function __construct(Entity $entity)
    {
        parent::__construct(message: 'Entity code cannot be modified after it is set. Entity: ' . get_class($entity));
    }
}
