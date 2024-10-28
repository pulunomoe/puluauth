<?php

namespace Tests\Unit\Adapter\Administration\Slim\Middleware;

use PHPUnit\Framework\MockObject\Exception;
use Psr\Http\Server\RequestHandlerInterface;
use Tests\Psr7TestCase;

abstract class MiddlewareTestCase extends Psr7TestCase
{
    protected RequestHandlerInterface $handler;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = $this->createMock(RequestHandlerInterface::class);

        $this->request = self::$psr17Factory->createServerRequest('GET', '/');

        $this->response = self::$psr17Factory->createResponse(200)
            ->withHeader('Content-Type', 'application/json');
        $this->response->getBody()->write(json_encode(['message' => 'Hello, World!']));
    }
}
