<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class Psr7TestCase extends TestCase
{
    use Psr7TestTrait;

    protected ServerRequestInterface $request;
    protected ResponseInterface $response;

    public static function setUpBeforeClass(): void
    {
        self::setUpPsr7BeforeClass();
    }
}
