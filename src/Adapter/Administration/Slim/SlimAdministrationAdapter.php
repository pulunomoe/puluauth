<?php

namespace App\Adapter\Administration\Slim;

use App\Application\Port\Administration\AdministrationPort;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;

readonly class SlimAdministrationAdapter implements AdministrationPort
{
    public function __construct(
        private App $app
    ) {
    }

    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        return $this->app->handle($request);
    }
}
