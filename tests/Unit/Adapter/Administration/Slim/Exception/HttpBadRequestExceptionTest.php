<?php

namespace Tests\Unit\Adapter\Administration\Slim\Exception;

use App\Adapter\Administration\Slim\Exception\HttpBadRequestException;

class HttpBadRequestExceptionTest extends HttpExceptionTestCase
{
    protected function setUp(): void
    {
        $this->exceptionClass = HttpBadRequestException::class;
        $this->defaultMessage = 'Bad Request';
        $this->defaultStatusCode = 400;
    }
}
