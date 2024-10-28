<?php

namespace Tests\Unit\Adapter\Administration\Slim\Exception;

use App\Adapter\Administration\Slim\Exception\HttpUnauthorizedException;

class HttpUnauthorizedExceptionTest extends HttpExceptionTestCase
{
    protected function setUp(): void
    {
        $this->exceptionClass = HttpUnauthorizedException::class;
        $this->defaultMessage = 'Unauthorized';
        $this->defaultStatusCode = 401;
    }
}
