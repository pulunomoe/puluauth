<?php

namespace Tests\Unit\Adapter\Administration\Slim\Exception;

use App\Adapter\Administration\Slim\Exception\HttpNotFoundException;

class HttpNotFoundExceptionTest extends HttpExceptionTestCase
{
    protected function setUp(): void
    {
        $this->exceptionClass = HttpNotFoundException::class;
        $this->defaultMessage = 'Not Found';
        $this->defaultStatusCode = 404;
    }
}
