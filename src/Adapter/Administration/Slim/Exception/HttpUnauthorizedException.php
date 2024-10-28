<?php

namespace App\Adapter\Administration\Slim\Exception;

use Throwable;

class HttpUnauthorizedException extends HttpException
{
    protected int $statusCode = 401;

    public function __construct(string $message = 'Unauthorized', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $this->statusCode, $code, $previous);
    }
}
