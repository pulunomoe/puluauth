<?php

namespace Tests\Unit\Adapter\Administration\Slim;

use App\Adapter\Administration\Slim\Controller;

readonly class TestController extends Controller
{
    public function testGet(string $class): ?object
    {
        return $this->get($class);
    }
}
