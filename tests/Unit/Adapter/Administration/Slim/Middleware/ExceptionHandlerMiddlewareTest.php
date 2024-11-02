<?php

namespace Tests\Unit\Adapter\Administration\Slim\Middleware;

use App\Adapter\Administration\Slim\Exception\HttpNotFoundException;
use App\Adapter\Administration\Slim\Middleware\ExceptionHandleMiddleware;
use Exception;
use Monolog\Logger;

class ExceptionHandlerMiddlewareTest extends MiddlewareTestCase
{
    private Logger $logger;
    private bool $debugEnabled = true;
    private ExceptionHandleMiddleware $exceptionHandlerMiddleware;

    protected function setUp(): void
    {
        parent::setUp();

        $this->logger = $this->createMock(Logger::class);

        $this->exceptionHandlerMiddleware = new ExceptionHandleMiddleware(
            $this->logger,
            $this->debugEnabled
        );
    }

    public function testWithoutException(): void
    {
        $this->handler->method('handle')
            ->willReturn($this->response);

        $response = $this->exceptionHandlerMiddleware->process($this->request, $this->handler);
        $this->assertEquals($this->response, $response);
    }

    public function testWithHttpException(): void
    {
        $message = 'This is a not found exception';
        $exception = new HttpNotFoundException($message);

        $this->handler->method('handle')
            ->willThrowException($exception);

        $response = $this->exceptionHandlerMiddleware->process($this->request, $this->handler);

        $expectedResponse = $this->createJsonResponse(404, [
            'message' => $message
        ]);

        $this->assertResponseEquals($expectedResponse, $response);
    }

    public function testWithOtherExceptionWithDebugEnabled(): void
    {
        $message = 'This is a general exception';
        $exception = new Exception($message);

        $this->handler->method('handle')
            ->willThrowException($exception);

        $response = $this->exceptionHandlerMiddleware->process($this->request, $this->handler);

        $expectedResponse = $this->createJsonResponse(500, [
            'message' => 'Unexpected error occurred',
            'detail' => $exception->getMessage(),
            'trace' => explode("\n", $exception->getTraceAsString())
        ]);

        $this->assertResponseEquals($expectedResponse, $response);
    }

    public function testWithOtherExceptionWithDebugDisabled(): void
    {
        $this->exceptionHandlerMiddleware = new ExceptionHandleMiddleware(
            $this->logger,
            false
        );

        $message = 'This is a general exception';
        $exception = new Exception($message);

        $this->handler->method('handle')
            ->willThrowException($exception);

        $response = $this->exceptionHandlerMiddleware->process($this->request, $this->handler);

        $expectedResponse = $this->createJsonResponse(500, [
            'message' => 'Unexpected error occurred'
        ]);

        $this->assertResponseEquals($expectedResponse, $response);
    }
}
