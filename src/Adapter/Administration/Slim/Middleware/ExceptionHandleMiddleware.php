<?php

namespace App\Adapter\Administration\Slim\Middleware;

use App\Adapter\Administration\Slim\Exception\HttpException;
use App\Adapter\Administration\Slim\Service\JsonResponseFactory;
use Exception;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

readonly class ExceptionHandleMiddleware implements MiddlewareInterface
{
    public function __construct(
        private Logger $logger,
        private bool $debugEnabled
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {

            return $handler->handle($request);

        } catch (Exception $e) {

            if ($e instanceof HttpException) {
                $statusCode = $e->getStatusCode();
                $json = ['message' => $e->getMessage()];
            } else {
                $statusCode = 500;
                $json = ['message' => 'Unexpected error occurred'];

                if ($this->debugEnabled) {
                    $json['detail'] = $e->getMessage();
                    $json['trace'] = explode("\n", $e->getTraceAsString());
                }
            }

            $this->logger->error($e->getMessage(), $this->debugEnabled ? $e->getTrace() : [$e->getFile() . ':'. $e->getLine()]);

            return JsonResponseFactory::create($statusCode, $json);

        }
    }
}
