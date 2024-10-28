<?php

namespace App\Adapter\Administration\Slim\Middleware;

use App\Adapter\Administration\Slim\Exception\HttpUnauthorizedException;
use App\Application\Exception\AuthenticationException;
use App\Application\Exception\MediatorException;
use App\Application\Mediator\AuthenticationMediator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

readonly class AuthenticationMiddleware implements MiddlewareInterface
{
    public function __construct(
        private AuthenticationMediator $authenticationMediator
    ) {
    }

    /**
     * @throws MediatorException
     * @throws HttpUnauthorizedException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $request = $this->authenticationMediator->authenticate($request);
            return $handler->handle($request);
        } catch (AuthenticationException $e) {
            throw new HttpUnauthorizedException($e->getMessage());
        }
    }
}
