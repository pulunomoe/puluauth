<?php

namespace App\Adapter\Administration\Slim;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

abstract readonly class Controller
{
    public function __construct(
        protected ContainerInterface $container
    ) {
    }

    protected function get(string $class): ?object
    {
        try {
            return $this->container->get($class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface) {
            return null;
        }
    }
}
