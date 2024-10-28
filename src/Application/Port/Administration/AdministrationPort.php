<?php

namespace App\Application\Port\Administration;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface AdministrationPort
{
    public function handleRequest(ServerRequestInterface $request): ResponseInterface;
}
