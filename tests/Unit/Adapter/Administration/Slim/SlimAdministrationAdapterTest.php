<?php

namespace Tests\Unit\Adapter\Administration\Slim;

use App\Adapter\Administration\Slim\SlimAdministrationAdapter;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;

class SlimAdministrationAdapterTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testHandleRequest(): void
    {
        $response = $this->createStub(ResponseInterface::class);

        $app = $this->createStub(App::class);
        $app->method('handle')
            ->willReturn($response);

        $slimAdministrationAdapter = new SlimAdministrationAdapter($app);

        $request = $this->createStub(ServerRequestInterface::class);
        $response = $slimAdministrationAdapter->handleRequest($request);
        $this->assertInstanceOf(ResponseInterface::class, $response);

    }
}
