<?php

namespace Tests;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;

trait Psr7TestTrait
{
    protected static Psr17Factory $psr17Factory;

    public static function setUpPsr7BeforeClass(): void
    {
        self::$psr17Factory = new Psr17Factory();
    }

    protected function createJsonResponse(int $statusCode = 200, ?array $json = null): ResponseInterface
    {
        $stream = self::$psr17Factory->createStream();
        if (is_array($json)) {
            $stream->write(json_encode($json));
            $stream->rewind();
        }
        return self::$psr17Factory->createResponse($statusCode)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($stream);
    }

    protected function assertResponseEquals(ResponseInterface $expected, ResponseInterface $actual): void
    {
        $this->assertEquals($expected->getStatusCode(), $actual->getStatusCode());
        $this->assertEquals($expected->getHeaders(), $actual->getHeaders());
        $this->assertEquals($expected->getBody()->getContents(), $actual->getBody()->getContents());
    }
}
