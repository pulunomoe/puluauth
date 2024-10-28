<?php

namespace Tests\Unit\Adapter\Administration\Slim\Exception;

use PHPUnit\Framework\TestCase;

abstract class HttpExceptionTestCase extends TestCase
{
    protected string $exceptionClass;
    protected string $defaultMessage;
    protected int $defaultStatusCode;

    public function testStatusCodeIsCorrect(): void
    {
        $exception = new $this->exceptionClass();
        $this->assertEquals($this->defaultStatusCode, $exception->getStatusCode());
    }

    public function testWithDefaultMessage(): void
    {
        $exception = new $this->exceptionClass();
        $this->assertEquals($this->defaultMessage, $exception->getMessage());
    }

    public function testWithCustomMessage(): void
    {
        $message = 'Custom Message';
        $exception = new $this->exceptionClass($message);
        $this->assertEquals($message, $exception->getMessage());
    }
}
