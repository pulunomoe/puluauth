<?php

namespace App\Application\Mediator;

use App\Application\Exception\AuthenticationException;
use App\Application\Exception\MediatorException;
use Psr\Http\Message\ServerRequestInterface;

interface AuthenticationMediator
{
    /**
     * @throws MediatorException
     * @throws AuthenticationException
     */
    public function authenticate(ServerRequestInterface $request): ServerRequestInterface;
}
