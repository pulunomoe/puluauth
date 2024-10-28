<?php

namespace App\Adapter\Administration\Slim\Service;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;

class JsonResponseFactory
{
    public static Psr17Factory $psr17Factory;

    public static function setPsr17Factory(Psr17Factory $psr17Factory): void
    {
        self::$psr17Factory = $psr17Factory;
    }

    public static function create(int $statusCode, ?array $json): ResponseInterface
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
}
