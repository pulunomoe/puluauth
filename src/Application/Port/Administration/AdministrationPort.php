<?php

namespace App\Application\Port\Administration;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface AdministrationPort extends RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface;
}
