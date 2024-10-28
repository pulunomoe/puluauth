<?php

namespace App\Adapter\Administration\Slim\Exception;

use Throwable;

class HttpNotFoundException extends HttpException
{
    protected int $statusCode = 404;

    public function __construct(string $message = 'Not Found', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $this->statusCode, $code, $previous);
    }
}
