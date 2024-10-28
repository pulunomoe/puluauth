<?php

namespace Tests\Unit\Adapter\Administration\Slim\Service;

use App\Adapter\Administration\Slim\Service\JsonResponseFactory;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\MockObject\Exception;
use Tests\Psr7TestCase;

class JsonResponseFactoryTest extends Psr7TestCase
{
    protected function setUp(): void
    {
        JsonResponseFactory::setPsr17Factory(new Psr17Factory());
    }

    /**
     * @throws Exception
     */
    public function testSetPsr17Factory()
    {
        $psr17Factory = $this->createStub(Psr17Factory::class);
        JsonResponseFactory::setPsr17Factory($psr17Factory);
        $this->assertEquals($psr17Factory, JsonResponseFactory::$psr17Factory);
    }

    public function testWithArrayBody(): void
    {
        $response = JsonResponseFactory::create(200, [
            'message' => 'Hello, World!'
        ]);

        $expectedResponse = $this->createJsonResponse(200, [
            'message' => 'Hello, World!'
        ]);

        $this->assertResponseEquals($expectedResponse, $response);
    }

    public function testWithEmptyArrayBody(): void
    {
        $response = JsonResponseFactory::create(200, []);

        $expectedResponse = $this->createJsonResponse(200, []);

        $this->assertResponseEquals($expectedResponse, $response);
    }

    public function testWithNullBody(): void
    {
        $response = JsonResponseFactory::create(200, null);

        $expectedResponse = $this->createJsonResponse();

        $this->assertResponseEquals($expectedResponse, $response);
    }
}
