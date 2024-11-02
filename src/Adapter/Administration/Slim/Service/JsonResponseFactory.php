<?php

namespace App\Adapter\Administration\Slim\Service;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;

class JsonResponseFactory
{
    public static ?Psr17Factory $psr17Factory = null;

    public static function setPsr17Factory(Psr17Factory $psr17Factory): void
    {
        self::$psr17Factory = $psr17Factory;
    }

    private static function getPsr17Factory(): Psr17Factory
    {
        if (self::$psr17Factory == null) {
            self::$psr17Factory = new Psr17Factory();
        }
        return self::$psr17Factory;
    }

    public static function create(int $statusCode, ?array $json): ResponseInterface
    {
        $factory = self::getPsr17Factory();
        $stream = $factory->createStream();
        if (is_array($json)) {
            $stream->write(json_encode($json));
            $stream->rewind();
        }
        return $factory->createResponse($statusCode)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($stream);
    }
}
