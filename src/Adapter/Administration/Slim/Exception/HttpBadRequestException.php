<?php

namespace App\Adapter\Administration\Slim\Exception;

use Throwable;

class HttpBadRequestException extends HttpException
{
    protected int $statusCode = 400;

    public function __construct(string $message = 'Bad Request', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $this->statusCode, $code, $previous);
    }
}
